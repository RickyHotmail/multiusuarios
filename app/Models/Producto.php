<?php

namespace App\Models;

use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use HasFactory;
    protected $table='producto';
    protected $primaryKey = 'producto_id';
    public $timestamps=true;
    protected $fillable = [
        'producto_codigo',
        'producto_nombre',     
        'producto_codigo_barras',        
        'producto_tipo',
        'producto_precio_costo',       
        'producto_stock',        
        'producto_stock_minimo',        
        'producto_stock_maximo',
        'producto_fecha_ingreso',
        'producto_tiene_iva',
        'producto_tiene_descuento',        
        'producto_tiene_serie',        
        'producto_compra_venta',
        'producto_precio1',
        'producto_estado',
        'producto_cuenta_inventario',        
        'producto_cuenta_venta',        
        'producto_cuenta_gasto',
        'categoria_id',
        'marca_id',        
        'unidad_medida_id',        
        'empresa_id',
        'tamano_id',
        'grupo_id',
        'sucursal_id',
    ];
    protected $guarded =[
    ];
    public function precios(){
        return $this->hasMany(Precio_Producto::class, 'producto_id', 'producto_id');
    }
    public function codigos(){
        return $this->hasMany(Codigo_Producto::class, 'producto_id', 'producto_id');
    }
    public function cuentaInventario(){
        return $this->belongsTo(Cuenta::class, 'producto_cuenta_inventario', 'cuenta_id');
    }
    public function cuentaVenta(){
        return $this->belongsTo(Cuenta::class, 'producto_cuenta_venta', 'cuenta_id');
    }
    public function cuentaGasto(){
        return $this->belongsTo(Cuenta::class, 'producto_cuenta_gasto', 'cuenta_id');
    }
    public function categoriaProducto(){
        return $this->belongsTo(Categoria_Producto::class, 'categoria_id', 'categoria_id');
    }
    public function marca(){
        return $this->belongsTo(Marca_Producto::class, 'marca_id', 'marca_id');
    }
    public function unidadMedida(){
        return $this->belongsTo(Unidad_Medida_Producto::class, 'unidad_medida_id', 'unidad_medida_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function tamano(){
        return $this->belongsTo(Tamano_Producto::class, 'tamano_id', 'tamano_id');
    }
    public function grupo(){
        return $this->belongsTo(Grupo_Producto::class, 'grupo_id', 'grupo_id');
    }
    public function categoria(){
        return $this->belongsTo(Categoria_Producto::class, 'categoria_id', 'categoria_id');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function scopeProductos($query){
        return $query->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1')->orderBy('producto_nombre','asc');
    }
    public function scopeProductosCompraVenta($query){
        return $query->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1')->where('producto_compra_venta','<>','2')->orderBy('producto_nombre','asc');
    }
    public function scopeProducto($query, $id){
        return $query->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1')->where('producto_id','=',$id);
    }
    public function scopeProductoTipo($query, $tipo, $sucursal, $compraVenta){
        $query->join('sucursal','sucursal.sucursal_id','=','producto.sucursal_id')->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1');        
        if($tipo != '0'){
            $query->where('producto_tipo', '=', $tipo);
        }
        if($sucursal != '0'){
            $query->where('producto.sucursal_id', '=', $sucursal);
        }
        if($compraVenta != '0'){
            $query->where('producto_compra_venta', '=', $compraVenta);
        }        
        return $query;

    }
    public function scopeProductoCodigo($query, $id){
        return $query->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1')->where('producto_codigo','=',$id);
    }
    public function scopeExisteCodigo($query, $id){
        return $query->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_codigo','=',$id);
    }
    public function scopeProductosG($query){
        return $query->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->orwhere('grupo_producto.grupo_nombre','=','Laboratorio'
                    )->orwhere('grupo_producto.grupo_nombre','=','Procedimiento'
                    )->orwhere('grupo_producto.grupo_nombre','=','LABORATORIO'
                    )->orwhere('grupo_producto.grupo_nombre','=','PROCEDIMIENTO'
                    )->orwhere('grupo_producto.grupo_nombre','=','laboratorio'
                    )->orwhere('grupo_producto.grupo_nombre','=','procedimiento'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto.producto_estado','=','1'
                    )->orderBy('producto.producto_nombre','asc');
    }
    public function scopeProductoslaboratorio($query){
        return $query->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->where('grupo_producto.grupo_nombre','=','Laboratorio'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto.producto_estado','=','1'
                    )->orderBy('producto.producto_nombre','asc');
    }

    public function scopeProductosImagen($query){
        return $query->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->where('grupo_producto.grupo_nombre','=','Imagen'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto.producto_estado','=','1'
                    )->orderBy('producto.producto_nombre','asc');
    }

    public function scopeProductosMedicamentos($query){
        return $query->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->where('grupo_producto.grupo_nombre','=','Medicamentos'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto.producto_estado','=','1'
                    )->orderBy('producto.producto_nombre','asc');
    }
    public function scopeBuscarProductoslaboratorio($query, $buscar){
        return $query->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->where('grupo_producto.grupo_nombre','=','Laboratorio'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto.producto_estado','=','1'
                    )->where(DB::raw('lower(producto_nombre)'), 'like', '%'.strtolower($buscar).'%');
    } 
    public function scopeBuscarProductosmedicinas($query, $buscar){
        return $query->join('medicamento','medicamento.producto_id','=','producto.producto_id'
                    )->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->where('grupo_producto.grupo_nombre','=','Medicamentos'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto.producto_estado','=','1'
                    )->where(DB::raw('lower(producto_nombre)'), 'like', '%'.strtolower($buscar).'%');
    } 
       
    public function scopeServicios($query, $paciente, $especialidad){
        return $query->join('procedimiento_especialidad','procedimiento_especialidad.producto_id','=','producto.producto_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimiento_id','=','procedimiento_especialidad.procedimiento_id'
                    )->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('paciente','paciente.cliente_id','=','cliente.cliente_id'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('paciente.paciente_id','=',$paciente
                    )->where('especialidad.especialidad_id','=',$especialidad);
    }  
    public function scopeProductosByNombre($query, $buscar){
        return $query->join('empresa','empresa.empresa_id','=','producto.empresa_id'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('producto_estado','=','1'
                    )->where(DB::raw('lower(producto_nombre)'), 'like', '%'.strtolower($buscar).'%'
                    )->orderBy('producto_nombre','asc');
    }
    public function scopeProductosByNombreStock($query, $buscar){
        return $query->select('producto.producto_id','producto.producto_nombre', 'producto.producto_stock'
                    )->join('empresa','empresa.empresa_id','=','producto.empresa_id'
                    )->where('producto.empresa_id','=', 1
                    )->where('producto_estado','=','1'
                    )->where(DB::raw('lower(producto_nombre)'), 'like', '%'.strtolower($buscar).'%'
                    )->orderBy('producto_nombre','asc');
    }
    public function scopeProductosByNombreCodigo($query, $buscar){
        return $query->join('empresa','empresa.empresa_id','=','producto.empresa_id')
        ->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1')
        ->where(function ($query) use ($buscar) { 
            $query->where(DB::raw('lower(producto_nombre)'), 'like', '%'.strtolower($buscar).'%')
            ->orwhere(DB::raw('lower(producto_codigo)'), 'like', '%'.strtolower($buscar).'%');
        })->orderBy('producto_nombre','asc');        
    }
    public function scopeProductosByCompraCodigo($query, $buscar){
        return $query->join('empresa','empresa.empresa_id','=','producto.empresa_id')
        ->orwhere('producto.producto_compra_venta','=','1')
        ->orwhere('producto.producto_compra_venta','=','3')
        ->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('producto_estado','=','1')
        ->where(function ($query) use ($buscar) { 
            $query->where(DB::raw('lower(producto_nombre)'), 'like', '%'.strtolower($buscar).'%')
            ->orwhere(DB::raw('lower(producto_codigo)'), 'like', '%'.strtolower($buscar).'%');
        })->orderBy('producto_nombre','asc');        
    }
}
