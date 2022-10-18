<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Analisis extends Model
{
    use HasFactory;
    protected $table='detalle_analisis';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_estado',       
        'producto_id',
        'analisis_laboratorio_id',
        'id_externo',
        'tecnica',
        'fecha_recepcion_muestra',
        'fecha_reporte',
        'fecha_validacion',
        'usuario_validacion',
        'estado'
    ];

    protected $guarded =[
    ];

    public function scopedetalleid($query, $idcabecera, $id){
        return $query->where('producto_id','=',$id)->where('analisis_laboratorio_id','=',$idcabecera);
    }

    public function scopeDetalleExamen($query, $analisis, $id_externo){
        return $query
                ->where('analisis_laboratorio_id','=',$analisis)
                ->where('id_externo','=', $id_externo);
    }

    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    
    public function analisis(){
        return $this->belongsTo(Analisis_Laboratorio::class, 'analisis_laboratorio_id', 'analisis_laboratorio_id');
    }

    public function detalles(){
        return $this->hasMany(Detalles_Analisis_Valores::class, 'detalle_id', 'detalle_id');
    }
}
