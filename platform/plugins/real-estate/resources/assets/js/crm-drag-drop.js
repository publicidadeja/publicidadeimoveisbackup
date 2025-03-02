/**
 * Kanban board drag and drop functionality using Sortable.js
 */
$(document).ready(function() {
    if (typeof Sortable === 'undefined') {
        console.error('Sortable.js is required for drag and drop functionality');
        return;
    }

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
    
    // Function to update lead status via AJAX
    function updateLeadStatus(leadId, newStatus) {
        $.ajax({
            url: route('crm.update-status'),
            type: 'POST',
            data: {
                id: leadId,
                lead_color: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Show success notification
                Botble.showNotice('success', response.message || 'Status atualizado com sucesso');
            },
            error: function(xhr, status, error) {
                // Show error notification and move card back
                const errorMessage = xhr.responseJSON?.message || 'Erro ao atualizar status';
                Botble.showNotice('error', errorMessage);
                
                // Refresh the board to reset positions
                loadKanbanItems();
            }
        });
    }
});