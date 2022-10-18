<?php

namespace App\NEOPAGUPA;

use App\Models\Empresa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleViewExcel implements WithMultipleSheets
{
    public function __construct($view, $data) {
        $this->datos=$data;
        $this->view=$view;
    }
    
    public function sheets(): array
    { 
        $sheets=[];
        $i=0;
        foreach($this->datos as $clave=>$dato){
            $i++;
            $sheets['hoja'.$i] =  new ViewExcelTitle($this->view, array('ordenes'=>$dato), strtoupper($clave));
        }

        return $sheets;
    }
    
}