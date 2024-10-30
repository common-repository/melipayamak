<?php
/*
 Plugin Name: Melipayamak
 Plugin URI: http://www.melipayamak.com/
 Description: A Persian plugin to connect WordPress/WooCommerce to Melipayamak SMS service
 Version: 2.2.12
 Author: Melipayamak
 Author URI: http://melipayamak.com/
 */
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//include pluggable.php
require_once(ABSPATH . "wp-includes/pluggable.php");
//jalali date
if (!class_exists('jDateTime'))
    require_once dirname(__FILE__) . '/includes/jdatetime.class.php';
//define product version
$melipayamak_version = '2.2.12';
define('MELIPAYAMAK_VERSION', $melipayamak_version);
//include functions
require_once(dirname(__FILE__) . '/includes/functions.php');
//include updates
require_once(dirname(__FILE__) . '/includes/updates.php');
//include melipayamak class
require_once(dirname(__FILE__) . '/includes/class.php');
$melipayamak = new melipayamak;
//register install hook
register_activation_hook(__FILE__, 'melipayamak_install');
//paginator
require_once dirname(__FILE__) . '/includes/paginator.php';
$melipayamak_page = (intval(get_option('melipayamak_page')) > 0 && intval(get_option('melipayamak_page')) < 31) ? intval(get_option('melipayamak_page')) : 10;
//include admin panel
require_once dirname(__FILE__) . '/includes/admin.php';
//include shortcode
require_once dirname(__FILE__) . '/includes/shortcode.php';
//plugin widget (abzarak!)
require_once dirname(__FILE__) . '/includes/widget.php';
//admin bar
require_once dirname(__FILE__) . '/includes/adminbar.php';
//plugin actions
require_once dirname(__FILE__) . '/includes/actions.php';
//Gravity Forms Mobile Verification
require_once dirname(__FILE__) . '/includes/GFVerification.php';