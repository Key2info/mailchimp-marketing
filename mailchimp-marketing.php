<?php

/**
 * Plugin Name:     Mailchimp Marketing
 * Plugin URI:      https://www.mailchimp-marketing.be
 * Description:     
 * Author:          De Belser Arne
 * Author URI:      https://www.mailchimp-marketing.be
 * Text Domain:    	adb-mailchimp-marketing
 * Domain Path:     /languages
 * Version:         0.6.1
 *
 * @package         ADB_Mailchimp_Marketing
 */

require_once __DIR__ . '/vendor/autoload.php';

use ADB\MailchimpMarketing\Plugin;

// Woocoommerce has to be active in order for this plugin to work
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action('admin_notices', function () {
		echo '<div class="notice notice-warning is-dismissible">
             <p>' . __('Mailchimp Marketing - Woocoommerce has to be active in order for this plugin to work', 'mailchimp-marketing') . '</p>
         </div>';
	});
}

if (!function_exists('dd')) { {
		echo '<pre>';
		var_dump($message);
		echo '</pre>';
		die();
	}
}

define('MMA_PATH', __DIR__);
define('MMA_PATH_RELATIVE', plugin_dir_path(__FILE__));
$GLOBALS['ADB_MAILCHIMP_MARKETING'] = new Plugin();
