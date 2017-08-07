(function($) {
    
    $(document).ready(function(){
        
        $('#voy_field_voynotif_content .voy-field_input').append( $('#postdivrich') );
        
    });
    

    
    $(document).ajaxComplete(function(){
       
    $('.select2 select').select2();
        if($(".voy-recipient_type_field_wrapper select").val() == 'emails') {
            $("#voy_field_voynotif_recipient_emails").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_recipient_emails").addClass('invisible');
        }
        if($(".voy-recipient_type_field_wrapper select").val() == 'roles') {
            $("#voy_field_voynotif_recipient_roles").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_recipient_roles").addClass('invisible');
        }
        if($(".voy-recipient_type_field_wrapper select").val() == 'users') {
            $("#voy_field_voynotif_recipient_users").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_recipient_users").addClass('invisible');
        }
  
    $("#voynotif_specify_sender").change(function() {
        if(this.checked) {
            $("#voy_field_voynotif_sender_name").removeClass('invisible');
            $("#voy_field_voynotif_sender_email").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_sender_name").addClass('invisible');
            $("#voy_field_voynotif_sender_email").addClass('invisible');
        }
    });
       
    $(".voy-recipient_type_field_wrapper").change(function() {
        if($(".voy-recipient_type_field_wrapper select").val() == 'emails') {
            $("#voy_field_voynotif_recipient_emails").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_recipient_emails").addClass('invisible');
        }
        if($(".voy-recipient_type_field_wrapper select").val() == 'roles') {
            $("#voy_field_voynotif_recipient_roles").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_recipient_roles").addClass('invisible');
        }
        if($(".voy-recipient_type_field_wrapper select").val() == 'users') {
            $("#voy_field_voynotif_recipient_users").removeClass('invisible');
        } else {
            $("#voy_field_voynotif_recipient_users").addClass('invisible');
        }
    });  
    
   jQuery(".voy_mask_select").change(function() {
        
        if( jQuery(":selected", this).val() != "" ) {
            //send_to_editor("{" + jQuery(":selected", this).val() + "}");
            var content_to_send = "{" + jQuery(":selected", this).val() + "}";
            tinymce.activeEditor.execCommand('mceInsertContent', false, content_to_send); 
        }       
        jQuery(this).val("");
        return false;
    });
    
  });
  
})(jQuery);