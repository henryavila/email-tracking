<?php

namespace AppsInteligentes\EmailTracking\Commands;

use Illuminate\Console\Command;

class EmailTrackingCommand extends Command
{
    public $signature = 'email-tracking';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
