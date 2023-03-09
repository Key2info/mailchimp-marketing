<?php

namespace ADB\MailchimpMarketing\Admin;

use ADB\MailchimpMarketing\Plugin;

class Settings
{
    public const PAGE_SLUG = 'mailchimp-markerting-settings';
    public const OPTION_NAME = 'adb_mma_options';

    public function __construct()
    {
        // Initialize plugin settings
        add_action('admin_init', function () {
            $this->initSettings();
        });

        // Add settings page
        add_action('admin_menu', function () {
            $this->addMenuPage();
        });
    }

    private static function getOptionName($option)
    {
        return sprintf('%s[%s]', self::OPTION_NAME, $option);
    }

    private static function getOptionValue($option)
    {
        return static::getSetting($option);
    }

    protected function initSettings(): void
    {
        register_setting(self::PAGE_SLUG, self::OPTION_NAME);

        add_settings_section(
            'mma_api_creds',
            __('API Credentials', 'adb-mailchimp-marketing'),
            function ($args) {
                echo Plugin::render('settings/credentials', $args);
            },
            self::PAGE_SLUG
        );

        add_settings_field(
            'api_token',
            __('API Token', 'adb-mailchimp-marketing'),
            static function ($args) {
                echo Plugin::render('settings/field/input', [
                    'atts' => [
                        'type' => 'password',
                        'name' => static::getOptionName('api_token'),
                        'value' => static::getOptionValue('api_token'),
                    ],
                    'args' => $args,
                    'description' => __('The access token used to authenticate with the Mailchimp API.', 'adb-mailchimp-marketing'),
                ]);
            },
            self::PAGE_SLUG,
            'mma_api_creds',
            [
                'class' => 'wporg_row',
                'label_for' => 'api_token',
            ]
        );

        add_settings_field(
            'api_token_transactional',
            __('API Token Transactional', 'adb-mailchimp-marketing'),
            static function ($args) {
                echo Plugin::render('settings/field/input', [
                    'atts' => [
                        'type' => 'password',
                        'name' => static::getOptionName('api_token_transactional'),
                        'value' => static::getOptionValue('api_token_transactional'),
                    ],
                    'args' => $args,
                    'description' => __('The access token used to authenticate with the Mandrill API.', 'adb-mailchimp-marketing'),
                ]);
            },
            self::PAGE_SLUG,
            'mma_api_creds',
            [
                'class' => 'wporg_row',
                'label_for' => 'api_token_transactional',
            ]
        );

        add_settings_section(
            'mma_test_api_creds',
            __('API Test Credentials', 'adb-mailchimp-marketing'),
            function ($args) {
                echo Plugin::render('settings/test', $args);
            },
            self::PAGE_SLUG
        );

        add_settings_field(
            'test_api_token',
            __('API Token', 'adb-mailchimp-marketing'),
            static function ($args) {
                echo Plugin::render('settings/field/input', [
                    'atts' => [
                        'type' => 'password',
                        'name' => static::getOptionName('test_api_token'),
                        'value' => static::getOptionValue('test_api_token'),
                    ],
                    'args' => $args,
                    'description' => __('The access token used to authenticate with the test sandbox for the Mailchimp API.', 'adb-mailchimp-marketing'),
                ]);
            },
            self::PAGE_SLUG,
            'mma_test_api_creds',
            [
                'class' => 'wporg_row',
                'label_for' => 'test_api_token',
            ]
        );
    }

    public static function getSettings()
    {
        return wp_parse_args(
            get_option(self::OPTION_NAME),
            [
                'api_token' => '',
                'api_token_transactional' => '',
                'test_api_token' => '',
                'test_api_token_transactional' => '',
            ]
        );
    }

    public static function getSetting($setting)
    {
        $settings = static::getSettings();

        return $settings[$setting] ?? null;
    }

    protected function addMenuPage(): void
    {
        add_options_page(
            __('Mailchimp Marketing Settings', 'adb-mailchimp-marketing'),
            __('Mailchimp Marketing Settings', 'adb-mailchimp-marketing'),
            'manage_options',
            self::PAGE_SLUG,
            static function () {
                if (!current_user_can('manage_options')) {
                    return;
                }

                echo Plugin::render('settings', [
                    'log_files' => Plugin::getLogFiles(),
                    'selected_log_file' => $_GET['log_file'] ?? null,
                    'log_file_content' => isset($_GET['log_file']) ? Plugin::getLogFileContent($_GET['log_file']) : null,
                ]);
            }
        );
    }
}
