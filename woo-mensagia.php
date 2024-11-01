<?php
/*
Plugin Name: Mensagia for WooCommerce
Plugin URI:  https://mensagia.com/plugin-sms
Description: Connect your ecommerce with Mensagia to create hypersegmented campaigns and send automatic notifications through SMS or Email Marketing.
Version:     1.6
Author:      Sinermedia
Author URI:  http://sinermedia.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: woo-mensagia
Domain Path: /languages
*/
defined('ABSPATH') or die('No script kiddies please!');

global $mensagiasms_jal_db_version;
$mensagiasms_jal_db_version = '1.0';

require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaSDK.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaPrestashopExport.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaSMSNotification.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaInstall.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaAdmin.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaHooks.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaCountry.php');
require_once(plugin_dir_path( __FILE__ ).'classes/MensagiaWPAdminNotices.php');
require_once(ABSPATH . 'wp-admin/includes/plugin.php');


class WooMensagia
{
    private static $instance;

    private $mensagiaSDK;
    private $mensagiaHooks;
    private $version;

    public $email;
    public $password;
    public $authenticated;
    public $prefix_mode;
    public $api_configuration;
    public $demo;
    public $default_language;

    /**
     * MensagiaWoocomerce constructor.
     */
    public function __construct()
    {
        $this->version  = "1.6";
        $this->demo     = false;

        $this->mensagiaSDK = new MensagiaSDK();
        $this->mensagiaHooks = new MensagiaHooks();

        $this->email             = get_option('MENSAGIA_LOGIN_EMAIL');
        $this->password          = get_option('MENSAGIA_LOGIN_PASSWORD');
        $this->authenticated     = get_option('MENSAGIA_AUTHENTICATED');
        $this->prefix_mode       = get_option('MENSAGIA_PREFIX_MODE');
        $this->api_configuration = get_option('MENSAGIA_API_CONFIGURATION');

        $this->default_language  = $this->get_short_language();

        register_activation_hook(__FILE__, 'mensagiasms_jal_install');
        register_activation_hook(__FILE__, 'mensagiasms_jal_install_data');


        // Is WooCommerce Active?
        if (! is_plugin_active('woocommerce/woocommerce.php')) {
            if (is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins([plugin_basename(__FILE__)]);
            }

            $error_message = __('The Mensagia plugin only works if  <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> is installed and actived. Please, install/active it to use the Mensagia plugin.', 'woo-mensagia');

            exit($error_message);
        }
    }

    public static function getInstance()
    {
        load_plugin_textdomain('woo-mensagia', false, basename(dirname(__FILE__)) . '/languages');

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function mensagiasms_pluginMenu()
    {
        add_menu_page(
            __('Mensagia', 'woo-mensagia'),
            __('Mensagia', 'woo-mensagia'),
            'manage_mensagia',
            __FILE__,
            null,
            'dashicons-icon-m-mensagia',
            56
        );

        add_submenu_page(
            __FILE__,
            __('SMS Notifications', 'woo-mensagia'),
            __('SMS Notifications', 'woo-mensagia'),
            'manage_options',
            '/woo-mensagia-sms-notifications',
            array($this, 'mensagiasms_renderSmsNotifications')
        );

        add_submenu_page(
            __FILE__,
            __('Export to Mensagia', 'woo-mensagia'),
            __('Export to Mensagia', 'woo-mensagia'),
            'manage_options',
            '/woo-mensagia-export',
            array($this, 'mensagiasms_renderExport')
        );

        add_submenu_page(
            __FILE__,
            __('Configuration', 'woo-mensagia'),
            __('Configuration', 'woo-mensagia'),
            'manage_options',
            '/woo-mensagia-configuration',
            array($this, 'mensagiasms_renderConfiguration')
        );
    }

    private function postRenderConfigurationPrefixes($post)
    {
        if ($this->demo) {
            $messages['errors'][] = __('Demo data can not be modified');
            return $messages;
        }

        $prefix_mode = (string) sanitize_text_field($post['prefix_mode']);

        $messages['errors']   = [];
        $messages['success']  = [];

        if ($prefix_mode == 'check_prefixs' or $prefix_mode == 'with_prefixs') {
            update_option('MENSAGIA_PREFIX_MODE', $prefix_mode);
            $this->prefix_mode = $prefix_mode;

            $messages['success'][] = __('Prefix mode saved succesfully', 'woo-mensagia');
        } else {
            $messages['errors'][] = __('Error saving prefix mode', 'woo-mensagia');
        }

        return $messages;
    }


    private function postRenderConfigurationAccount($post)
    {
        if ($this->demo) {
            $messages['errors'][] = __('Demo data can not be modified');
            return $messages;
        }

        $email    = (string) sanitize_email($post['MENSAGIA_LOGIN_EMAIL']);
        $password = (string) sanitize_text_field($post['MENSAGIA_LOGIN_PASSWORD']);

        $messages['errors']   = [];
        $messages['success']  = [];

        if (!$email || empty($email)) {
            $messages['errors'][] = __('You must enter a valid email address', 'woo-mensagia');
        } elseif (!$password || empty($password)) {
            $messages['errors'][] = __('You must enter a valid password', 'woo-mensagia');
        } else {
            $auth = $this->mensagiaSDK->authenticate($email, $password);

            update_option('MENSAGIA_LOGIN_EMAIL', $email);
            update_option('MENSAGIA_LOGIN_PASSWORD', $password);
            update_option('MENSAGIA_API_CONFIGURATION', null);

            $this->email    = $email;
            $this->password = $password;

            if ($auth['result'] == 'success') {
                update_option('MENSAGIA_AUTHENTICATED', true);
                $this->authenticated = true;
                $messages['success'][] = __('Account connected!', 'woo-mensagia');
            } else {
                update_option('MENSAGIA_AUTHENTICATED', false);
                $this->authenticated = false;
                $messages['errors'][] = __('The access data does not correspond to any active Mensagia account.', 'woo-mensagia');
            }
        }

        return $messages;
    }

    private function postRenderConfigurationAdmins($post)
    {
        if ($this->demo) {
            $messages['errors'][] = __('Demo data can not be modified');
            return $messages;
        }

        $admin_name    = (string) sanitize_text_field($post['admin_name']);
        $admin_mobile  = (integer) sanitize_text_field($post['admin_mobile']);

        $messages['errors']   = [];
        $messages['success']  = [];

        if ($admin_name && $admin_mobile) {
            if (! MensagiaAdmin::exists($admin_mobile)) {
                $result = MensagiaAdmin::create($admin_name, $admin_mobile);

                if ($result) {
                    $messages['success'][] = __('Administrator created correctly.', 'woo-mensagia');
                } else {
                    $messages['errors'][] = __('Error creating administrator.', 'woo-mensagia');
                }
            } else {
                $messages['errors'][] = __('An administrator already exists with this number.', 'woo-mensagia');
            }
        } else {
            $messages['errors'][] = __('Incorrect data.', 'woo-mensagia');
        }

        return $messages;
    }


    private function postRenderConfigurationDeleteAdmins($post)
    {
        if ($this->demo) {
            $messages['errors'][] = __('Demo data can not be modified');
            return $messages;
        }

        $messages['errors']   = [];
        $messages['success']  = [];

        $admin_id    = (integer) sanitize_text_field($post['admin_id']);

        if ($admin_id) {
            $result = MensagiaAdmin::remove($admin_id);

            if ($result) {
                $messages['success'][] = __('Administrator successfully deleted.', 'woo-mensagia');
            } else {
                $messages['errors'][] = __('Error removing administrator.', 'woo-mensagia');
            }
        }

        return $messages;
    }


    private function postRenderConfigurationApiConfigurations($post)
    {
        if ($this->demo) {
            $messages['errors'][] = __('Demo data can not be modified');
            return $messages;
        }

        $messages['errors']   = [];
        $messages['success']  = [];

        $api_configuration   = (string) sanitize_text_field($post['sapi_configuration']);

        if ($api_configuration) {
            update_option('MENSAGIA_API_CONFIGURATION', $api_configuration);

            $messages['success'][] = __('The delivery settings have been configured correctly.', 'woo-mensagia');
        } else {
            $messages['errors'][] = __('You have not chosen any delivery settings.', 'woo-mensagia');
        }



        return $messages;
    }


    public function mensagiasms_renderConfiguration()
    {
        if (isset($_POST)) {
            if (!empty($_POST)) {
                if (isset($_POST['submitmensagiaprestashop'])) {
                    $messages = $this->postRenderConfigurationAccount($_POST);
                }

                if (isset($_POST['prefix_mode_submit'])) {
                    $messages =  $this->postRenderConfigurationPrefixes($_POST);
                }

                if (isset($_POST['create_admins'])) {
                    $messages =  $this->postRenderConfigurationAdmins($_POST);
                }

                if (isset($_POST['delete_admins'])) {
                    $messages =  $this->postRenderConfigurationDeleteAdmins($_POST);
                }

                if (isset($_POST['sapi_configuration'])) {
                    $messages =  $this->postRenderConfigurationApiConfigurations($_POST);
                }
            }
        }

        if (!isset($messages['errors'])) {
            $messages['errors'] = [];
        }

        if (!isset($messages['success'])) {
            $messages['success'] = [];
        }

        $balance = null;

        $connected         = (bool) get_option('MENSAGIA_AUTHENTICATED');
        $api_configuration = (string) get_option('MENSAGIA_API_CONFIGURATION');

        if ($this->authenticated) {
            $this->mensagiaSDK->authenticate($this->email, $this->password);
            $api_configurations = $this->mensagiaSDK->getApiConfigurations();
            $balance = $this->mensagiaSDK->getBalance();

            if (isset($api_configurations['data'])) {
                if ($api_configurations['meta']['pagination']['total'] > 0) {
                    $api_configurations =  $api_configurations['data'];
                } else {
                    $api_configurations = null;
                    array_push($messages['errors'], __('You must create a delivery setting in Mensagia.', 'woo-mensagia'));
                }
            } else {
                $api_configurations = null;
                array_push($messages['errors'], __('You must choose a delivery setting in Mensagia.', 'woo-mensagia'));
            }

            if (isset($balance['data'])) {
                $balance = number_format(
                        $balance['data']['balance'],
                        4,
                        ',',
                        '.'
                    ) . $balance['data']['currency'];
            }
        } else {
            $api_configurations = null;

            array_push($messages['errors'], __('You must sign in your Mensagia account.', 'woo-mensagia'));
        }

        // Admins
        $admins = MensagiaAdmin::getAdmins();

        // mostramos mensaje de plugin correctamente configurado
        if ($this->authenticated and empty($messages['errors'])) {
            $messages['success'][] =__('The Mensagia plugin is correctly configured!', 'woo-mensagia');
        }

        // styles && scripts
        wp_enqueue_script('bootstrapjs', plugins_url('scripts/bootstrap.js', __FILE__), false, $this->version, false);
        wp_enqueue_style('bootstrapcss', plugins_url('css/bootstrap.css', __FILE__), false, $this->version, false);
        wp_enqueue_style('customstyles', plugins_url('css/styles.css', __FILE__), false, $this->version, false);
        wp_enqueue_style('fontawesome', 'http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', false, $this->version, false);
        wp_enqueue_script('jquery', "https://code.jquery.com/jquery-1.12.4.min.js", false, $this->version, false);
        wp_enqueue_script('configurationjs', plugins_url('scripts/configuration.js', __FILE__), false, $this->version, false);

        // render view
        require_once(plugin_dir_path( __FILE__ ).'/views/configuration.php');
    }


    public function mensagiasms_renderSmsNotifications()
    {
        if (!$this->authenticated or !MensagiaAdmin::countAdmins() or !(bool) get_option('MENSAGIA_PREFIX_MODE') or
            !(bool) get_option('MENSAGIA_API_CONFIGURATION')) {
            wp_redirect("/wp-admin/admin.php?page=woo-mensagia-configuration");
        }

        global $wpdb;
        global $wp_roles;

        $name         = get_bloginfo('name');
        $date_prefix  = date('Y_m_d_His');
        $default_lang = substr(get_bloginfo('language'), 0, 2);
        $orderStates  = wc_get_order_statuses();

        $hooksOrderStatesCustomer = MensagiaSMSNotification::getHooks(
            'customer',
            'orderStatusChanged'
        );
        $hooksOrderStatesAdmin    = MensagiaSMSNotification::getHooks(
            'admin',
            'orderStatusChanged'
        );

        $sMSNotifications = MensagiaSMSNotification::getNotifications();
        $sMSNotificationsList = $sMSNotifications;

        //var_dump($sMSNotifications);die();

        $sMSNotifications = MensagiaSMSNotification::transformNotificationsToCheck($sMSNotifications);
        $sMSNotificationsLang = MensagiaSMSNotification::getAllMessagesNotifications();

        // transform $sMSNotificationsLang
        $notificationsLangArray = array();

        if ($sMSNotificationsLang) {
            foreach ($sMSNotificationsLang as $lang) {
                $notificationsLangArray[$lang['mensagia_sms_notification_id']] = $lang['message'];
            }
        }

        $idsNamesNotifications = array();

        if ($sMSNotificationsList) {
            foreach ($sMSNotificationsList as $notification) {
                $idsNamesNotifications[$notification['type']."_".$notification['hook']] = $notification['id'];
            }
        }

        // styles && scripts

        wp_enqueue_script('jquery');
        wp_enqueue_script('boostraptooglejs', 'https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js', false, $this->version, false);
        wp_enqueue_style('boostraptooglecss', 'https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css', false, $this->version, false);
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', false, $this->version, false);
        wp_enqueue_script('bootstrapjs', plugins_url('scripts/bootstrap.js', __FILE__), false, $this->version, false);
        wp_enqueue_script('smsnotificationsjs', plugins_url('scripts/sms_notifications.js', __FILE__), false, $this->version, false);
        wp_enqueue_style('bootstrapcss', plugins_url('css/bootstrap.css', __FILE__), false, $this->version, false);
        wp_enqueue_style('customstyles', plugins_url('css/styles.css', __FILE__), false, $this->version, false);
        wp_enqueue_style('fontawesome', 'http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', false, $this->version, false);

        wp_localize_script('exportjs', 'ajax_object',
            array( 'ajax_url' => admin_url('admin-ajax.php'), 'we_value' => 1234 ));

        // render view
        require_once(plugin_dir_path( __FILE__ ).'/views/sms_notifications.php');
    }


    public function mensagiasms_renderExport()
    {
        if (!$this->authenticated or !(bool) get_option('MENSAGIA_PREFIX_MODE')) {
            wp_redirect("/wp-admin/admin.php?page=woo-mensagia-configuration");
        }

        global $wpdb;
        global $wp_roles;

        $name = get_bloginfo('name');
        $date_prefix = date('Y_m_d_His');

        // styles && scripts

        wp_enqueue_script('jquery');
        wp_enqueue_script('bootstrapjs', plugins_url('scripts/bootstrap.js', __FILE__), false, $this->version, false);
        wp_enqueue_script('exportjs', plugins_url('scripts/export.js', __FILE__), false, $this->version, false);
        wp_enqueue_style('bootstrapcss', plugins_url('css/bootstrap.css', __FILE__), false, $this->version, false);
        wp_enqueue_style('customstyles', plugins_url('css/styles.css', __FILE__), false, $this->version, false);
        wp_enqueue_style('fontawesome', 'http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', false, $this->version, false);

        wp_localize_script('exportjs', 'ajax_object',
            array( 'ajax_url' => admin_url('admin-ajax.php') ));

        // render view
        require_once(plugin_dir_path( __FILE__ ).'/views/export.php');
    }

    public function mensagiasms_initPlugin()
    {
        add_action('admin_menu', array($this, 'mensagiasms_pluginMenu'));
        add_action('wp_ajax_export_request', 'mensagiasms_ajax_export_request');
        add_action('wp_ajax_notifications_request', 'mensagiasms_ajax_notifications_request');
        add_action('woocommerce_order_status_changed', 'mensagiasms_orderStatusChanged', 10, 10);
        add_action('admin_notices', 'mensagiasms_wp_admin_notices');
        add_action('woocommerce_thankyou', 'mensagiasms_newOrderHook', 10);
        add_action('woocommerce_payment_complete', 'mensagiasms_paymentCompletedHook', 10);
        add_action('woocommerce_order_refunded', 'mensagiasms_orderRefunded', 10, 10);
        add_action('before_delete_post', 'mensagiasms_deleteProductHook', 10, 10);
        add_action('admin_enqueue_scripts', 'mensagiasms_my_scripts' );
    }


    public function get_short_language()
    {
        return substr(get_bloginfo('language'), 0, 2);
    }
}


$mensagia = WooMensagia::getInstance();
$mensagia->mensagiasms_initPlugin();


function mensagiasms_ajax_notifications_request()
{
    if (isset($_POST)) {
        if (!empty($_POST)) {
            if (isset($_POST['type_req'])) {
                $data['action'] = (string) sanitize_text_field($_POST['type_req']);

                switch ($data['action']) {
                    case 'toggleNotification':
                        $data['hook']   = (string) sanitize_text_field($_POST['hook']);
                        $data['htype']  = (string) sanitize_text_field($_POST['htype']);
                        $data['hid']    = (string) sanitize_text_field($_POST['hid']);
                        $active_post = (int) $_POST['active'];
                        $data['active'] = ($active_post === 1) ? 1 : 0;
                        echo MensagiaSMSNotification::updateNotification($data);
                    break;
                    case 'saveTextNotification':
                        // JSON sanitize inside the method
                        echo MensagiaSMSNotification::updateTextNotifications(['json' => $_POST['json']]);
                    break;
                }
            }
        }
    }

    wp_die();
}


function mensagiasms_ajax_export_request()
{
    if (isset($_POST)) {
        if (!empty($_POST)) {
            if (isset($_POST['type_req'])) {
                $action     = (string) sanitize_text_field($_POST['type_req']);
                // JSON sanitize inside the method
                $options    = $_POST['options'];

                $email             = get_option('MENSAGIA_LOGIN_EMAIL');
                $password          = get_option('MENSAGIA_LOGIN_PASSWORD');

                $export = new MensagiaPrestashopExport($email, $password, $options);

                switch ($action) {
                    case 'export_init':
                        echo $export->exportInit();
                        break;

                    case 'delete_agendas':
                        echo $export->deleteAgendas();
                        break;

                    case 'create_agendas':
                        echo $export->createAgendas();
                        break;

                    case 'check_extrafields':
                        echo $export->checkExtrafields();
                        break;

                    case 'export_groups':
                        echo $export->exportGroups();
                        break;

                    case 'waiting_finish_processes':
                        echo $export->waitingFinishProcesses();
                        break;

                    case 'export_finished':
                        echo $export->exportFinished();
                        break;

                    default:
                        break;
                }
            }
        }
    }

    wp_die();
}

function mensagiasms_orderStatusChanged($id, $from, $to, $wooOrderObject)
{
    $mensagiaHooks = new MensagiaHooks();
    $order = new WC_Order($id);

    // customer
    $hookCustomer = MensagiaSMSNotification::isHookEnabled('orderStatusChanged', 'customer', 'wc-'.$to);

    if ($hookCustomer) {
        $result = $mensagiaHooks->sendNotification($order, $hookCustomer, 'customer', $order);
    }

    // admin
    $hookAdmin = MensagiaSMSNotification::isHookEnabled('orderStatusChanged', 'admin', 'wc-'.$to);

    if ($hookAdmin) {
        $result = $mensagiaHooks->sendNotification($order, $hookAdmin, 'admin', $order);
    }
}

function mensagiasms_newOrderHook($order_id)
{
    $mensagiaHooks = new MensagiaHooks();
    $order = new WC_Order($order_id);

    // customer
    $hookCustomer = MensagiaSMSNotification::isHookEnabled('newOrderHook', 'customer');

    if ($hookCustomer) {
        $result = $mensagiaHooks->sendNotification($order, $hookCustomer, 'customer', $order);
    }

    // admin
    $hookAdmin = MensagiaSMSNotification::isHookEnabled('newOrderHook', 'admin');

    if ($hookAdmin) {
        $result = $mensagiaHooks->sendNotification($order, $hookAdmin, 'admin', $order);
    }
}

function mensagiasms_paymentCompletedHook($order_id)
{
    $mensagiaHooks = new MensagiaHooks();
    $order = new WC_Order($order_id);

    // customer
    $hookCustomer = MensagiaSMSNotification::isHookEnabled('paymentCompletedHook', 'customer');

    if ($hookCustomer) {
        $result = $mensagiaHooks->sendNotification($order, $hookCustomer, 'customer', $order);
    }

    // admin
    $hookAdmin = MensagiaSMSNotification::isHookEnabled('paymentCompletedHook', 'admin');

    if ($hookAdmin) {
        $result = $mensagiaHooks->sendNotification($order, $hookAdmin, 'admin', $order);
    }

    //error_log( print_r($order, TRUE) );
}

function mensagiasms_orderRefunded($order_id, $refund_id)
{
    $mensagiaHooks = new MensagiaHooks();
    $order = new WC_Order($order_id);
    $refund = new WC_Order_Refund($refund_id);

    // customer
    $hookCustomer = MensagiaSMSNotification::isHookEnabled('orderRefunded', 'customer');

    if ($hookCustomer) {
        $result = $mensagiaHooks->sendNotification(null, $hookCustomer, 'customer', $order, $refund);
    }

    // admin
    $hookCustomer = MensagiaSMSNotification::isHookEnabled('orderRefunded', 'admin');

    if ($hookCustomer) {
        $result = $mensagiaHooks->sendNotification(null, $hookCustomer, 'admin', $order, $refund);
    }
}

function mensagiasms_deleteProductHook($post_id)
{
    $mensagiaHooks = new MensagiaHooks();
    $product       = wc_get_product($post_id);

    if ($product) {
        // admin
        $hookCustomer = MensagiaSMSNotification::isHookEnabled('deletedProduct', 'admin');

        if ($hookCustomer) {
            $result = $mensagiaHooks->sendNotification(null, $hookCustomer, 'admin', null, null, $product);
        }
    }
}

function mensagiasms_wp_admin_notices($msg)
{
    $arrayNotice = get_option('MensagiaWPAdminNotices');

    if ($arrayNotice) {
        foreach ($arrayNotice as $msg) {
            $arrayNotice = explode('|||', $msg);
            $type        = $arrayNotice[0];
            $msg         = $arrayNotice[1]; ?>
            <div class="notice <?php echo $type; ?> is-dismissable" >
                <p><?php echo __($msg, 'woo-mensagia'); ?></p>
            </div>
            <?php
        }
    }

    update_option('MensagiaWPAdminNotices', null);
}

function mensagiasms_my_scripts()
{
    wp_register_style('mensagia_woocommerce_dashicons', plugins_url( 'css/fontello.css', __FILE__));
    wp_enqueue_style('mensagia_woocommerce_dashicons');
}
