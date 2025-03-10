<?php

namespace Srapid\RealEstate\Services;

use Srapid\RealEstate\Models\Project;
use Srapid\RealEstate\Services\Abstracts\StoreProjectCategoryServiceAbstract;
use Illuminate\Http\Request;

class StoreProjectCategoryService extends StoreProjectCategoryServiceAbstract
{

    /**
     * @param Request $request
     * @param Project $project
     * @return mixed|void
     */
    public function execute(Request $request, Project $project)
    {
        $categories = $request->input('categories', []);
        if (is_array($categories)) {
            if ($categories) {
                $project->categories()->sync($categories);
            } else {
                $project->categories()->detach();
            }
        }
    }
}
