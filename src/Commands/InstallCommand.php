<?php

namespace MahShamim\CityBank\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'city-bank:install';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'added basic setting needed  for city bank install';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->confirm('Do you wish to added Environment Variables?')) {
            try {
                $this->addEnvironmentVariable();
                return self::SUCCESS;
            } catch (\Exception) {
                return self::FAILURE;
            }
        }
        return self::SUCCESS;
    }

    protected function addEnvironmentVariable()
    {
        //
    }
}
