/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Mensagia.com
 *  @copyright 2010-2015 Sinermedia
 *  @license   LICENSE.txt
 */
jQuery(document).ready(function(){

    jQuery("#sapi_configuration").change(function(e){
        show_remitente();
    });

    show_remitente();
});

function show_remitente() {
    optionSelected = jQuery( "#sapi_configuration option:selected" );

    allow_service_number_alias  = optionSelected.attr('allow_service_number_alias');
    sender_name                 = optionSelected.attr('sender_name');

    if (allow_service_number_alias == "true")
        jQuery(".remitente").html(sender_name);
    else{
        if (optionSelected.attr('sender_name') != "-")
            jQuery(".remitente").html(no_dinamic_sender);
        else
            jQuery(".remitente").html("-");
    }

}