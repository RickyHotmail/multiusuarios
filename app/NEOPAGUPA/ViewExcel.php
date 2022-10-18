<?php

namespace App\NEOPAGUPA;

use App\Models\Empresa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ViewExcel implements FromView
{
    public function __construct($view, $data) {
        $this->data = $data;
        $this->vista = $view;
    }
    
    public function view(): View
    { 
        $empresa =  Empresa::empresa()->first();
        return view($this->vista,['datos'=>$this->data,'empresa'=>$empresa]);
    }
    
}