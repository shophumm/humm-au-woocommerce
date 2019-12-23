<?php
/* copyright flexigroup
 * Plugin Name: Oxipay Payment Gateway
 * Plugin URI: https://www.oxipay.com.au
 * Description: Easy to setup installment payment plans from <a href="https://oxipay.com.au">Oxipay</a>.
 * Version: plugin_version_placeholder
 * Author: roger.bi@flexigroup.com.au
 * Author URI: https://www.oxipay.com.au
 * @package WordPress
 * @since 0.4.8
 */

if (!defined('ABSPATH')) exit;

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active('woocommerce/woocommerce.php')) return;
require_once plugin_dir_path(__FILE__) . 'includes/WC_Oxipay_config.php';

add_action('plugins_loaded', 'woocommerce_oxipay_init', 0);

add_action('parse_request', 'get_oxipay_settings');

/**
 *
 */

function woocommerce_oxipay_init()
{
    require_once plugin_dir_path(__FILE__) . 'includes/WC_Oxipay_Gateway.php';
}

/**
 * @param $methods
 * @return array
 */

function add_oxipay_payment_gateway($methods)
{
    $methods[] = 'WC_Oxipay_Gateway';
    return $methods;
}

/**
 * @param $vars
 * @return array
 */

function add_oxipay_query_vars_filter($vars)
{
    $vars[] = "oxi_settings";
    return $vars;
}

/**
 * Look for an ajax request that wants settings
 * @param @query
 * @return null
 */

function get_oxipay_settings($query)
{

    $gateways = WC_Payment_Gateways::instance();
    if (!$gateways) {
        return;
    }

    $list = $gateways->payment_gateways();
    if (!$list || !isset($list['oxipay'])) {
        return;
    }

    $oxipay = $list['oxipay'];

    if (isset($query->query_vars['oxi_settings'])) {
        $settings = $oxipay->get_settings();
        wp_send_json($settings);
    }
    return;
}

/**
 * @param $links
 * @return array
 */

function oxipay_settings_link($links)
{
    $settings_link = array('<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=oxipay') . '">Settings</a>');
    return array_merge($settings_link, $links);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'oxipay_settings_link');
add_filter('woocommerce_payment_gateways', 'add_oxipay_payment_gateway');
add_filter('query_vars', 'add_oxipay_query_vars_filter');
