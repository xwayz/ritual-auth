<?php
namespace Admaja\RitualAuth;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RitualAuthPublishCommand extends Command{
    protected $signature = 'ritualauth:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Ritualauth assets from vendor packages';

    public function handle()
    {
        $this->fire();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->publishMigrations();
        $this->publishModels();

        $this->info("Publishing RitualAuth complete");
    }

    protected function publishDirectory($from , $to)
    {
        $exclude = array('..' , '.' , '.DS_Store');
        $source = array_diff(scandir($from) , $exclude);

        foreach ($source as $item) {
            $this->info("Copying file: " . $to . $item);
            File::copy($from . $item , $to . $item);
        }
    }

    protected function publishModels()
    {
        $targetPath = app()->path()."/Models/";

        if (!File::isDirectory($targetPath)){
            File::makeDirectory($targetPath, 0777, true, true);
        }

        $this->publishDirectory(__DIR__.'/database/models/', app()->path()."/Models/");
    }

    protected function publishMigrations()
    {
        $this->publishDirectory(__DIR__.'/database/migrations/', app()->databasePath()."/migrations/");
    }
}