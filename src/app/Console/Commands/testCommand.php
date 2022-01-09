<?php

namespace App\Console\Commands;

use Boot\Console\Commands\Command;

class testCommand extends Command
{
    /**
     * Define console command name
     * php slim make:command
     */
    protected $name = 'test';
    protected $help = 'help text for console command';
    protected $description = 'description text for console command';

    protected function arguments()
    {
        return [
            'name' => $this->require('testCommand name command description'),
        ];
    }

    public function handler()
    {
          //
    }
}
