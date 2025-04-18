<?php

namespace Tuanbtre\FormBuilder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InstallFormBuilder extends Command
{
    protected $signature = 'form-builder:install';
    protected $description = 'Install the FormBuilder package';

    public function handle()
    {
        $this->info('Installing FormBuilder...');
		$this->info('copy FormController.php to Controller/Admin');
		copy(__DIR__.'/../Http/Controllers/FormController.php', app_path('Http/Controllers/Admin/FormController.php'));
		$this->info('copy model Form.php to Model folder');
		copy(__DIR__.'/../Models/Form.php', app_path('Models/Form.php'));
		$this->info('copy model FormSubmission.php to Model folder');
		copy(__DIR__.'/../Models/FormSubmission.php', app_path('Models/FormSubmission.php'));
        $this->info('add controller to route-admin');
		DB::table('tbl_function')->insert([
			[
				'id' => 1115,
				'icon' => null,
				'url' => 'forms',
				'controlleract' => 'FormController@index',
				'method' => 'any',
				'title_en' => 'Form manament',
				'title_vn' => 'Quan ly Form',
				'description' => null,
				'function_tab' => 'Form manament',
				'route_name' => 'admin.advertisement.index',
				'can_grant' => 1,
				'isshow' => 1,
				'parent_id' => 11,
				'created_at' => now(),
				'updated_at' => now()
			]);
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