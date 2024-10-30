jQuery(document).ready(function($){
    /*Intialize color picker*/
    $('.cm-color-picker').wpColorPicker();
    /*Show color picker if alternate color is picked*/
    $("[name='cm_choose_color']").on('change', function(){
        if( $(this).val() === 'other-color' ){
            $('.cm-other-color').show();
        }else{
            $('.cm-other-color').hide();
        }
    });

    /*Make checkboxes to determine social networks sortable*/
    $('#sortable').sortable();

});