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

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->confirm('Do you wish to added Environment Variables?')) {
            try {
                return $this->addEnvironmentVariable();
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return int
     */
    protected function addEnvironmentVariable(): int
    {
        $envPath = base_path('.env');

        if (file_exists($envPath)) {

            $envContent = file_get_contents($envPath);

            $apiEnvVariableContent = '';

            if (strpos($envContent, "CITY_BANK_API_MODE") ||
                strpos($envContent, "CITY_BANK_API_USERNAME") ||
                strpos($envContent, "CITY_BANK_API_PASSWORD") ||
                strpos($envContent, "CITY_BANK_EXCHANGE_COMPANY") ||
                strpos($envContent, "CITY_BANK_API_HOST")) {

                if ($this->confirm('Environment variables already exists try overwrite')) {
                    $apiEnvVariableContent = $this->writeEnvVariables();
                }

                $this->error("Environment variables copy canceled");
                return self::FAILURE;

            } else {
                $this->error("Environment variables update failed.");
                return self::FAILURE;
            }
        } else {
            $this->error("Can't find the (.env) file");
            return self::FAILURE;
        }
    }

    /**
     * @param bool $overwrite
     * @return string
     */
    protected function writeEnvVariables(bool $overwrite = false): string
    {
        $currentConfig = config('city-bank');

        $mode = 'sandbox';
        $username = '';
        $password = '';
        $company = '';

        if ($overwrite) {
        }




        $envContent = "
            CITY_BANK_API_MODE=sandbox}\n
            CITY_BANK_API_USERNAME=\n
            CITY_BANK_API_PASSWORD=\n
            CITY_BANK_EXCHANGE_COMPANY=\n
            CITY_BANK_API_HOST=\n\n
            ";
    }
}
