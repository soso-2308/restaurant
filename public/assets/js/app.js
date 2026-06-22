/**
 * RYOHA - Application JavaScript
 */

$(document).ready(function() {
    
    // --- Mobile menu toggle ---
    $('#mobileToggle').click(function() {
        $('.nav').toggleClass('open');
    });

    // --- Fermer le menu mobile en cliquant sur un lien ---
    $('.nav a').click(function() {
        if ($(window).width() <= 768) {
            $('.nav').removeClass('open');
        }
    });

    // --- Smooth scroll pour les ancres ---
    $('a[href^="#"]').click(function(e) {
        e.preventDefault();
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });

    // --- Masquer les messages flash après 5 secondes ---
    setTimeout(function() {
        $('.flash-message').fadeOut('slow');
    }, 5000);

});