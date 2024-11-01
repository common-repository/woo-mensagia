<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mensagia_countries");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mensagia_admins");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mensagia_sms_notifications");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mensagia_sms_notifications_lang");

// delete options
delete_option('MENSAGIA_LOGIN_EMAIL');
delete_site_option('MENSAGIA_LOGIN_EMAIL');


delete_option('MENSAGIA_LOGIN_PASSWORD');
delete_site_option('MENSAGIA_LOGIN_PASSWORD');


delete_option('MENSAGIA_AUTHENTICATED');
delete_site_option('MENSAGIA_AUTHENTICATED');


delete_option('MENSAGIA_PREFIX_MODE');
delete_site_option('MENSAGIA_PREFIX_MODE');


delete_option('MENSAGIA_API_CONFIGURATION');
delete_site_option('MENSAGIA_API_CONFIGURATION');
