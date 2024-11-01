var demo    = false;

jQuery(document).ready(function(){

    jQuery(function() {

        jQuery('.toggleNotification').bootstrapToggle({
            on: notificacion_activada,
            off: notificacion_desactivada,
            width: 200
        });

        jQuery('.lichange').click(function(e) {

            e.preventDefault();

            group   = jQuery(this).attr('group');
            opener  = jQuery(this).attr('opener');
            counter = jQuery(this).attr('counter');

            jQuery("#"+opener).fadeIn();
            jQuery(".li_"+group).removeClass('active');
            jQuery(this).parent().addClass('active');

            jQuery(".textarea_"+group).hide();
            jQuery(".textarea_"+group+"_"+counter).show();

            // texto ejemplo
            text =  jQuery(".textarea_"+group+"_"+counter).val();
            newText = transformTextExample(text);
            jQuery(".textarea_"+group+"_"+counter).parent().find('.textTransform').text(newText);
        });

        jQuery('.toogleTypeNotification').click(function() {

            mustOpen = jQuery(this).attr('mustOpen');
            mustOpenClass  = jQuery(this).attr('mustOpenClass');

            jQuery(this).parent().parent().find("li").removeClass('active');
            jQuery(this).parent().addClass('active');

            jQuery("."+mustOpenClass).hide();
            jQuery("#"+mustOpen).show();
        });

        jQuery('.toggleNotification').change(function() {

            hook    = jQuery(this).attr('hook');
            htype   = jQuery(this).attr('htype');
            hid     = jQuery(this).attr('hid');

            if (jQuery(this).prop('checked'))
                active = 1;
            else
                active = 0;

            jQuery.ajax({
                url: ajax_url,
                data:{
                    action: 'notifications_request',
                    type_req: 'toggleNotification',
                    hook: hook,
                    htype: htype,
                    hid: hid,
                    active: active,
                },
                method: 'POST',
                success: function(data){
                    if (data != 'true')
                        alert('Ha habido un error cambiando el estado de la notificación')
                }
            });
        });

        jQuery('.openMsgSelector').click(function(e) {

            e.preventDefault();

            opened  = jQuery(this).attr('opened');
            related = jQuery(this).attr('related');
            opener  = jQuery(this).attr('related').replace('h_', 't_');

            if (opened == "false")
            {
                jQuery("#"+opener+" td").attr('style', 'border-bottom: 0px !important;background-color:#eee;');
                jQuery("#"+related).fadeIn();

                jQuery(this).attr('opened', "true");

                // texto ejemplo
                text =  jQuery("#"+related+"").find('textarea:visible').val();
                newText = transformTextExample(text);
                jQuery("#"+related+"").find('.textTransform').text(newText);
            }
            else
            {

                jQuery("#"+opener+" td").removeAttr('style');
                jQuery("#"+related).fadeOut();

                jQuery(this).attr('opened', "false");
            }

        });


        jQuery('.btn-save').click(function(e) {

            if (demo) {
                jQuery('#modalDemo').modal('toggle');
                return false;
            }

            _this = jQuery(this);

            e.preventDefault();

            group   = jQuery(this).attr('group');
            counter = jQuery(this).attr('counter');
            textOld = jQuery("#textarea_"+group).attr('textOld');

            if (textOld == jQuery("#textarea_"+group).val())
                return false;
            else
                jQuery("#textarea_"+group).attr('textOld', jQuery("#textarea_"+group).val());


            var arr = [];

            arr.push({
                text: jQuery("#textarea_"+group).val(),
                id : parseInt(jQuery("#textarea_"+group).attr('lang_id'))
            });

            json = JSON.stringify( arr );

            jQuery.ajax({
                url: ajax_url,
                data:{
                    action: 'notifications_request',
                    type_req: 'saveTextNotification',
                    json: json,
                },
                method: 'POST',
                success: function(data){
                    _this.find('.loaderImg').fadeOut();

                    if (data == 'true')
                    {
                        _this.parent().find('.saved_ok').fadeIn();

                        setTimeout(function(){ _this.parent().find('.saved_ok').fadeOut()}, 3000);
                    }
                    else
                    {
                        _this.parent().find('.saved_ko').fadeIn();

                        setTimeout(function(){ _this.parent().find('.saved_ko').fadeOut()}, 3000);
                    }

                },
                beforeSend: function() {
                    _this.find('.loaderImg').fadeIn();
                },
            });
        });

        jQuery('.table_vars tbody tr td').click(function() {

            id   = jQuery(this).parent().parent().parent().parent().parent().parent().find('textarea:visible').attr('id');

            addToMensage(id, jQuery(this).text(), false);

            text = null;
            text = jQuery(this).parent().parent().parent().parent().parent().parent().find('textarea:visible').val();
            newText = transformTextExample(text);

            jQuery(this).parent().parent().parent().parent().parent().parent().find('.textTransform').text(newText);
        });


        jQuery('.btn-close').click(function() {

            related = jQuery(this).attr('related');
            opener  = jQuery(this).attr('related').replace('h_', 't_');

            jQuery("#"+opener+" td").removeAttr('style');
            jQuery("#"+related).fadeOut();
        });

        jQuery('textarea').keyup(function() {
            // texto ejemplo
            text =  jQuery(this).val();
            newText = transformTextExample(text);
            jQuery(this).parent().find('.textTransform').text(newText);
        });

    });
});

function transformTextExample(text)
{
    trobat = false;

    var regExp = /\{([^}]+)\}/;

    while(!trobat)
    {
        var matches = regExp.exec(text);
        if (matches)
        {
            if (typeof matches[1] !== 'undefined') {
                if (typeof example['{'+matches[1]+'}'] !== 'undefined')
                    text = text.replace('{'+matches[1]+'}', example['{'+matches[1]+'}']);
                else
                    text = text.replace('{'+matches[1]+'}', '');
            }
            else
                trobat = true;
        }
        else
            trobat = true;

    }

    return text;
}

function addToMensage(selector_textarea,txtToAdd, highlight)
{
    messageTxt = jQuery("#"+selector_textarea);

    var caretPos = document.getElementById(messageTxt.attr('id')).selectionStart;
    var textAreaTxt = messageTxt.val();
    messageTxt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
}