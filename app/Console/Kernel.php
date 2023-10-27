<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Informes;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        //Commands\SendEmail::class,
        /*Commands\SendInformes::class,
        Commands\SendInforme2::class,
        Commands\SendInformesSemanal::class,
        Commands\SendInformesMensual::class,
        Commands\SendInformesTrimestre::class,
        Commands\SendInformesAnual::class,

        Commands\AlertPotenciaConsumo::class,
        Commands\AlertasGenerales::class,

        Commands\SendInformesAnalizadorDiario::class,
        Commands\SendInformesAnalizadorMensual::class,
        Commands\SendInformesAnalizadorAnual::class,*/
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')
        //        ->everyMinute();
        //$schedule->command('email:alert')->everyMinute();


        /*$schedule->command('send:informes')
                 ->everyMinute();*/


        // $minut = Informes::where('check',1)->get();
        // if(!is_null($minut))
        // {
        // $schedule->command('send:informes')->everyMinute();
        //$schedule->command('send:informes')
        //  ->everyMinute();
        // }
        // $minut5 = Informes::where('check',2)->get();
        // if(!is_null($minut5))
        // {
        // $schedule->command('send:informes2')->everyFiveMinutes();
        // $schedule->command('send:informes2')
        //      ->weekly();
        // }

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
