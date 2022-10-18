<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lista_Precio extends Model
{
    use HasFactory;
    protected $table='lista_precio';
    protected $primaryKey = 'lista_id';
    public $timestamps = true;
    protected $fillable = [     
        'lista_nombre',   
        'lista_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeListasPrecios($query)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('lista_estado', '=', '1');
    }
    public function scopeListasPreciosDetalle($query)
    {
        return $query->join('detalle_lista', 'detalle_lista.lista_id','=','lista_precio.lista_id'
                    )->where('lista_precio.empresa_id', '=', Auth::user()->empresa_id)->where('lista_precio.lista_estado', '=', '1');
    }
    public function scopeListaPrecio($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('lista_id', '=', $id);
    }
    public function scopeListaPrecioDetalle($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('lista_precio.lista_id', '=', $id);
    }
    public function detalles(){
        return $this->hasMany(Detalle_Lista::class, 'lista_id', 'lista_id');
    }
    
}
