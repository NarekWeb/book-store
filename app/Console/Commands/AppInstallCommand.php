<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppInstallCommand extends Command
{
    protected $signature = 'app:install';
    protected $description = 'Initial application installation: key, migrations, optional seeders & storage link';

    public function handle(): int
    {
        $this->info('Starting application installation...');

        $this->info('Generating APP_KEY...');
        $this->call('key:generate', ['--force' => true]);


        $this->warn('Running migrate:fresh (all data will be lost)...');
        $this->call('migrate:fresh', ['--force' => true]);

        $this->info('Running database seeders...');
        $this->call('db:seed', ['--force' => true]);

        $this->info('Creating storage symlink...');
        $this->call('storage:link');

        $this->info('Application installation completed successfully ✅');

        return Command::SUCCESS;
    }
}
