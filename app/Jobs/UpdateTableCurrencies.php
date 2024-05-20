<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class UpdateTableCurrencies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
     
    $currencies = ['USD', 'EUR', 'AED'];
    $data = [];

    foreach ($currencies as $currency) {
        $client = new Client();
        $reqUrl = "http://api.exchangerate.host/convert?access_key=c959934593424bc119294d86ec8f0f45&from=USD&to=EGP&amount=1";

        try {
            $response = $client->get($reqUrl);
            $exchangeRateData = json_decode($response->getBody());

            // Extract the exchange rate value from the response
            $result = $exchangeRateData->result;         
            // Add the currency and its exchange rate to the data array
            $data[$currency] = $result;
        } catch (\Exception $e) {
            // Handle HTTP request or JSON parse error...
            // Log the error or handle it based on your application's requirements
            // Example: Log::error($e->getMessage());
        }
    }
    foreach ($data as $currency =>$val){
        DB::table('currencies')->where('currency',$currency)->update(['exchange_rate' =>$val,'updated_at'=> Carbon::now()]);

    }
    }
}
