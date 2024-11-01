<div style="margin-top:20px;margin-bottom:20px;">

    <div class="row">
        <div class="col-md-6">

            <?php foreach ($languages as $lang) { ?>
                <textarea name="textarea_<?php echo $name; ?>" class="textarea_<?php echo $name; ?> textarea_<?php echo $name; ?>" lang_id="<?php echo $curr['id']; ?>" id="textarea_<?php echo $name; ?>" cols="10" rows="5" class="form-group" textOld="<?php echo (isset($notificationsLangArray[$curr['id']])) ? esc_textarea($notificationsLangArray[$curr['id']]) : "";?>" style="width: 90% !important;"><?php echo (isset ($notificationsLangArray[$curr['id']])) ? esc_textarea($notificationsLangArray[$curr['id']]) : "";?></textarea>
            <?php } ?>

            <div style="margin-top:10px;"><b><?php echo __('SMS example', 'woo-mensagia');?>: </b><br/><span class="textTransform"></span></div>

        </div>
        <div class="col-md-6">
            <div style="width:200px;height: 200px;overflow-y:scroll;border:1px solid #ddd;text-align: left;">
                <?php
                if ($name == 'h_admin_actionCustomerAccountAdd' or $name == 'h_customer_actionCustomerAccountAdd') {
                    //{include file="./vars.tpl" customer_basic=true customer_all=false pedido=false tienda=true employees=false productos=false }
                } elseif ($name == 'h_admin_deletedProduct') {
                    $customer_basic = false;
                    $customer_all   = false;
                    $pedido         = false;
                    $tienda         = true;
                    $productos      = true;
                    $refunds        = false;
                } elseif ($name == 'h_customer_orderRefunded' or $name == 'h_admin_orderRefunded') {
                    $customer_basic = true;
                    $customer_all   = true;
                    $pedido         = true;
                    $tienda         = true;
                    $productos      = false;
                    $refunds        = true;
                } else {
                    $customer_basic = true;
                    $customer_all   = true;
                    $pedido         = true;
                    $tienda         = true;
                    $productos      = false;
                    $refunds        = false;
                }

                require(plugin_dir_path( __FILE__ ).'/vars.php');
                ?>
            </div>
        </div>
    </div>

    <br>

    <span class="btn btn-success btn-save"  group="<?php echo $name; ?>"><img class='loaderImg' src="<?php echo plugins_url( 'img/ajax-loader.gif', dirname ( dirname ( __FILE__ ) ) );?>" style="display:none;"/> <?php echo __('Save', 'woo-mensagia');?></span>
    <span class="btn btn-default btn-close" related="<?php echo $name; ?>"><?php echo __('Close', 'woo-mensagia');?></span>

    <span class="saved_ok" style="color:green;font-weight:bold;display:none;">&nbsp;&nbsp;&nbsp;<?php echo __('Saved successfully', 'woo-mensagia');?></span>
    <span class="saved_ko" style="color:red;font-weight:bold;display:none;">&nbsp;&nbsp;&nbsp;<?php echo __('Error saving data', 'woo-mensagia');?></span>
</div>


<br/>