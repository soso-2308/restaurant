/**
 * Admin RYOHA - JavaScript
 */

$(document).ready(function() {

    // ============================================
    // TOGGLE SIDEBAR
    // ============================================
    const sidebar = $('#sidebar');
    const toggleBtn = $('#toggleSidebar');
    const mainContent = $('#mainContent');

    // Vérifier l'état sauvegardé dans localStorage
    const sidebarState = localStorage.getItem('sidebarCollapsed');
    if (sidebarState === 'true') {
        sidebar.addClass('collapsed');
    }

    toggleBtn.on('click', function() {
        sidebar.toggleClass('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.hasClass('collapsed'));
    });

    // ============================================
    // MASQUER LES FLASH MESSAGES (5 secondes)
    // ============================================
    setTimeout(function() {
        $('.flash-message').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);

    // ============================================
    // CONFIRMATION POUR SUPPRESSION
    // ============================================
    // Utiliser sur tous les boutons .btn-delete-confirm
    $(document).on('click', '.btn-delete-confirm', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            e.preventDefault();
            return false;
        }
    });

    // ============================================
    // TOOLTIP SIMPLE (pour les icônes)
    // ============================================
    $(document).on('mouseenter', '[data-tooltip]', function() {
        const text = $(this).data('tooltip');
        if (!text) return;
        const tooltip = $('<div class="tooltip-custom">' + text + '</div>');
        tooltip.css({
            position: 'fixed',
            background: 'rgba(0,0,0,0.85)',
            color: '#fff',
            padding: '4px 12px',
            borderRadius: '6px',
            fontSize: '12px',
            fontWeight: '500',
            pointerEvents: 'none',
            zIndex: 9999,
            fontFamily: 'Inter, sans-serif',
            boxShadow: '0 4px 12px rgba(0,0,0,0.2)'
        });
        $('body').append(tooltip);
        const offset = $(this).offset();
        const width = $(this).outerWidth();
        tooltip.css({
            left: offset.left + width / 2 - tooltip.outerWidth() / 2,
            top: offset.top - tooltip.outerHeight() - 8
        });
        $(this).data('tooltip-element', tooltip);
    });

    $(document).on('mouseleave', '[data-tooltip]', function() {
        const tooltip = $(this).data('tooltip-element');
        if (tooltip) {
            tooltip.remove();
            $(this).removeData('tooltip-element');
        }
    });

});