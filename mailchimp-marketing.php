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

if (!function_exists('dd')) {
	function dd($message)
	{
		echo '<pre>';
		var_dump($message);
		echo '</pre>';
		die();
	}
}

define('MMA_PATH', __DIR__);
$GLOBALS['ADB_MAILCHIMP_MARKETING'] = new Plugin();
