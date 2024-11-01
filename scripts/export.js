var demo    = false;

var options = {
    roles                           : [],
    exporting                       : false,
    prefix_agenda                   : null,
    delete_agendas                  : null,
    limit                           : 500,
    jobs                            : 0,
    jobs_done                       : 0,
    next_index_group_to_export      : null,
    export_started                  : false,
    groups                          : [],
    processes                       : [],
    extrafields                     : [],
    default_language_id             : null,
    date_format                     : null,
    date_format_full                : null,
    waiting_finish_processes        : false,
    finished                        : false,
    processes_checked_errors        : [],
    prefix_mode                     : null,
    total_rows_processed            : 0,
    total_created_contacts          : 0,
    total_imported_contacts         : 0,
    total_errors                    : 0,
    woo_total_users                  : 0
}

jQuery(document).ready(function(){

    jQuery('[data-toggle="tooltip"]').tooltip();


    jQuery(".markAll").change(function() {
        value = jQuery(this).prop('checked');

        jQuery(this).parent().parent().parent().find('input').each(function() {

            uncheckeables = ['woo_phone', 'woo_name', 'woo_email', 'woo_city'];

            id = jQuery(this).prop('id');

            if (! in_array(id, uncheckeables, false))
                jQuery(this).prop('checked', value);
        });

    });

    jQuery(".btnExport").click(function(e) {

        e.preventDefault();

        jQuery(".error_export").hide();
        jQuery(".error_name_agenda").hide();

        options.prefix_agenda       = jQuery("#prefix_agenda").val();
        options.default_language_id = default_language_id;
        options.date_format_lite    = date_format_lite;
        options.date_format_full    = date_format_full;
        options.prefix_mode         = prefix_mode;


        if (jQuery("input[name='delete_agendas']:checked").val() == "1")
            options.delete_agendas = true;
        else
            options.delete_agendas = false;

        // extrafields
        jQuery(".woo_extrafields:checked").each(function() {

            options.extrafields.push({
                name: jQuery(this).attr('id'),
                extra_field_type: jQuery(this).attr('extra_field_type'),
                nameToShow: jQuery(this).attr('nameToShow'),
                date_format_type: jQuery(this).attr('date_format_type'),
                woo_group: jQuery(this).attr('woo_group')
            });
        });

        // roles
        jQuery(".roles:checked").each(function() {
            options.roles.push(jQuery(this).val());
        });


        if (demo) {
            jQuery('#modalDemo').modal('toggle');
            return false;
        }

        if (!options.roles.length) {
            jQuery(".error_export").fadeIn();
        } else if (!options.prefix_agenda) {
            jQuery(".error_name_agenda").fadeIn();
        } else {
            if (! options.exporting)
            {
                if (demo) {
                    $('#modalDemo').modal('toggle');
                    return false;
                }

                options.exporting = true;

                block_export_button();

                export_init();

                goToByScroll('exportBox');
            }
        }


    });

});

function block_export_button()
{
    jQuery(".btnExport").fadeOut();
    jQuery(".panel-export").fadeIn();
}

function unblock_export_button()
{
    jQuery(".btnExport").prop('disabled', false);
    jQuery(".loaderImg").hide();
    jQuery(".imageExport").show();
}

function export_init()
{
    jQuery.ajax({
        url: ajax_url,
        data:{
            action: 'export_request',
            type_req: 'export_init',
            token: new Date().getTime(),
            options: JSON.stringify( options ),
            language_id: language_id
        },
        method: 'POST',
        type: 'JSON',
        success: function(result){

            data = JSON.parse(result);

            options = data['options'];
            jQuery(".export_process").append(data['html']);
            jQuery(".export_process_errors").append(data['errors']);

            if (data['continue'])
            {
                if (options.delete_agendas)
                    delete_agendas();
                else
                    create_agendas();
            }
        }
    });
}

function export_error()
{
    jQuery(".progress").hide();
    jQuery(".exportError").fadeIn();
}


function export_finished()
{
    jQuery(".progress-bar").removeClass('progress-bar-info').addClass('progress-bar-success');

    jQuery.ajax({
        url: ajax_url,
        data:{
            action: 'export_request',
            type_req: 'export_finished',
            token: new Date().getTime(),
            options: JSON.stringify( options )
        },
        method: 'POST',
        type: 'JSON',
        success: function(result){
            data = JSON.parse(result);

            options = data['options'];
            jQuery(".export_process").append(data['html']);
            jQuery(".export_process_errors").append(data['errors']);
        }
    });
}

function waitingFinishProcesses()
{
    jQuery.ajax({
        url: ajax_url,
        data:{
            action: 'export_request',
            type_req: 'waiting_finish_processes',
            token: new Date().getTime(),
            options: JSON.stringify( options )
        },
        method: 'POST',
        type: 'JSON',
        success: function(result){
            data = JSON.parse(result);

            options = data['options'];
            jQuery(".export_process").append(data['html']);
            jQuery(".export_process_errors").append(data['errors']);

            refresh_progress_bar();

            if (data['continue'] == true)
            {
                if (options.finished)
                    export_finished();
                else
                    setTimeout(function(){waitingFinishProcesses()}, 5000);
            }
            else
                export_error();
        }
    });
}


function export_groups()
{
    jQuery.ajax({
        url: ajax_url,
        data:{
            action: 'export_request',
            type_req: 'export_groups',
            token: new Date().getTime(),
            options: JSON.stringify( options )
        },
        method: 'POST',
        type: 'JSON',
        success: function(result){
            data = JSON.parse(result);

            options = data['options'];
            jQuery(".export_process").append(data['html']);
            jQuery(".export_process_errors").append(data['errors']);

            refresh_progress_bar();

            if (data['continue'] == true)
            {
                if (options.next_index_group_to_export !== null)
                    setTimeout(function(){export_groups(), 5000});
                else
                    waitingFinishProcesses();
            }
            else
                export_error();
        }
    });
}


function check_extrafields()
{
    jQuery.ajax({
        url: ajax_url,
        data:{
            action: 'export_request',
            type_req: 'check_extrafields',
            token: new Date().getTime(),
            options: JSON.stringify( options )
        },
        method: 'POST',
        type: 'JSON',
        success: function(result){
            data = JSON.parse(result);

            options = data['options'];
            jQuery(".export_process").append(data['html']);
            jQuery(".export_process_errors").append(data['errors']);

            refresh_progress_bar();

            if (data['continue'] == true)
                export_groups();
            else
                export_error();
        }
    });
}


function create_agendas()
{
    jQuery.ajax({
        url: ajax_url,
        data:{
            action: 'export_request',
            type_req: 'create_agendas',
            token: new Date().getTime(),
            options: JSON.stringify( options )
        },
        method: 'POST',
        type: 'JSON',
        success: function(result){
            data = JSON.parse(result);

            options = data['options'];
            jQuery(".export_process").append(data['html']);
            jQuery(".export_process_errors").append(data['errors']);

            refresh_progress_bar();

            if (data['continue'] == true)
                check_extrafields();
            else
                export_error();
        }
    });
}

function delete_agendas()
{
    if (options.delete_agendas)
    {
        jQuery.ajax({
            url: ajax_url,
            data:{
                action: 'export_request',
                type_req: 'delete_agendas',
                token: new Date().getTime(),
                options: JSON.stringify( options )
            },
            method: 'POST',
            type: 'JSON',
            success: function(result){
                data = JSON.parse(result);

                options = data['options'];
                jQuery(".export_process").append(data['html']);

                refresh_progress_bar();

                if (data['continue'] == true)
                    create_agendas();
                else
                    export_error();
            }
        });
    }
}

function refresh_progress_bar()
{
    // get actual width
    actual_width = jQuery(".progress-bar").attr('aria-valuenow');

    // get new width
    width = ( options.jobs_done * 100 ) / options.jobs;


    if (actual_width != width)
    {
        jQuery(".progress-bar").attr('aria-valuenow', width);

        jQuery(".progress-bar").stop().animate({
            'width': width+"%"
        });

        if (width != 100)
            jQuery(".progress-bar").html(width.toFixed(2)+"%");
        else
            jQuery(".progress-bar").html("100%");
    }

    // scroll log always bottom
    jQuery(".export_process").animate({ scrollTop: jQuery(document).height() }, "slow");
    jQuery(".export_process_errors").animate({ scrollTop: jQuery(document).height() }, "slow");
}


function goToByScroll(id)
{
    id = id.replace("link", "");

    // Scroll
    jQuery('html,body').animate({
            scrollTop: jQuery("#"+id).offset().top},
        'slow');
}


function in_array (needle, haystack, argStrict) {
    var key = ''
    var strict = !!argStrict
    // we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] === ndl)
    // in just one for, in order to improve the performance
    // deciding wich type of comparation will do before walk array
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) { // eslint-disable-line eqeqeq
                return true
            }
        }
    }
    return false
}