<table class="table table_vars" style="width:100%" cellspacing="10">
    <?php if ($customer_basic == true or $customer_all == true) {
    ?>
        <thead><tr><th><?php echo __('Customer variables', 'mensagia-woocommerce'); ?></th></tr></thead>
        <tbody>
    <?php
} ?>

    <?php if ($customer_basic == true) {
        ?>
        <tr><td var="">{customer_firstname}</td></tr>
        <tr><td var="">{customer_lastname}</td></tr>
        <tr><td var="">{customer_email}</td></tr>
    <?php
    } ?>

    <?php if ($customer_all == true) {
        ?>
        <tr><td var="">{customer_company}</td></tr>
        <tr><td var="">{customer_address1}</td></tr>
        <tr><td var="">{customer_address2}</td></tr>
        <tr><td var="">{customer_postcode}</td></tr>
        <tr><td var="">{customer_city}</td></tr>
        <tr><td var="">{customer_country}</td></tr>
        <tr><td var="">{customer_state}</td></tr>
        <tr><td var="">{customer_phone}</td></tr>
    <?php
    } ?>

    <?php if ($customer_basic == true or $customer_all == true) {
        ?>
        </tbody>
    <?php
    } ?>


    <?php if ($tienda == true) {
        ?>
        <thead><tr><th><?php echo __('Store variables', 'woo-mensagia'); ?></th></tr></thead>
        <tbody>
            <tr><td var="">{shop_name}</td></tr>
            <tr><td var="">{shop_domain}</td></tr>
            <tr><td var="">{shop_email}</td></tr>
        </tbody>
    <?php
    } ?>

    <?php if ($pedido == true) {
        ?>
        <thead><tr><th><?php echo __('Order variables', 'woo-mensagia'); ?></th></tr></thead>
        <tbody>
            <tr><td var="">{order_id}</td></tr>
            <tr><td var="">{order_total_paid}</td></tr>
            <tr><td var="">{order_currency}</td></tr>
            <tr><td var="">{order_data_es}</td></tr>
            <tr><td var="">{order_data_en}</td></tr>
            <tr><td var="">{order_payment_method}</td></tr>
        </tbody>
    <?php
    } ?>

    <?php if ($productos == true) {
        ?>
        <thead><tr><th><?php echo __('Product variables', 'woo-mensagia'); ?></th></tr></thead>
        <tbody>
            <tr><td var="">{product_id}</td></tr>
            <tr><td var="">{product_name}</td></tr>
            <tr><td var="">{product_created_date_es}</td></tr>
            <tr><td var="">{product_created_date_en}</td></tr>
        </tbody>
    <?php
    } ?>


    <?php if ($refunds == true) {
        ?>
        <thead><tr><th><?php echo __('Refunds', 'woo-mensagia'); ?></th></tr></thead>
        <tbody>
        <tr><td var="">{total_refunded}</td></tr>
        <tr><td var="">{order_currency_refunded}</td></tr>
        </tbody>
    <?php
    } ?>

</table>