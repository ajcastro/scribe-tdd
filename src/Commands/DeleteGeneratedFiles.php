<?php

namespace AjCastro\ScribeTdd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeleteGeneratedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scribe:tdd:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete generated files by scribe-tdd.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        collect(File::allFiles(storage_path('scribe-tdd')))->reject(function ($file) {
            return Str::endsWith($file->getPathname(), '-@.json');
        })->each(function ($file) {
            File::delete($file->getPathname());
        });

        collect(File::directories(storage_path('scribe-tdd')))->filter(function ($dir) {
            return collect(File::files($dir))->isEmpty();
        })->each(function ($dir) {
            File::deleteDirectory($dir);
        });

        $this->info('Successfully deleted generated files from scribe-tdd. :-)');
    }
}
