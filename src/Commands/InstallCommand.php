<?php

namespace MahShamim\CityBank\Commands;

use Illuminate\Console\Command;
use MahShamim\CityBank\Config;

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
    public $description = 'added basic setting needed for city bank install';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Publish Configuration File?')) {
            $this->saveConfiguration();
        }

        if ($this->confirm('Do you wish to added Environment Variables?')) {
            return $this->saveEnvironmentVariable();
        }

        return self::SUCCESS;
    }

    /**
     * publish the configuration file to config directory
     *
     * @return void
     */
    protected function saveConfiguration()
    {
        if (file_exists(base_path('config/city-bank.php'))) {
            if ($this->confirm('Overwrite this existing configuration file?')) {
                $this->call('vendor:publish', ['--tag' => 'city-bank-config', '--force' => true]);
                $this->info('Configuration published.');
            }
            $this->info('Configuration published cancelled.');
        } else {
            $this->call('vendor:publish', ['--tag' => 'city-bank-config']);
            $this->info('Configuration published.');
        }
    }

    /**
     * Append Environment Variables to .env file
     *
     * @return int
     */
    protected function saveEnvironmentVariable()
    {
        $envPath = base_path('.env');

        if (file_exists($envPath)) {
            $apiEnvVariableContent = $this->envVariables();

            if (file_put_contents($envPath, $apiEnvVariableContent, FILE_APPEND) !== false) {
                $this->info('Environment variables added successfully.');

                return self::SUCCESS;
            }

            $this->error('Environment variables update failed.');

            return self::FAILURE;
        }

        $this->error("Can't find the (.env) file");

        return self::FAILURE;
    }

    /**
     * Return the env values
     *
     * @param  bool  $overwrite
     * @return string
     */
    protected function envVariables($overwrite = false)
    {
        $currentConfig = config('city-bank.sandbox');

        $mode = (isset($currentConfig['mode']) ? $currentConfig['mode'] : Config::MODE_SANDBOX);
        $username = (isset($currentConfig['username']) ? $currentConfig['username'] : '');
        $password = (isset($currentConfig['password']) ? $currentConfig['password'] : '');
        $company = (isset($currentConfig['company']) ? $currentConfig['company'] : '');

        return implode("\n", [
            "\n",
            "CITY_BANK_API_MODE={$mode}",
            "CITY_BANK_API_USERNAME={$username}",
            "CITY_BANK_API_PASSWORD={$password}",
            "CITY_BANK_EXCHANGE_COMPANY={$company}",
            "\n",
        ]);
    }
}
