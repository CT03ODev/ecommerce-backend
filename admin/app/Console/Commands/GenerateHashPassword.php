<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateHashPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-hash-password {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate hash password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment(Hash::make($this->argument('password')));
    }
}
