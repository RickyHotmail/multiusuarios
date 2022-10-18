<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento_Nota_Credito extends Model
{
    use HasFactory;
    protected $table='movimiento_nota_credito';
    protected $primaryKey = 'movimientonc_id';
    public $timestamps = true;
    protected $fillable = [     
        'movimientonc_tipo',  
        'movimientonc_valor',
        'movimientonc_descripcion',
        'nota_id',
        'tipo_id',        
    ];
    protected $guarded =[
    ]; 
    public function scopeMovimientoNotaCredito($query, $id){
        return $query->where('nota_id','=',$id);
    }
    public function notaCreditoBanco(){
        return $this->belongsTo(nota_credito_banco::class, 'nota_id', 'nota_id');
    } 
    public function tipoMovBanco(){
        return $this->belongsTo(Tipo_Movimiento_Banco::class, 'tipo_id', 'tipo_id');
    }  
}
