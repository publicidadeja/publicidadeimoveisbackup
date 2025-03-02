<?php

namespace Srapid\RealEstate\Http\Controllers;

use Illuminate\Http\Request;
use Srapid\Base\Events\BeforeEditContentEvent;
use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Base\Events\UpdatedContentEvent;
use Srapid\Base\Forms\FormBuilder;
use Srapid\Base\Http\Controllers\BaseController;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Forms\CrmForm;
use Srapid\RealEstate\Http\Requests\CrmRequest;
use Srapid\RealEstate\Repositories\Interfaces\CrmInterface;
use Srapid\RealEstate\Tables\CrmTable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class CrmController extends BaseController
{
    /**
     * @var CrmInterface
     */
    protected $crmRepository;

    /**
     * CrmController constructor.
     * @param CrmInterface $crmRepository
     */
    public function __construct(CrmInterface $crmRepository)
    {
        $this->crmRepository = $crmRepository;
    }

    /**
     * @param CrmTable $table
     * @return JsonResponse|View
     * @throws Throwable
     */
    public function index(CrmTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::crm.name'));

        // Get lead counts by status for the dashboard
        $leadsByStatus = $this->crmRepository->getModel()
            ->select(['lead_color', \DB::raw('count(*) as total')])
            ->groupBy('lead_color')
            ->get()
            ->pluck('total', 'lead_color')
            ->toArray();
            
        // Get default values for any missing status
        $leadStatuses = [
            'red' => $leadsByStatus['red'] ?? 0,      // Hot leads
            'blue' => $leadsByStatus['blue'] ?? 0,    // Cold leads
            'yellow' => $leadsByStatus['yellow'] ?? 0, // In negotiation
            'gray' => $leadsByStatus['gray'] ?? 0,     // Lost sales
        ];
        
        return view('plugins/real-estate::crm.dashboard', compact('table', 'leadStatuses'));
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::crm.create'));

        return $formBuilder->create(CrmForm::class)->renderForm();
    }

    /**
     * @param CrmRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CrmRequest $request, BaseHttpResponse $response)
    {
        $crm = $this->crmRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

        return $response
            ->setPreviousUrl(route('crm.index'))
            ->setNextUrl(route('crm.edit', $crm->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $crm = $this->crmRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $crm));

        page_title()->setTitle(trans('plugins/real-estate::crm.edit') . ' "' . $crm->name . '"');

        return $formBuilder->create(CrmForm::class, ['model' => $crm])->renderForm();
    }

    /**
     * @param int $id
     * @param CrmRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CrmRequest $request, BaseHttpResponse $response)
    {
        $crm = $this->crmRepository->findOrFail($id);

        $crm->fill($request->input());

        $this->crmRepository->createOrUpdate($crm);

        event(new UpdatedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

        return $response
            ->setPreviousUrl(route('crm.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $crm = $this->crmRepository->findOrFail($id);

            $this->crmRepository->delete($crm);

            event(new DeletedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $crm = $this->crmRepository->findOrFail($id);
            $this->crmRepository->delete($crm);
            event(new DeletedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
    
    /**
     * Get Kanban board data
     * 
     * @return JsonResponse
     */
    public function getKanbanData(Request $request)
    {
        try {
            $leads = $this->crmRepository->getModel()
                ->select([
                    're_crm.id',
                    're_crm.name',
                    're_crm.phone',
                    're_crm.email',
                    're_crm.content',
                    're_crm.property_value',
                    're_crm.category',     
                    're_crm.lead_color',   
                ])
                ->get();
                
            $leadsByStatus = [
                'red' => [],
                'blue' => [],
                'yellow' => [],
                'gray' => []
            ];
            
            $categoryMap = CrmTable::CATEGORIES;
            
            foreach ($leads as $lead) {
                $lead->category_text = $categoryMap[$lead->category] ?? $lead->category;
                $lead->formatted_property_value = $lead->property_value ? 'R$ ' . number_format($lead->property_value, 2, ',', '.') : null;
                $lead->edit_url = route('crm.edit', $lead->id);
                
                $leadsByStatus[$lead->lead_color ?? 'blue'][] = $lead;
            }
            
            return response()->json($leadsByStatus);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update lead status via AJAX
     * 
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function updateStatus(Request $request, BaseHttpResponse $response)
    {
        try {
            $id = $request->input('id');
            $newStatus = $request->input('lead_color');
            
            $lead = $this->crmRepository->findOrFail($id);
            $lead->lead_color = $newStatus;
            $this->crmRepository->createOrUpdate($lead);
            
            return $response->setMessage('Status atualizado com sucesso');
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}