<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Lista extends Model
{
    use HasFactory;
    protected $table ='detalle_lista';
    protected $primaryKey = 'detallel_id';
    public $timestamps=true;
    protected $fillable = [   
        'detallel_dias',     
        'detallel_valor',
        'detallel_estado',
        'lista_id',
        'producto_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeDetallesLista($query){
        return $query->join('lista_precio','lista_precio.lista_id','=','detalle_lista.lista_id'
                    )->where('lista_precio.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_lista.detallel_estado','=','1');       
    }
    public function scopeDetalleLista($query, $id){
        return $query->join('lista_precio','lista_precio.lista_id','=','detalle_lista.lista_id'
                    )->where('lista_precio.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_lista.detallel_id','=',$id);
    }
    public function scopeDetalleByLista($query, $id){
        return $query->join('lista_precio','lista_precio.lista_id','=','detalle_lista.lista_id'
                    )->where('lista_precio.empresa_id','=',Auth::user()->empresa_id
                    )->where('lista_precio.lista_id','=',$id);
    }
    public function scopeDetallesListaId($query, $idLista, $idProducto){
        return $query->join('lista_precio','lista_precio.lista_id','=','detalle_lista.lista_id'
                    )->where('lista_precio.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_lista.lista_id','=',$idLista
                    )->where('detalle_lista.producto_id','=',$idProducto);
    }
    public function scopePrecioCliente($query, $lista_id, $producto_id, $plazo)
    {
        return $query->join('lista_precio','lista_precio.lista_id','=','detalle_lista.lista_id')
                    ->where('lista_precio.empresa_id','=',Auth::user()->empresa_id)
                    ->where('detalle_lista.lista_id', '=', $lista_id)
                    ->where('detalle_lista.producto_id', '=', $producto_id)
                    ->where('detalle_lista.detallel_dias', '=', $plazo);
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
