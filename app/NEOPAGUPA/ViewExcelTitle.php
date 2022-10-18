<?php

namespace App\NEOPAGUPA;

use App\Models\Empresa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ViewExcelTitle implements FromView, WithTitle
{
    public function __construct($view, $data, $titulo) {
        $this->data = $data;
        $this->vista = $view;
        $this->titulo = $titulo;
    }
    
    public function view(): View
    { 
        $empresa =  Empresa::empresa()->first();
        return view($this->vista,['datos'=>$this->data,'empresa'=>$empresa]);
    }

    public function title(): string
    {
        return $this->titulo;
    }
}