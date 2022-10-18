<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion_Reporte extends Model
{
    use HasFactory;
    protected $table ='configuracion_reporte';
    protected $primaryKey = 'configuracion_id';
    public $timestamps = true;
    protected $fillable = [       
        'configuracion_nombre',
        'configuracion_detalle'
    ];
    protected $guarded =[
    ];

    public function scopeGetConfiguracionReporteMasivo($query)
    {
        return $query->where('configuracion_nombre', '=', 'REPORTE MASIVO');
    }
}
