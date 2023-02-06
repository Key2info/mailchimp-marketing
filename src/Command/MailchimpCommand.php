<?php

namespace ADB\MailchimpMarketing\Command;

use ADB\MailchimpMarketing\Plugin;
use DateTimeImmutable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use WP_CLI;

abstract class MailchimpCommand implements LoggerAwareInterface
{
    public const LAST_RUN_OPTION = '_mma_last_run';

    /** @var LoggerInterface|null */
    protected $logger;

    /** @var Configuration */
    protected $configuration;

    /** @var DateTimeImmutable */
    protected $startDate;

    public function __construct()
    {
        $this->setLogger(Plugin::getLogger());
        $this->configuration = Plugin::initConfiguration();
        $this->startDate = new DateTimeImmutable('now', Plugin::getTimeZone());
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getLastRun(string $operation): ?DateTimeImmutable
    {
        $lastRun = static::getLastRuns();

        return !empty($lastRun) && isset($lastRun[$operation]) ? DateTimeImmutable::createFromFormat('U', $lastRun[$operation], Plugin::getTimeZone()) : null;
    }

    public static function getLastRuns(): array
    {
        return get_option(static::LAST_RUN_OPTION, []);
    }

    protected static function setLastRun(string $operation, \DateTimeInterface $updatedLastRun = null): void
    {
        $updatedLastRun = $updatedLastRun ?? new DateTimeImmutable("now", Plugin::getTimeZone());
        $lastRun = get_option(static::LAST_RUN_OPTION);
        $lastRun[$operation] = $updatedLastRun->getTimestamp();

        update_option(static::LAST_RUN_OPTION, $lastRun);
    }

    protected function reportMemoryUsage()
    {
        $memory_usage = memory_get_usage();
        $real_memory_usage = memory_get_usage(true);
        $memory_peak = memory_get_peak_usage();
        $real_memory_peak = memory_get_peak_usage(true);
        $memory_limit = static::unitToInt(ini_get('memory_limit'));

        $format = static function ($int) {
            return sprintf('%.2fMiB', $int / 1024 / 1024);
        };

        $date = new DateTimeImmutable('now', Plugin::getTimeZone());

        WP_CLI\Utils\format_items(
            'table',
            [
                [
                    'time running' => $date->diff($this->startDate)->format('%H:%I:%S'),
                    'current usage' => $format($memory_usage),
                    'real current usage' => $format($real_memory_usage),
                    'peak usage' => $format($memory_peak),
                    'real peak usage' => $format($real_memory_peak),
                    'memory limit' => $format($memory_limit)
                ]
            ],
            array(
                'time running',
                'current usage',
                'real current usage',
                'peak usage',
                'real peak usage',
                'memory limit'
            )
        );
    }

    protected static function unitToInt($s)
    {
        if ($s === '-1') {
            return -1;
        }

        return (int)preg_replace_callback(
            '/(\-?\d+)(.?)/',
            static function ($m) {
                return $m[1] * (1024 ** strpos('BKMG', $m[2]));
            },
            strtoupper($s)
        );
    }
}
