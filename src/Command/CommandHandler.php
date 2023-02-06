<?php

namespace ADB\MailchimpMarketing\Command;

use ADB\MailchimpMarketing\Command\Campaign;

class CommandHandler
{
    private $commands = [];

    public function __construct()
    {
        if (defined('WP_CLI') && WP_CLI) {
            $this->commands = array_map(
                function ($className) {
                    return \WP_CLI::add_command($className::COMMAND_NAME, $className);
                },
                [
                    Campaign::class,
                ]
            );
        }
    }
}
