<?php

namespace App\Models;

use App\Observers\MovimientoProductoObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Movimiento_Producto extends Model
{
    use HasFactory;
    protected $table='movimiento_producto';
    protected $primaryKey = 'movimiento_id';
    public $timestamps=true;
    protected $fillable = [
        'movimiento_fecha',
        'movimiento_costeo',  
        'movimiento_cantidad',       
        'movimiento_precio',        
        'movimiento_iva', 
        'movimiento_total',
        'movimiento_stock_actual',
        'movimiento_costo_promedio',
        'movimiento_documento',
        'movimiento_motivo',
        'movimiento_tipo',
        'movimiento_descripcion',
        'movimiento_estado',
        'producto_id',
        'siembra_id',
        'bodega_id',
        'centro_consumo_id',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    protected static function booted()
    {
        Movimiento_Producto::observe(MovimientoProductoObserver::class);
    }
    public function scopeMovProductoByFechaCorte($query, $producto_id, $fechaCorte){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('movimiento_producto.producto_id','=',$producto_id)->where('movimiento_fecha','<=',$fechaCorte);
    }
    public function scopeMovProductoByFecha($query, $producto_id, $fechaInicio,$fechaFin){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('producto_id','=',$producto_id)->where('movimiento_fecha','>=',$fechaInicio)->where('movimiento_fecha','<=',$fechaFin);
    }
    public function scopeMovimientoByCC($query, $cc, $fechaInicio,$fechaFin){
        return $query->where('movimiento_producto.empresa_id','=',Auth::user()->empresa_id)->where('centro_consumo_id','=',$cc)->where('movimiento_fecha','>=',$fechaInicio)->where('movimiento_fecha','<=',$fechaFin);
    }
    public function scopeMovimientoComprasByCC($query, $cc, $fechaInicio,$fechaFin){
        $query->join('centro_consumo','centro_consumo.centro_consumo_id','=','movimiento_producto.centro_consumo_id')
        ->join('producto','producto.producto_id','=','movimiento_producto.producto_id')
        ->join('categoria_producto','categoria_producto.categoria_id','=','producto.categoria_id')
        ->where('movimiento_producto.empresa_id','=',Auth::user()->empresa_id);
       if($cc!='0'){
           $query->where('centro_consumo_id','=',$cc);
       }
       $query->where('movimiento_fecha','>=',$fechaInicio)->where('movimiento_fecha','<=',$fechaFin);
       return($query);
   }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function siembra(){
        return $this->belongsTo(siembra::class, 'siembra_id', 'siembra_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function cuenta(){
        return $this->belongsTo(cuenta::class, 'cuenta_id', 'cuenta_id');
    }
    public function centroConsumo(){
        return $this->belongsTo(Centro_Consumo::class, 'centro_consumo_id', 'centro_consumo_id');
    }
    public function detallesDiario()
    {
        return $this->hasMany(Detalle_Diario::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_fv()
    {
        return $this->hasOne(Detalle_FV::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_nc()
    {
        return $this->hasOne(Detalle_NC::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_nd()
    {
        return $this->hasOne(Detalle_ND::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_tc()
    {
        return $this->hasOne(Detalle_TC::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_eb()
    {
        return $this->hasOne(Detalle_EB::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_ib()
    {
        return $this->hasOne(Detalle_IB::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_lc()
    {
        return $this->hasOne(Detalle_LC::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_od()
    {
        return $this->hasOne(Detalle_OD::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_ne()
    {
        return $this->hasOne(Detalle_NE::class, 'movimiento_id', 'movimiento_id');
    }
    public function detalle_or()
    {
        return $this->hasOne(detalle_or::class, 'movimiento_id', 'movimiento_id');
    }
}
