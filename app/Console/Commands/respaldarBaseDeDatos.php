<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;

class respaldarBaseDeDatos extends Command
{
    protected $signature = 'respaldo:respaldarDB';

    protected $description = 'hacer un respaldo de la base de datos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $ruta = 'respaldos_base_datos';
        if (!is_dir(public_path().'/'.$ruta)) {
            mkdir(public_path().'/'.$ruta, 0777, true);
        }

        $filename = "backup_" . (new DateTime())->format('Y-m-d_h-i-s') . ".sql";
  
        $command = '"C:\Program Files\PostgreSQL\12\bin\pg_dump.exe" --dbname=postgresql://'
                .env('DB_USERNAME').":"
                .env('DB_PASSWORD')."@"
                .env('DB_HOST').":5432/"
                .env('DB_DATABASE')
                .' -f '.public_path().'/'.$ruta.'/'.env('DB_DATABASE').'_'.$filename;
        
        $returnVar = NULL;
        $output  = NULL;
        exec($command, $output, $returnVar);
    }
}