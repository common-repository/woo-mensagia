<div class='wrap'>
    <h2><?php echo __('Export to Mensagia', 'mensagia-woocommerce');?></h2>
</div>

<?php

//var_dump(get_option('test_mensagia'));
?>

<div class="bootstrap-wrapper">
    <div class="panel">
        <div class="panel-heading"><i class="fa fa-external-link"></i> <?php echo __("Export to Mensagia", 'mensagia-woocommerce');?></div>
        <div class="panel-body">

            <form id="exportar_form" class="defaultForm form-horizontal mensagiaprestashop" action="" method="post" enctype="multipart/form-data" novalidate="" autocomplete="off">


                <div class="form-group" style="margin-top:30px;">
                    <label class="control-label col-lg-3" for="prefix_agenda">
                        <?php echo __("Choose the user roles to export", 'mensagia-woocommerce');?>
                    </label>

                    <div class="col-lg-9">
                        <?php
                        foreach ($wp_roles->roles as $key =>$role) {
                            echo "<label for='role_".$key."' style='cursor:pointer;'><input id='role_".$key."' type='checkbox' class='roles' value='".$key."'> ".$role['name']."</label><br>";
                        }

                        ?>

                        <div class="error_export" style="color:red;font-weight:bold;margin-top:10px;display:none;"><?php echo __("You must choose at least one role to export to Mensagia", 'woo-mensagia');?></div>

                    </div>
                </div>

                <div class="form-group" style="margin-top:30px;">
                    <label class="control-label col-lg-3" for="prefix_agenda">
                        <?php echo __("Choose the agenda name", 'woo-mensagia');?>
                    </label>

                    <div class="col-lg-9">
                        <input type="text" id="prefix_agenda" name="prefix_agenda" class="form-control" value="<?php echo $name;?>_<?php echo $date_prefix;?>">
                        <span id="helpBlockAgendas" class="help-block" style="display:none;"><?php echo __("The following agendas will be created on Mensagia:", 'woo-mensagia');?> <span id="helpBlockAgendasNames"></span></span>
                        <div class="error_name_agenda" style="color:red;font-weight:bold;margin-top:10px;display:none;"><?php echo __("The agenda name cannot be empty", 'woo-mensagia');?></div>
                    </div>
                </div>


                <div class="form-group" style="margin-top:30px;">
                    <label class="control-label col-lg-3">
                        <?php echo __("If this agenda already exists in Mensagia. Do you want to delete it?", 'woo-mensagia');?>
                    </label>

                    <div class="col-lg-9">
                        <p class="radio">
                            <label for="radioNO" style="font-weight:bold;"><input type="radio" id="radioNO" name="delete_agendas" class="" value="0" checked><?php echo __("No", 'woo-mensagia');?></label> <br>
                        </p>
                        <p class="radio">
                            <label for="radioSI" style="font-weight:bold;"><input type="radio" id="radioSI" name="delete_agendas" class="" value="1"><?php echo __("Yes", 'woo-mensagia');?></label>
                        </p>
                    </div>
                </div>

                <div class="form-group" style="margin-top:30px;">
                    <label class="control-label col-lg-3">
                        <?php echo __("Fields to export", 'woo-mensagia');?>
                    </label>

                    <div class="col-lg-9">
                        <div class="row" style="border:1px solid #ddd;padding:20px;">
                            <div class="col-lg-3">
                                <div class="checks">
                                    <b style="margin-bottom: 10px;display: block;text-decoration:underline;"><?php echo __("Users", 'woo-mensagia');?></b>
                                    <label for="woo_phone" style="cursor:pointer;"> <input type="checkbox" id="woo_phone" name="woo_phone"  class="" value="1" checked disabled> <?php echo __("Mobile phone", 'woo-mensagia');?> </label><br>
                                    <label for="woo_name" style="cursor:pointer;"> <input type="checkbox" id="woo_name" name="woo_name"  class="" value="1" checked disabled> <?php echo __("Name and lastname", 'woo-mensagia');?> </label><br>
                                    <label for="woo_email" style="cursor:pointer;"> <input type="checkbox" id="woo_email" name="woo_email"  class="" value="1" checked disabled> <?php echo __("Email", 'woo-mensagia');?> </label><br>
                                    <label for="woo_id_customer" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_id_customer"> <input type="checkbox" id="woo_id_customer" woo_group="customer" nameToShow="<?php echo __("Customer ID", 'woo-mensagia');?>" extra_field_type="number" class="woo_extrafields" value="1" date_format_type=""> <?php echo __("Customer ID", 'woo-mensagia');?> </label><br>
                                    <label for="woo_date_add" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_date_add"> <input type="checkbox" id="woo_date_add" name="woo_date_add" woo_group="customer" nameToShow="<?php echo __("Creation date", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="date" date_format_type="date_format_full" value="1"> <?php echo __("Creation date", 'woo-mensagia');?></label><br>
                                    <label for="woo_user_role" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_user_role"> <input type="checkbox" id="woo_user_role" woo_group="customer" nameToShow="<?php echo __("User role", 'woo-mensagia');?>" extra_field_type="string" class="woo_extrafields" value="1" date_format_type=""> <?php echo __("User role", 'woo-mensagia');?> </label><br>
                                </div>

                                <div class="" style="margin-top:10px;">
                                    <label for="markAllCustomers" style="cursor:pointer;font-weight: normal;"> <input type="checkbox" id="markAllCustomers" name="markAllCustomers"  class="markAll" value="1"> <?php echo __("Mark / Unmark all", 'woo-mensagia');?></label><br>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div  class="checks">
                                    <b style="margin-bottom: 10px;display: block;text-decoration:underline;"><?php echo __("Orders", 'woo-mensagia');?></b>
                                    <label for="woo_date_last_order" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_date_last_order"> <input type="checkbox" id="woo_date_last_order" name="woo_date_last_order" woo_group="order" nameToShow="<?php echo __("Last order date", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="date" value="1" date_format_type="date_format_full"> <?php echo __("Last order date", 'woo-mensagia');?> </label><br>
                                    <label for="woo_orders_quantity" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_orders_quantity"> <input type="checkbox" id="woo_orders_quantity" name="woo_orders_quantity" woo_group="order" nameToShow="<?php echo __("Number of orders placed", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="number" value="1" date_format_type=""> <?php echo __("Number of orders placed", 'woo-mensagia');?> </label><br>
                                    <label for="woo_orders_total_spent" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_orders_total_spent"> <input type="checkbox" id="woo_orders_total_spent" name="woo_orders_total_spent" woo_group="order" nameToShow="<?php echo __("Total spent", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="number" value="1" date_format_type=""> <?php echo __("Total spent", 'woo-mensagia');?> </label><br>
                                    <label for="woo_orders_total_spent_current_year" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_orders_total_spent_current_year"> <input type="checkbox" id="woo_orders_total_spent_current_year" name="woo_orders_total_spent_current_year" woo_group="order" nameToShow="<?php echo __("Total spent this year", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="number" value="1" date_format_type=""> <?php echo __("Total spent this year", 'woo-mensagia');?> </label><br>
                                    <label for="woo_orders_total_spent_last_year" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_orders_total_spent_last_year"> <input type="checkbox" id="woo_orders_total_spent_last_year" name="woo_orders_total_spent_last_year" woo_group="order" nameToShow="<?php echo __("Total spent the last year", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="number" value="1" date_format_type=""> <?php echo __("Total spent the last year", 'woo-mensagia');?> </label><br>
                                    <label for="woo_orders_total_spent_current_month" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_orders_total_spent_current_month"> <input type="checkbox" id="woo_orders_total_spent_current_month" name="woo_orders_total_spent_current_month" woo_group="order" nameToShow="<?php echo __("Total spent this month", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="number" value="1" date_format_type=""> <?php echo __("Total spent this month", 'woo-mensagia');?> </label><br>
                                    <label for="woo_orders_total_spent_last_month" style="cursor:pointer;" data-toggle="tooltip" title="<?php echo __("Mensagia extra field", 'woo-mensagia');?>: woo_orders_total_spent_last_month"> <input type="checkbox" id="woo_orders_total_spent_last_month" name="woo_orders_total_spent_last_month" woo_group="order" nameToShow="<?php echo __("Total spent the last month", 'woo-mensagia');?>" class="woo_extrafields" extra_field_type="number" value="1" date_format_type=""> <?php echo __("Total spent the last month", 'woo-mensagia');?> </label><br>
                                </div>

                                <div class="" style="margin-top:10px;">
                                    <label for="markAllOrders" style="cursor:pointer;font-weight: normal;"> <input type="checkbox" id="markAllOrders" name="markAllOrders"  class="markAll" value="1"> <?php echo __("Mark / Unmark all", 'woo-mensagia');?></label><br>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>

                <div class="form-group">

                    <label class="control-label col-lg-3">

                    </label>

                    <div class="col-lg-9">
                        <button type="submit" class="btn btn-primary pull-right btnExport" name=""><i class="fa fa-external-link-square"></i> <?php echo __("Export groups to Mensagia", 'woo-mensagia');?></button>
                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="panel panel-export" id='exportBox' style="display: none;">
        <div class="panel-heading"><i class="fa fa-gear"></i> <?php echo __("Export", 'woo-mensagia');?></div>
        <div class="panel-body">

            <div class="alert alert-danger exportError" style="display:none;">
                <?php echo __("Export has ended unexpectedly. Check the export errors to know the reason.", 'woo-mensagia');?>
            </div>

            <div class="alert alert-success exportSuccess" style="display:none;">
                <?php echo __("Export completed successfully.", 'woo-mensagia');?>
            </div>

            <div class="progress" style="height:30px">
                <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar"
                     aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%;min-width: 2em;height:30px;line-height: 25px;">
                    0%
                </div>
            </div>

            <b><?php echo __("Export processing", 'woo-mensagia');?></b>
            <div class="export_process" style="border:1px solid #aaa; padding:10px;height:230px;overflow-y:scroll;"></div>
            <br>
            <b><?php echo __("Export errors", 'woo-mensagia');?></b>
            <div class="export_process_errors" style="border:1px solid #aaa; padding:10px;color:red;height:230px;overflow-y:scroll;"></div>
        </div>
    </div>
</div>



<?php require_once "partials/modalDemo.php"?>
<?php require_once "partials/logoFooter.php"?>

<script type="text/javascript">
    var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
    var language_id         = 1;
    var default_language_id = 1;
    var date_format_lite    = 'Y-m-d';
    var date_format_full    = 'Y-m-d H:i:s';
    var prefix_mode         = '<?php echo $this->prefix_mode;?>';
</script>
