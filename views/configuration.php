<div class='wrap'>
    <h2><?php echo __('Configuration', 'mensagia-woocommerce');?></h2>
</div>

<div class="bootstrap-wrapper">

    <?php
    if (!$this->email || !$this->password) {
        $messages['errors'][] =  __('You must enter your Mensagia\'s access data before using this plugin', 'mensagia-woocommerce');
    } else {
        if (! $this->authenticated) {
            $messages['errors'][] =  __('You must enter a valid Mensagia account before using this plugin', 'mensagia-woocommerce');
        }
    }

    if (count($messages['errors'])) {
        echo "<div class='alert alert-warning'>
                <div style='margin-bottom:10px;'>".__('In order to use the Mensagia plugin you must configure it first. Correct the following errors:', 'mensagia-woocommerce')."</div>";
        foreach ($messages['errors'] as $errors) {
            echo "<li>".$errors."</li>";
        }
        echo "</div>";
    }

    if (count($messages['success'])) {
        echo "<div class='alert alert-success'>";
        foreach ($messages['success'] as $success) {
            echo "<li>".$success."</li>";
        }
        echo "</div>";
    }


    ?>

    <form id="" class="defaultForm form-horizontal mensagiaprestashop" action="" method="post" enctype="multipart/form-data" autocomplete="off" role="presentation">
        <input type="hidden" name="submitmensagiaprestashop" value="1">

        <div class="panel" id="fieldset_0">

            <div class="panel-heading">
                <i class="fa fa-gear"></i> <?php echo __('Connect your Mensagia account with Wordpress', 'woo-mensagia');?>
            </div>

            <div class="form-wrapper panel-body">

                <?php if (! $this->authenticated) {
        ?>
                    <div class="alert alert-danger"><?php echo __('You must connect your Mensagia account to use this plugin', 'woo-mensagia'); ?></div>
                <?php
    }; ?>

                <div class="form-group">

                    <label class="control-label col-lg-3 required">
                        <?php echo __("Mensagia's email access", 'woo-mensagia');?>
                    </label>

                    <div class="col-lg-9">
                        <input type="text" name="MENSAGIA_LOGIN_EMAIL" id="MENSAGIA_LOGIN_EMAIL" value="<?php echo esc_attr($this->email); ?>" class="" size="40" required="required"   autocomplete="off">
                    </div>

                </div>

                <div class="form-group">

                    <label class="control-label col-lg-3 required">
                        <?php echo __("Mensagia's password access", 'woo-mensagia');?>
                    </label>

                    <div class="col-lg-9">
                        <div class="input-group fixed-width-lg">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            <input type="password" autocomplete="new-password" id="MENSAGIA_LOGIN_PASSWORD" name="MENSAGIA_LOGIN_PASSWORD" class="" value="" required="required" autocomplete="off">
                        </div>
                    </div>

                </div>

                <div class="form-group">

                    <label class="control-label col-lg-3">

                    </label>

                    <div class="col-lg-9">
                        <?php if ($this->authenticated) {
        ?>
                            <span style="color:green;font-weight:bold"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo __("Account correctly connected to Mensagia", 'woo-mensagia'); ?></span><br>
                        <?php
    }; ?>

                    </div>

                </div>


                <?php if ($balance) {
        ?>
                    <div class="form-group">

                        <label class="control-label col-lg-3">
                            <?php echo __("Available balance", 'woo-mensagia'); ?>
                        </label>

                        <div class="col-lg-9">
                            <div class="input-group fixed-width-lg">
                                <div class="balance" style="margin-top:8px;font-weight: bold;"><?php echo $balance; ?></div>
                            </div>
                        </div>

                    </div>
                <?php
    }; ?>

                <div class="form-group">

                    <label class="control-label col-lg-3">

                    </label>

                    <div class="col-lg-9">
                        <div style="margin-top:7px;"><i class="fa fa-angle-double-right" aria-hidden="true"></i> <a target='_blank' href="https://mensagia.com/signup"><?php echo __("Create new account", 'woo-mensagia');?></a></div>
                        <div style="margin-top:5px;"><i class="fa fa-angle-double-right" aria-hidden="true"></i> <a target='_blank' href="https://mensagia.com/mrequests/create?type=1"><?php echo __("Add balance", 'woo-mensagia');?></a></div>
                        <div style="margin-top:5px;"><i class="fa fa-angle-double-right" aria-hidden="true"></i> <a target='_blank' href="https://mensagia.com/forgot_password"><?php echo __("Remember password", 'woo-mensagia');?></a></div>
                    </div>

                </div>


                <div class="form-group">

                    <label class="control-label col-lg-3">

                    </label>

                    <div class="col-lg-9">
                        <button type="submit" value="1" id="configuration_form_submit_btn" name="submitmensagiaprestashop" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> <?php echo __("Save", 'woo-mensagia');?>
                        </button>
                    </div>

                </div>

            </div><!-- /.form-wrapper -->

        </div>
    </form>


    <div class="panel">
        <div class="panel-heading"><i class="fa fa-mobile"></i> <?php echo __("Send API settings and source address", 'woo-mensagia');?></div>
        <div class="panel-body">

            <form action="" method="post" class="defaultForm form-horizontal mensagiaprestashop">


                <?php if ($connected) {
        ?>

                    <?php if (count($api_configurations)) {
            ?>

                        <?php if (!$api_configuration) {
                ?>
                        <div class="alert alert-warning">
                            <b><?php echo __("Attention!", 'woo-mensagia'); ?></b>: <?php echo __("You must choose a source address configuration in order to use SMS Notifications", 'woo-mensagia'); ?>.
                        </div>
                        <?php
            } ?>


                        <div class="form-group">
                            <label class="control-label col-lg-3 required">
                                <?php echo __("Choose configuration to send", 'woo-mensagia'); ?>
                            </label>

                            <div class="col-lg-9">
                                <select name="sapi_configuration" id="sapi_configuration" required style="width:250px !important;">
                                    <option value="" sender_name="-"><?php echo __("Choose a configuration to send...", 'woo-mensagia'); ?></option>

                                    <?php foreach ($api_configurations as $apiconf) {
                ?>

                                    <option sender_name="<?php echo $apiconf['sender_name']; ?>" allow_service_number_alias="<?php echo ($apiconf['allow_service_number_alias']) ? "true": "false"; ?>" <?php if ($apiconf['name'] == $api_configuration) {
                    echo "selected";
                } ?>  value="<?php echo $apiconf['name']; ?>"><?php echo $apiconf['name']; ?></option>
                                    <?php
            } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="control-label col-lg-3">
                                <?php echo __("The source address will be", 'woo-mensagia'); ?>:
                            </label>

                            <div class="col-lg-9">
                                <div class="remitente" style="margin-top:8px;font-weight: bold;"></div>
                            </div>

                        </div>
                    <?php
        } else {
            ?>
                        <div class="alert alert-danger">
                            <b><?php echo __("Attention!", 'woo-mensagia'); ?></b>: <?php echo __("You do not have any configuration to send created in Mensagia. You must create one in:", 'woo-mensagia'); ?> <a target='_blank' href='https://mensagia.com/api/configurations/create'><?php echo __("API shipment settings in Mensagia", 'woo-mensagia'); ?></a>
                        </div>
                    <?php
        } ?>

                <?php
    } else {
        ?>
                    <div class="alert alert-info">
                        <b><?php echo __("Attention!", 'woo-mensagia'); ?></b>: <?php echo __("You must first configure your access to Mensagia before configuring the configuration to send.", 'woo-mensagia'); ?>
                    </div>
                <?php
    } ?>

                <div class="form-group">

                    <label class="control-label col-lg-3">

                    </label>

                    <div class="col-lg-9">
                        <button type="submit" value="1" id="api_configurations" name="api_configurations" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> <?php echo __("Save", 'woo-mensagia');?>
                        </button>
                    </div>

                </div>

        </div>


        </form>


    </div>


    <div class="panel">
        <div class="panel-heading"><i class="fa fa-users"></i> <?php echo __("Shop administrators", 'woo-mensagia');?></div>
        <div class="panel-body bodyForms">

            <?php if (count($admins)) {
        ?>

            <p><?php echo __("Create store administrators to recieve SMS notifications of administrator type.", 'woo-mensagia'); ?></p>

                <table class="table table-hover" width="70%" cellpadding="5" cellspacing="10" align="center">
                    <thead>
                    <tr>
                        <th><b><?php echo __("Name", 'woo-mensagia'); ?></b></th>
                        <th><b><?php echo __("Phone number", 'woo-mensagia'); ?></b></th>
                        <th><b><?php echo __("Delete", 'woo-mensagia'); ?></b></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($admins as $admin) {
            ?>
                    <tr>
                        <td height="50"><?php echo esc_attr($admin->name); ?></td>
                        <td><?php echo esc_attr($admin->number); ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="admin_id" value="<?php echo esc_attr($admin->id); ?>">
                                <button type="submit" class="btn btn-danger btn-delete-admin" name="delete_admins" ><i class="icon-trash"></i> <?php echo __("Delete", 'woo-mensagia'); ?></button>
                            </form>
                        </td>
                    </tr>
                    <?php
        } ?>

                    </tbody>
                </table>

            <?php
    } else {
        ?>
                <div class="alert alert-warning">
                    <b><?php echo __("Attention", 'woo-mensagia'); ?></b>: <?php echo __("You do not have administrators created, you must create at least one to use SMS Notifications.", 'woo-mensagia'); ?>
                </div>

                <p><?php echo __("Create store administrators to recieve SMS notifications of administrator type", 'woo-mensagia'); ?>.</p>
            <?php
    } ?>

            <div class="form-group">

                <label class="control-label col-lg-3">

                </label>

                <div class="col-lg-9">
                    <button id="create_admin" name="create_admin" class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-save"></i> <?php echo __("Create administrator", 'woo-mensagia');?>
                    </button>
                </div>

            </div>

        </div>
    </div>


    <form action="" method="post" class="defaultForm form-horizontal mensagiaprestashop">
        <div class="panel">
            <div class="panel-heading"><i class="fa fa-phone"></i> <?php echo __("Management of international prefixes on mobile numbers", 'woo-mensagia');?></div>
            <div class="panel-body">

                <?php if (!$this->prefix_mode) {
        ?>
                    <div class="alert alert-danger">
                        <b><?php echo __("Attention!", 'woo-mensagia'); ?></b>: <?php echo __("You must choose how we will handle the prefixes of your contact's mobile numbers.", 'woo-mensagia'); ?>
                    </div>
                <?php
    }; ?>


                <?php echo __("In order to send SMS we need numbers to have prefixes. Tell us how we should proceed with your contacts's mobile numbers", 'woo-mensagia');?>:

                <div class="form-group" style="margin-top:30px;">
                    <label class="control-label col-lg-4">
                        <?php echo __("Are the mobile numbers of your contacts stored with the country code?", 'woo-mensagia');?>
                    </label>

                    <div class="col-lg-8">
                        <p class="radio" style="margin-top:0px !important;">
                            <label for="radioNO" style="font-weight:bold;"><input type="radio" id="radioNO" name="prefix_mode" class="" <?php if ($this->prefix_mode == 'check_prefixs') {
        echo "checked";
    }?> value="check_prefixs"><?php echo __("It is not being verified that they keep the mobile number with a prefix. If the mobile number has no prefix, add the prefix of the country of the contact.", 'woo-mensagia');?></label> <br>
                        </p>
                        <p class="radio">
                            <label for="radioSI" style="font-weight:bold;"><input type="radio" id="radioSI" name="prefix_mode" class="" <?php if ($this->prefix_mode == 'with_prefixs') {
        echo "checked";
    }?> value="with_prefixs"> <?php echo __("Yes, all numbers are stored with their corresponding country code.", 'woo-mensagia');?></label>
                        </p>
                    </div>
                </div>


                <div class="form-group">

                    <label class="control-label col-lg-3">

                    </label>

                    <div class="col-lg-9">
                        <button type="submit" value="1" id="prefix_mode" name="prefix_mode_submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> <?php echo __("Save", 'woo-mensagia');?>
                        </button>
                    </div>

                </div>

            </div>

        </div>

    </form>



    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="icon-user"></i> <?php echo __("Create administrator", 'woo-mensagia');?></h4>
                </div>

                <form action="" method="post">

                    <div class="modal-body">

                        <div class="form-group row">
                            <label class="control-label col-lg-4 required" label-for="admin_name">
                                <?php echo __("Name", 'woo-mensagia');?>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" name="admin_name" id="admin_name" value="" class="required" size="40" required="required">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-lg-4 required" label-for="admin_name">
                                <?php echo __("Phone number", 'woo-mensagia');?>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" name="admin_mobile" id="admin_mobile" value="" class="required" size="40" required="required">
                                <span id="helpBlock" class="help-block"><?php echo __("Add the number with a prefix, without the + sign and without spaces.", 'woo-mensagia');?></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __("Cancel", 'woo-mensagia');?></button>
                        <button type="submit" class="btn btn-primary btn-save-admins" name="create_admins"><?php echo __("Save", 'woo-mensagia');?></button>
                    </div>

                </form>

            </div>
        </div>
    </div>


</div>


<?php require_once "partials/logoFooter.php"?>

<script>
    var no_dinamic_sender  = '<?php echo __("The sending configuration chosen does not allow dynamic senders.", 'woo-mensagia');?>';
</script>
