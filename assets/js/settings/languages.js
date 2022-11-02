/**
 *  languages
 *  Creat New Theme
 */

// Detect Changes
$("body").on("change keyup", ".languages_update-tool form#lang-update input", function(event){
    let old = $(this).data('old');
    let change = $(this).val();
    if (old == change) {
        $(this).removeClass('alert-success');
    } else {
        $(this).addClass('alert-success');
    }
    let changed = 0;
    $( ".languages_update-tool form#lang-update input" ).each(function( index ) {
        if ($( this ).data('old') !== $( this ).val()) {
            changed++;
        }
    });
    $( '.languages_update-tool form #countChange' ).html( changed );
});

// @todo - count the changed phrase


// Add IP
$("body").on("submit", ".languages_update-tool form#lang-update", function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('languages', 'update',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("lang file saved successfully.");
            setTimeout(function() {
                window.location.href = 'sys_settings.php?section=languages_update-tool';
            }, 550);
        }
    });
});

