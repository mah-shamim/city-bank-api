<?php

namespace MahShamim\CityBank\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'city-bank:install';

    public $description = 'added basic setting needed  for city bank install';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
