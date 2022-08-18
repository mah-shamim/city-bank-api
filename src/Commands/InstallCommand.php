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
                return $this->addEnvironmentVariable();
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    protected function addEnvironmentVariable()
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $apiEnvVariableContent = "
            CITY_BANK_API_MODE=sandboxi\n
            CITY_BANK_API_USERNAME=null\n
            CITY_BANK_API_PASSWORD=null\n
            CITY_BANK_EXCHANGE_COMPANY=null\n
            CITY_BANK_API_HOST=null\n\n
            ";
            if (file_put_contents($envPath, ($envContent.$apiEnvVariableContent))) {
                $this->info('Environment Variables Added Successfully.');

                return self::SUCCESS;
            } else {
                $this->error('Environment Variables Update Failed.');

                return self::FAILURE;
            }
        } else {
            $this->error("can't find the (.env) file");

            return self::FAILURE;
        }
    }
}
