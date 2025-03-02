@extends('core/base::layouts.master')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-stat-container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="dashboard-stat red">
                            <div class="visual">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span>{{ $leadStatuses['red'] }}</span>
                                </div>
                                <div class="desc">Leads Quentes</div>
                            </div>
                            <a class="more" href="{{ route('crm.index') }}?lead_color=red">
                                Ver todos <i class="fas fa-arrow-alt-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-stat blue">
                            <div class="visual">
                                <i class="fas fa-snowflake"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span>{{ $leadStatuses['blue'] }}</span>
                                </div>
                                <div class="desc">Leads Frios</div>
                            </div>
                            <a class="more" href="{{ route('crm.index') }}?lead_color=blue">
                                Ver todos <i class="fas fa-arrow-alt-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-stat yellow">
                            <div class="visual">
                                <i class="fas fa-comments-dollar"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span>{{ $leadStatuses['yellow'] }}</span>
                                </div>
                                <div class="desc">Em Negociação</div>
                            </div>
                            <a class="more" href="{{ route('crm.index') }}?lead_color=yellow">
                                Ver todos <i class="fas fa-arrow-alt-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-stat gray">
                            <div class="visual">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span>{{ $leadStatuses['gray'] }}</span>
                                </div>
                                <div class="desc">Vendas Perdidas</div>
                            </div>
                            <a class="more" href="{{ route('crm.index') }}?lead_color=gray">
                                Ver todos <i class="fas fa-arrow-alt-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Kanban Board de Leads</h4>
                    <button class="btn btn-primary refresh-kanban">
                        <i class="fas fa-sync"></i> Atualizar
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="kanban-board">
                        <div class="row m-0 p-3">
                            <div class="col-md-3 kanban-column">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">Leads Quentes</h5>
                                    </div>
                                    <div class="card-body kanban-column-content" data-status="red">
                                        <div class="text-center py-5 loading-spinner">
                                            <div class="spinner-border text-danger" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 kanban-column">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Leads Frios</h5>
                                    </div>
                                    <div class="card-body kanban-column-content" data-status="blue">
                                        <div class="text-center py-5 loading-spinner">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 kanban-column">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">Em Negociação</h5>
                                    </div>
                                    <div class="card-body kanban-column-content" data-status="yellow">
                                        <div class="text-center py-5 loading-spinner">
                                            <div class="spinner-border text-warning" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 kanban-column">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">Vendas Perdidas</h5>
                                    </div>
                                    <div class="card-body kanban-column-content" data-status="gray">
                                        <div class="text-center py-5 loading-spinner">
                                            <div class="spinner-border text-secondary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Lista Completa de Leads</h4>
                </div>
                <div class="card-body">
                    {!! $table->renderTable() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('header')
    <style>
        .dashboard-stat {
            position: relative;
            display: block;
            margin-bottom: 25px;
            overflow: hidden;
            border-radius: 10px;
        }
        
        .dashboard-stat .visual {
            width: 80px;
            height: 80px;
            display: block;
            float: left;
            padding-top: 10px;
            padding-left: 15px;
            margin-bottom: 15px;
            font-size: 35px;
            line-height: 35px;
        }
        
        .dashboard-stat .details {
            position: absolute;
            right: 15px;
            padding-right: 15px;
            color: #FFFFFF;
            text-align: right;
        }
        
        .dashboard-stat .details .number {
            padding-top: 25px;
            text-align: right;
            font-size: 34px;
            line-height: 36px;
            letter-spacing: -1px;
            margin-bottom: 0px;
            font-weight: 300;
        }
        
        .dashboard-stat .details .desc {
            text-align: right;
            font-size: 16px;
            letter-spacing: 0px;
            font-weight: 300;
        }
        
        .dashboard-stat .more {
            clear: both;
            display: block;
            padding: 6px 10px;
            position: relative;
            color: #fff;
            text-transform: uppercase;
            font-weight: 300;
            font-size: 11px;
            opacity: 0.7;
            text-decoration: none;
        }
        
        .dashboard-stat .more:hover {
            text-decoration: none;
            opacity: 0.9;
        }
        
        .dashboard-stat .more > i {
            display: inline-block;
            margin-top: 1px;
            float: right;
        }
        
        .dashboard-stat.red {
            background-color: #e7505a;
        }
        
        .dashboard-stat.red .more {
            background-color: #e53e49;
            color: #fff;
        }
        
        .dashboard-stat.red .visual > i {
            color: #fff;
            opacity: 0.3;
        }
        
        .dashboard-stat.blue {
            background-color: #3598dc;
        }
        
        .dashboard-stat.blue .more {
            background-color: #258fd7;
            color: #fff;
        }
        
        .dashboard-stat.blue .visual > i {
            color: #fff;
            opacity: 0.3;
        }
        
        .dashboard-stat.yellow {
            background-color: #f1c40f;
        }
        
        .dashboard-stat.yellow .more {
            background-color: #ddb100;
            color: #fff;
        }
        
        .dashboard-stat.yellow .visual > i {
            color: #fff;
            opacity: 0.3;
        }
        
        .dashboard-stat.gray {
            background-color: #95a5a6;
        }
        
        .dashboard-stat.gray .more {
            background-color: #7f8c8d;
            color: #fff;
        }
        
        .dashboard-stat.gray .visual > i {
            color: #fff;
            opacity: 0.3;
        }
        
        /* Kanban styling */
        .kanban-board {
            min-height: 500px;
        }
        
        .kanban-column-content {
            min-height: 400px;
        }
        
        .kanban-card {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            cursor: move;
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        }
        
        .kanban-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
            transform: translateY(-2px);
        }
        
        .kanban-card-ghost {
            background-color: #f8f9fa;
            opacity: 0.8;
        }
        
        .kanban-card-chosen {
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }
        
        .kanban-card-drag {
            opacity: 0.9;
        }
        
        .kanban-card-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .kanban-card-category {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .kanban-card-property {
            font-size: 14px;
            color: #333;
            font-weight: bold;
        }
        
        .kanban-card-contact {
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
@endpush

@push('footer')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    window.loadKanbanItems = function() {
        // Mostrar spinner de carregamento
        $('.kanban-column-content').each(function() {
            $(this).html(`
                <div class="text-center py-5 loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        });
        
        // Carregar dados
        $.ajax({
            url: '{{ route('crm.kanban-data') }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Clear existing content
                $('.kanban-column-content').each(function() {
                    $(this).html('');
                });
                
                // Populate each column
                $.each(data, function(status, leads) {
                    var $column = $('.kanban-column-content[data-status="' + status + '"]');
                    
                    if (leads.length === 0) {
                        $column.append('<div class="text-center py-5 text-muted">Sem leads nesta categoria</div>');
                    } else {
                        $.each(leads, function(index, lead) {
                            var card = createLeadCard(lead);
                            $column.append(card);
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Erro ao carregar leads:', error);
                $('.kanban-column-content').html('');
                $('.kanban-column-content').append('<div class="alert alert-danger">Erro ao carregar leads. Tente novamente.</div>');
            }
        });
    };
    
    function createLeadCard(lead) {
        var phoneDisplay = lead.phone ? lead.phone : 'Sem telefone';
        var emailDisplay = lead.email ? lead.email : 'Sem email';
        var propertyValue = lead.property_value ? lead.formatted_property_value : 'Valor não informado';
        
        return `
            <div class="kanban-card" data-id="${lead.id}">
                <div class="kanban-card-title">${lead.name}</div>
                <div class="kanban-card-category">Categoria: ${lead.category_text}</div>
                <div class="kanban-card-property">${propertyValue}</div>
                <div class="kanban-card-contact">
                    <div><i class="fas fa-phone-alt"></i> ${phoneDisplay}</div>
                    <div><i class="fas fa-envelope"></i> ${emailDisplay}</div>
                </div>
                <div class="kanban-card-actions mt-2">
                    <a href="${lead.edit_url}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-info view-notes" data-id="${lead.id}" 
                            data-notes="${lead.content ? lead.content.replace(/"/g, '&quot;') : ''}">
                        <i class="fas fa-sticky-note"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    $(document).ready(function() {
        // Initial load of kanban items
        setTimeout(function() {
            loadKanbanItems();
        }, 500);
        
        // Verificar se Sortable está disponível
        if (typeof Sortable !== 'undefined') {
            // Initialize sortable for each kanban column
            const columns = document.querySelectorAll('.kanban-column-content');
            const sortables = [];
            
            columns.forEach(column => {
                const sortable = Sortable.create(column, {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'kanban-card-ghost',
                    chosenClass: 'kanban-card-chosen',
                    dragClass: 'kanban-card-drag',
                    onEnd: function(evt) {
                        const leadId = evt.item.getAttribute('data-id');
                        const newStatus = evt.to.getAttribute('data-status');
                        const oldStatus = evt.from.getAttribute('data-status');
                        
                        // Only update if status actually changed
                        if (newStatus !== oldStatus) {
                            updateLeadStatus(leadId, newStatus);
                        }
                    }
                });
                
                sortables.push(sortable);
            });
        } else {
            console.error('Sortable.js não está carregado. A funcionalidade de arrastar e soltar não estará disponível.');
            // Remover atribuição de 'cursor: move' que implica em funcionalidade
            $('.kanban-card').css('cursor', 'default');
        }
        
        // Function to update lead status via AJAX
        function updateLeadStatus(leadId, newStatus) {
            $.ajax({
                url: '{{ route('crm.update-status') }}',
                type: 'POST',
                data: {
                    id: leadId,
                    lead_color: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Show success notification
                    if (typeof Botble !== 'undefined' && Botble.showNotice) {
                        Botble.showNotice('success', response.message || 'Status atualizado com sucesso');
                    } else {
                        alert('Status atualizado com sucesso');
                    }
                },
                error: function(xhr, status, error) {
                    // Show error notification and move card back
                    const errorMessage = xhr.responseJSON?.message || 'Erro ao atualizar status';
                    
                    if (typeof Botble !== 'undefined' && Botble.showNotice) {
                        Botble.showNotice('error', errorMessage);
                    } else {
                        alert('Erro: ' + errorMessage);
                    }
                    
                    // Refresh the board to reset positions
                    loadKanbanItems();
                }
            });
        }
        
        // Handle notes view
        $(document).on('click', '.view-notes', function() {
            var notes = $(this).data('notes') || 'Sem anotações para este lead.';
            var leadId = $(this).data('id');
            
            Botble.showNotice('info', 'Anotações', notes, null, 10000);
        });
        
        // Add refresh button handler
        $('.refresh-kanban').on('click', function() {
            loadKanbanItems();
            Botble.showNotice('success', 'Kanban atualizado');
        });
    });
</script>
@endpush