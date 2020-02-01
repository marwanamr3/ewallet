<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Subscription;
use App\Service;
use App\User;
use App\Customer;
use DB;

class processSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription status for active users';

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
        $subscriptions = Subscription::all();
        foreach ($subscriptions as $sub) {
            echo('---------------------------------'."\n");
            $service = Service::find($sub->service_id);
            echo('This '.$service->name.' service subscription is '.$service->period.' days'."\n");
            
            $user = User::find($sub->customer_id);
            echo('Customer '.$user->username. ', funds: '. $user->wallet."\n");
          

            $startDate = $sub->created_at;
            $daysPassed = $startDate->diffInDays(now());
            $daysRemaining = $service->period - $daysPassed;
            echo('Days remaining for Customer #' .$sub->customer_id. ', subscribed to service #'. $sub->service_id .' is '.$daysRemaining.' days'."\n");
        
            try {
                DB::beginTransaction();
                
                if ($daysRemaining < 1) {
                    if ($user->wallet >= $service->price) {
                        //renew
                        $user->wallet = $user->wallet - $service->price;
                        $customer = Customer::find($sub->customer_id);
                        $customer->total_spendings+=$service->price;
                        Subscription::where('service_id', '=', $sub->service_id)
                        ->where('customer_id', '=', $sub->customer_id)
                        ->update(['status' => 'active','created_at' => now(),'updated_at' => now()]); // Manually update the subscription as it has compound primary key - save() does not work
                        $user->save();
                        $customer->save();
                        echo('Subscription successfully renewed, remaining funds: '.$user->wallet."\n");
                    } else {
                        //cancel
                        Subscription::where('service_id', '=', $sub->service_id)
                        ->where('customer_id', '=', $sub->customer_id)
                        ->update(['status' => 'expired','updated_at' => now()]); // Manually update the subscription as it has compound primary key - save() does not work
                        echo('Failed to renew subscription, not enough funds');
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
            }
            echo("\n".'---------------------------------');
        }
    }
}
