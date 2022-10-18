<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reporte_Tributario extends Model
{
    use HasFactory;
    protected $table='reporte_tributario';
    protected $primaryKey = 'reporte_id';
    public $timestamps = true;
    protected $fillable = [        
        'reporte_mes',
        'reporte_ano',
        'reporte_tipo',        
        'reporte_casillero',
        'reporte_vbruto',
        'reporte_vnc',        
        'reporte_vneto',
        'reporte_viva',
        'reporte_estado',
        'empresa_id',
          
    ];
    protected $guarded =[
    ];   
    public function scopeReportributarios($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('reporte_estado','=','1');
    }
    public function scopeReporteTributario($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('reporte_id','=',$id);
    }
}
