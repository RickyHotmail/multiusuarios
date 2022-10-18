<?php

namespace App\Models;

use App\Observers\MovimientoProductoPrestamoObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Movimiento_Prestamo_Producto extends Model
{
    use HasFactory;
    protected $table='movimiento_prestamo_producto';
    protected $primaryKey = 'movimiento_id';
    public $timestamps=true;
    protected $fillable = [
        'movimiento_fecha',
        'movimiento_tipo',  
        'movimiento_descripcion',       
        'movimiento_valor',        
        'movimiento_documento', 
        'movimiento_numero_documento',
        'movimiento_estado',
        'cliente_id',
        'producto_id',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    protected static function booted()
    {
        Movimiento_Prestamo_Producto::observe(MovimientoProductoPrestamoObserver::class);
    }
    public function scopeMovProductoByFechaCorte($query,  $fechaCorte){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('movimiento_fecha','<=',$fechaCorte);
    }
    public function scopebuscar($query,  $cliente_id,  $fechaCorte){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cliente_id','=',$cliente_id)->where('movimiento_fecha','<=',$fechaCorte);
    }
    public function scopeTotal($query,  $cliente_id,$fechaCorte){
        return $query->join('producto','movimiento_prestamo_producto.producto_id','=','producto.producto_id')->where('movimiento_prestamo_producto.empresa_id','=',Auth::user()->empresa_id)->where('cliente_id','=',$cliente_id)->where('movimiento_fecha','<=',$fechaCorte);
    }
    public function scopetotalproducto($query,  $cliente_id,  $producto_id,  $fecha){
        return $query->where('movimiento_prestamo_producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_id','=',$producto_id)->where('cliente_id','=',$cliente_id)->where('movimiento_fecha','<=',$fecha);
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
}
