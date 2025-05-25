<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\Charity;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\NotificationTrait;



class UpdateCountView extends Command
{
    use NotificationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-count-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clients = Client::all();
        if(!$clients->isEmpty()){
            foreach($clients as $client)
            {
                $user = User::where('id',$client->user_id)->first();
                $client->ad_view_counter = 0 ;
                $client->save();

                $now = Carbon::now()->addMonths(1);
                if($now->toDateString() == $client->points_exp_date && $client->points != 0 && $user->device_token != null){
                    $this->send_notify($user->device_token,"Loyality System","your points will be expired after one month");
                    $notify = Notification::query()->create([
                        'title' => "Loyality System",
                        'body' => "your points will be expired after one month",
                        'user_id' => $user->id,
                    ]);
                    print('success');
                }
            }
        }
        $charities = Charity::all();
        if(!$charities->isEmpty()){
            foreach($charities as $charity)
            {
                $user = User::where('id',$charity->user_id)->first();

                $now = Carbon::now()->addMonths(1);
                if($charity->points_exp_date == $now->toDateString() && $charity->points != 0 && $user->device_token != null){
                    $this->send_notify($user->device_token,"Loyality System","your points will be expired after one month");
                    $notify = Notification::query()->create([
                        'title' => "Loyality System",
                        'body' => "your points will be expired after one month",
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
