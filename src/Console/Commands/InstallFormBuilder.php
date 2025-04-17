<?php

namespace Tuanbtre\FormBuilder\Console\Commands;

use Illuminate\Console\Command;

class InstallFormBuilder extends Command
{
    protected $signature = 'form-builder:install';
    protected $description = 'Install the FormBuilder package';

    public function handle()
    {
        $this->info('Installing FormBuilder...');

        // Publish migrations
        $this->call('vendor:publish', [
            '--provider' => 'Tuanbtre\FormBuilder\FormBuilderServiceProvider',
            '--tag' => 'form-builder-migrations',
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--provider' => 'Tuanbtre\FormBuilder\FormBuilderServiceProvider',
            '--tag' => 'form-builder-views',
        ]);

        // Run migrations
        $this->call('migrate');

        $this->info('FormBuilder installed successfully!');
    }
}