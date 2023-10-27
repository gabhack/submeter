<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    public $ruta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $minut = Informes::where('check',1)->get();
        if(!is_null($minut))
        {
            foreach ($minut as $key) {
                $mails = explode(';', $key['emails']);
                foreach ($mails as $value) {
                    Mail::to($value,'Submeter 4.0 (Informes Programados)')->send(new SendMailable());
                }                
            }
        }
    }
}
