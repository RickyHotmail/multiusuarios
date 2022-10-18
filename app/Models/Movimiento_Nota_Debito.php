<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento_Nota_Debito extends Model
{
    use HasFactory;
    protected $table='movimiento_nota_debito';
    protected $primaryKey = 'movimientond_id';
    public $timestamps = true;
    protected $fillable = [  
        'movimientond_tipo',  
        'movimientond_valor',
        'movimientond_descripcion',
        'nota_id',
        'tipo_id',        
    ];
    protected $guarded =[
    ]; 
    public function scopeMovimientoNotaDebito($query, $id){
        return $query->where('nota_id','=',$id);
    }
    public function notaDebitoBanco(){
        return $this->belongsTo(nota_debito_banco::class, 'nota_id', 'nota_id');
    }  
    public function tipoMovBanco(){
        return $this->belongsTo(Tipo_Movimiento_Banco::class, 'tipo_id', 'tipo_id');
    } 
}
