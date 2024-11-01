<div class='wrap'>
    <h2><?php echo __('SMS Notifications', 'woo-mensagia');?></h2>
</div>


<div class="bootstrap-wrapper">
    <div class="panel">

        <div class="panel-heading"><i class="fa fa-gear"></i> <?php echo __('Order Status', 'woo-mensagia');?></div>
        <div class="panel-body bodyForms">

            <ul class="nav nav-tabs">
                <li role="presentation" class="active" ><a href="#" mustOpen="notif_estado_pedido_cliente" mustOpenClass="notif_estado_pedido" class="toogleTypeNotification"><?php echo __('Customer notifications', 'woo-mensagia');?></a></li>
                <li role="presentation"><a href="#" mustOpen="notif_estado_pedido_admin" mustOpenClass="notif_estado_pedido" class="toogleTypeNotification"><?php echo __('Administrators notifications', 'woo-mensagia');?></a></li>
            </ul>

            <div id="notif_estado_pedido_admin" class='notif_estado_pedido' style="border:1px solid #ddd; padding:20px;background:#fff;display:none;">

                <h4><?php echo __('Administrators: Order status', 'woo-mensagia');?></h4>

                <table class="table smsNotificationsTable" width="70%" cellpadding="5" cellspacing="10" align="center">
                    <tbody>

                    <?php foreach ($hooksOrderStatesAdmin as $curr) {
                    ?>

                        <tr id="t_admin_orderStatusChanged_<?php echo $curr['option_name'];?>" height="60px" class="trvisible">
                            <td width="500"><?php echo $orderStates[$curr['option_name']];?></td>
                            <td><input type="checkbox" class='toggleNotification orderStatusChanged' name="admin_orderStatusChanged_<?php echo $curr['option_name'];?>" id="admin_orderStatusChanged_<?php echo $curr['option_name'];?>" <?php echo ($sMSNotifications['admin_orderStatusChanged_'.$curr['option_name']]) ? "checked" : "";?>  htype="admin" hook="orderStatusChanged" hid="<?php echo $curr['option_name'];?>" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_admin_orderStatusChanged_<?php echo $curr['option_name'];?>" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia'); ?></span></td>
                        </tr>

                        <tr id="h_admin_orderStatusChanged_<?php echo $curr['option_name'];?>" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_admin_orderStatusChanged_".$curr['option_name'];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>

                <br><h4><?php echo __('Administrators: Orders', 'woo-mensagia');?></h4>

                <table class="table smsNotificationsTable" width="70%" cellpadding="5" cellspacing="10" align="center">

                    <tbody>

                        <tr id="t_admin_newOrderHook" height="60px" class="trvisible">
                            <td width="500"><?php echo __('New order', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification newOrderHook' name="admin_newOrderHook" id="admin_newOrderHook" <?php echo ($sMSNotifications['admin_newOrderHook']) ? "checked" : "";?>  htype="admin" hook="newOrderHook" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_admin_newOrderHook" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_admin_newOrderHook" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_admin_newOrderHook";
                                $curr = ['id' => $idsNamesNotifications['admin_newOrderHook']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>



                        <tr id="t_admin_paymentCompletedHook" height="60px" class="trvisible">
                            <td width="500"><?php echo __('Payment completed', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification paymentCompletedHook' name="admin_paymentCompletedHook" id="admin_paymentCompletedHook" <?php echo ($sMSNotifications['admin_paymentCompletedHook']) ? "checked" : "";?>  htype="admin" hook="paymentCompletedHook" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_admin_paymentCompletedHook" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_admin_paymentCompletedHook" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_admin_paymentCompletedHook";
                                $curr = ['id' => $idsNamesNotifications['admin_paymentCompletedHook']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>


                        <tr id="t_admin_orderRefunded" height="60px" class="trvisible">
                            <td width="500"><?php echo __('Refund completed', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification orderRefunded' name="admin_orderRefunded" id="admin_orderRefunded" <?php echo ($sMSNotifications['admin_orderRefunded']) ? "checked" : "";?>  htype="admin" hook="orderRefunded" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_admin_orderRefunded" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_admin_orderRefunded" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_admin_orderRefunded";
                                $curr = ['id' => $idsNamesNotifications['admin_orderRefunded']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>

                    </tbody>

                </table>


                <br><h4><?php echo __('Administrators: Products', 'woo-mensagia');?></h4>

                <table class="table smsNotificationsTable" width="70%" cellpadding="5" cellspacing="10" align="center">

                    <tbody>

                        <tr id="t_admin_deletedProduct" height="60px" class="trvisible">
                            <td width="500"><?php echo __('Product deleted', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification deletedProduct' name="admin_deletedProduct" id="admin_deletedProduct" <?php echo ($sMSNotifications['admin_deletedProduct']) ? "checked" : "";?>  htype="admin" hook="deletedProduct" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_admin_deletedProduct" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_admin_deletedProduct" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_admin_deletedProduct";
                                $curr = ['id' => $idsNamesNotifications['admin_deletedProduct']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>
                    
                    </tbody>

                </table>

            </div>

            <div id="notif_estado_pedido_cliente" class='notif_estado_pedido' style="border:1px solid #ddd; padding:20px;background:#fff;">

                <h4><?php echo __('Customers: Order status', 'woo-mensagia');?></h4>

                <table class="table smsNotificationsTable" width="70%" cellpadding="5" cellspacing="10" align="center">
                    <tbody>

                    <?php foreach ($hooksOrderStatesCustomer as $curr) {
                                    ?>

                        <tr id="t_customer_orderStatusChanged_<?php echo $curr['option_name'];?>" height="60px" class="trvisible">
                            <td width="500"><?php echo $orderStates[$curr['option_name']];?></td>
                            <td><input type="checkbox" class='toggleNotification orderStatusChanged' name="customer_orderStatusChanged_<?php echo $curr['option_name'];?>" id="customer_orderStatusChanged_<?php echo $curr['option_name'];?>" <?php echo ($sMSNotifications['customer_orderStatusChanged_'.$curr['option_name']]) ? "checked" : "";?>  htype="customer" hook="orderStatusChanged" hid="<?php echo $curr['option_name'];?>" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_customer_orderStatusChanged_<?php echo $curr['option_name'];?>" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia'); ?></span></td>
                        </tr>

                        <tr id="h_customer_orderStatusChanged_<?php echo $curr['option_name'];?>" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_customer_orderStatusChanged_".$curr['option_name'];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>
                    <?php
                                } ?>
                    </tbody>

                </table>

                <br><h4><?php echo __('Customers: Orders', 'woo-mensagia');?></h4>

                <table class="table smsNotificationsTable" width="70%" cellpadding="5" cellspacing="10" align="center">

                    <tbody>

                        <tr id="t_customer_newOrderHook" height="60px" class="trvisible">
                            <td width="500"><?php echo __('New order', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification newOrderHook' name="customer_newOrderHook" id="customer_newOrderHook" <?php echo ($sMSNotifications['customer_newOrderHook']) ? "checked" : "";?>  htype="customer" hook="newOrderHook" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_customer_newOrderHook" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_customer_newOrderHook" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_customer_newOrderHook";
                                $curr = ['id' => $idsNamesNotifications['customer_newOrderHook']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>


                        <tr id="t_customer_paymentCompletedHook" height="60px" class="trvisible">
                            <td width="500"><?php echo __('Payment completed', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification paymentCompletedHook' name="customer_paymentCompletedHook" id="customer_paymentCompletedHook" <?php echo ($sMSNotifications['customer_paymentCompletedHook']) ? "checked" : "";?>  htype="customer" hook="paymentCompletedHook" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_customer_paymentCompletedHook" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_customer_paymentCompletedHook" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_customer_paymentCompletedHook";
                                $curr = ['id' => $idsNamesNotifications['customer_paymentCompletedHook']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>

                        <tr id="t_customer_orderRefunded" height="60px" class="trvisible">
                            <td width="500"><?php echo __('Refund completed', 'woo-mensagia');?></td>
                            <td><input type="checkbox" class='toggleNotification orderRefunded' name="customer_orderRefunded" id="customer_orderRefunded" <?php echo ($sMSNotifications['customer_orderRefunded']) ? "checked" : "";?>  htype="customer" hook="orderRefunded" hid="" data-onstyle="success"></td>
                            <td><span class="openMsgSelector" style="text-decoration:underline;" opened="false" related="h_customer_orderRefunded" ><i class="fa fa-edit" aria-hidden="true"></i> <?php echo __('Edit notification', 'woo-mensagia');?></span></td>
                        </tr>

                        <tr id="h_customer_orderRefunded" style="display:none;">
                            <td colspan="3">
                                <?php
                                $languages = [$default_lang];
                                $name = "h_customer_orderRefunded";
                                $curr = ['id' => $idsNamesNotifications['customer_orderRefunded']];
                                require(plugin_dir_path( __FILE__ ).'partials/textarea.php');
                                ?>
                            </td>
                        </tr>                    

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>



<?php require_once "partials/modalDemo.php"?>
<?php require_once "partials/logoFooter.php"?>

<script type="text/javascript">
    var ajax_url                    = "<?php echo admin_url('admin-ajax.php'); ?>";
    var notificacion_activada       = '<?php echo __('Notification ACTIVATED', 'woo-mensagia');?>';
    var notificacion_desactivada    = '<?php echo __('Notification disabled', 'woo-mensagia');?>';

    var example = [];
    example['{shop_name}']    = "MyShop";
    example['{shop_domain}']  = "www.myshop.com";
    example['{shop_email}']   = "myshop@shop.com";

    example['{customer_firstname}']   = "John";
    example['{customer_lastname}']    = "Smith";
    example['{customer_email}']       = "jsmith@example.com";


    example['{customer_company}']      = "Smith Company";
    example['{customer_address1}']     = "Boulevard St. 12";
    example['{customer_address2}']     = "Atico 3-1";
    example['{customer_postcode}']     = "08080";
    example['{customer_city}']         = "Girona";
    example['{customer_country}']      = "Spain";
    example['{customer_state}']        = "GI";
    example['{customer_phone}']        = "34687483784";

    example['{order_id}']             = "16";
    example['{order_total_paid}']     = "34.34";
    example['{order_currency}']       = "EUR";
    example['{order_data_es}']        = "05-03-2017 12:21:00";
    example['{order_data_en}']        = "2017-03-05 12:21:00";
    example['{order_payment_method}'] = "Pagos por cheque";

    example['{total_refunded}']             = "14.54";
    example['{order_currency_refunded}']    = "EUR";

    example['{product_id}']                 = "34";
    example['{product_name}']               = "Air Shoes Red";
    example['{product_created_date_es}']    = "05-09-2017 12:21:00";
    example['{product_created_date_en}']    = "2017-09-05 12:21:00";
</script>
