<?php

namespace MahShamim\CityBank\Commands;

use Illuminate\Console\Command;

class CityBankCommand extends Command
{
    public $signature = 'city-bank-api';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
