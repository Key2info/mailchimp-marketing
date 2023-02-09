<?php

namespace ADB\MailchimpMarketing;

use ADB\MailchimpMarketing\Admin\Endpoint;
use ADB\MailchimpMarketing\Admin\Settings;
use ADB\MailchimpMarketing\Command\CommandHandler;
use ADB\MailchimpMarketing\Exception\SynchronisationException;
use MailchimpMarketing\ApiClient;
use MHCG\Monolog\Handler\WPCLIHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Plugin
{
    public const TIME_ZONE = 'Europe/Brussels';

    /**
     * Amount of log files to keep.
     */
    private const LOG_MAX_AMOUNT = 7;
    /** @var array */
    private static $credentials;
    /** @var LoggerInterface|null  */
    private static $logger;
    /** @var array */
    private $modules = [];
    public static $mailchimpClient;

    public function __construct()
    {
        static::$mailchimpClient = new ApiClient();

        ['token' => $token] = self::getCredentials();

        static::$mailchimpClient->setConfig([
            'apiKey' => $token,
            'server' => Plugin::getServer($token),
        ]);

        add_action('plugins_loaded', [$this, 'initModules']);
    }

    public static function getTimeZone(): \DateTimeZone
    {
        static $timeZone;

        if (null === $timeZone) {
            $timeZone = new \DateTimeZone(static::TIME_ZONE);
        }

        return $timeZone;
    }

    public static function createDateTimeFromFormats(string $time, \DateTimeZone $timeZone = null)
    {
        $timeZone = $timeZone ?? static::getTimeZone();

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            'U',
        ];

        foreach ($formats as $format) {
            if (false !== ($date = \DateTime::createFromFormat($format, $time, $timeZone))) {
                if ($format === 'Y-m-d') {
                    $date->setTime(0, 0, 0);
                }

                return $date;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public static function getCredentials(): array
    {
        if (!isset(static::$credentials)) {
            $token = Settings::getSetting(static::isTestMode() ? 'test_api_token' : 'api_token');
            //$accountId = Settings::getSetting(static::isTestMode() ? 'test_api_account_id' : 'api_account_id');

            if (empty($token)) {
                throw new SynchronisationException('No api token provided.');
            }

            static::$credentials = [
                'token' => $token,
            ];
        }

        return static::$credentials;
    }

    public static function isTestMode(): bool
    {
        return defined('MMA_TEST_MODE') && MMA_TEST_MODE === true;
    }

    public static function isDebugMode(): bool
    {
        return defined('SFT_DEBUG') && MMA_DEBUG === true;
    }

    // public static function initConfiguration()
    // {
    //     $configuration = Configuration::getDefaultConfiguration();
    //     $configuration->setApiToken($token);
    //     $configuration->setAccountId($accountId);
    //     $configuration->setDebug($debug);

    //     Configuration::setDefaultConfiguration($configuration);

    //     return $configuration;
    // }

    public static function getServer($token)
    {
        return end(explode('-', $token));
    }

    public static function getLogger()
    {
        if (!isset(static::$logger)) {
            static::initializeLogger();
        }

        return static::$logger;
    }

    private static function initializeLogger()
    {
        global $argv;
        static::$logger = new Logger('mma');

        // Set log level
        $logLevel = static::isDebugMode() ? Logger::DEBUG : Logger::WARNING;

        // Log file location
        $logFileLocation = static::getLogFileLocation();

        // Attempt to create log file directory
        if (!is_dir($logFileLocation)) {
            if (!mkdir($logFileLocation) && !is_dir($logFileLocation)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $logFileLocation));
            }
        }

        // Log to stdout or log file
        $handler = defined('WP_CLI') && WP_CLI && (!is_array($argv) || !in_array('--quiet', $argv, true))
            ? new WPCLIHandler($logLevel)
            : new RotatingFileHandler(static::getLogFileLocation() . '/mailchimp-marketing.log', static::LOG_MAX_AMOUNT, $logLevel);

        static::$logger->pushHandler($handler);
    }

    /**
     * @return string Path to log file
     */
    private static function getLogFileLocation()
    {
        return WP_CONTENT_DIR . '/mailchimp-marketing';
    }

    public static function getLogFiles()
    {
        $files = glob(static::getLogFileLocation() . '/*.log');

        return array_map('basename', $files);
    }

    public static function getLogFileContent($logFile)
    {
        $files = static::getLogFiles();
        $logFile = basename($logFile);

        if (!in_array($logFile, $files, true)) {
            return null;
        }

        return file_get_contents(static::getLogFileLocation() . '/' . $logFile);
    }

    /**
     * Initialize modules.
     *
     * @return void
     */
    public function initModules()
    {
        static $modules = [
            CommandHandler::class,
            Settings::class,
            Endpoint::class,
        ];

        $this->modules = array_map(
            function ($className) {
                return new $className;
            },
            $modules
        );
    }

    public static function render(string $template, $context = [])
    {
        $templateFolder = MMA_PATH . '/templates/';
        $templateFile = $templateFolder . $template . '.phtml';

        if (!is_readable($templateFile)) {
            return null;
        }

        extract($context, EXTR_SKIP);
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
}
