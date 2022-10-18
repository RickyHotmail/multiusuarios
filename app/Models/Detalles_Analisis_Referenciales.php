<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalles_Analisis_Referenciales extends Model
{
    use HasFactory;
    protected $table='detalle_analisis_referenciales';
    protected $primaryKey = 'detalle_referenciales_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_Columna1',
        'detalle_Columna2',
        'detalle_estado',       
        'detalle_valores_id',     
    ];
    protected $guarded =[
    ];
    public function scopeReferencialdetalle($query, $id){
        return $query->join('detalle_analisis_valores','detalle_analisis_valores.detalle_valores_id','=','detalle_analisis_referenciales.detalle_valores_id')->where('detalle_analisis_referenciales.detalle_valores_id','=',$id);
    }
}
