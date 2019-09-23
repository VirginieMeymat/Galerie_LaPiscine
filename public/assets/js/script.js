
$(document).ready(function(){
        /* méthode au survol du menu "Les Artistes" qui affiche ou cache le sous-menu */
    $('.menu_artists').hover(function(){
            $('.menu_categories').stop().slideDown('slow');
        }, function(){
             $('.menu_categories').stop().slideUp();
        }
    );

    $('.menu_categories').click(function(){
            $('.menu_categories').hide();
        }
    );

    /* ADMIN */

    /* modale pour message flash */
    $('.message').delay(3000).fadeOut();


});