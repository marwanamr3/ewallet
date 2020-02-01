<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $info = "RUNNING\n";
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
     * @return mixed
     */
    public function handle()
    {
        // $user = User::create([
        //     'username' => "Raggy",
        //     'email' => "Ragg@safas.com",
        //     'password' => "Raggudasdasd",
        //     'type' => "Merchant",
        // ]);
        //dd("yes we're here !?");
        echo('up and running');
        echo("\n");
        $user = User::find(1);
        $user->wallet++;
        $user->save();
        echo($user->wallet);
    }
}
