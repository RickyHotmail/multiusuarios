<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Proveedor extends Model
{
    use HasFactory;
    protected $table='proveedor';
    protected $primaryKey = 'proveedor_id';
    public $timestamps=true;
    protected $fillable = [
        'proveedor_ruc',
        'proveedor_nombre', 
        'proveedor_nombre_comercial',    
        'proveedor_gerente',
        'proveedor_direccion',
        'proveedor_telefono', 
        'proveedor_celular',    
        'proveedor_email',
        'proveedor_actividad',
        'proveedor_fecha_ingreso',
        'proveedor_tipo',
        'proveedor_lleva_contabilidad',
        'proveedor_contribuyente',
        'proveedor_cuenta_pagar',
        'proveedor_cuenta_anticipo',
        'tipo_sujeto_id',
        'tipo_identificacion_id',
        'ciudad_id',
        'categoria_proveedor_id',        
        'proveedor_estado',
    ];
    protected $guarded =[
    ];
    public function scopeProveedores($query){
        return $query->join('categoria_proveedor', 'categoria_proveedor.categoria_proveedor_id','=','proveedor.categoria_proveedor_id')->where('categoria_proveedor.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_estado','=','1')->orderBy('proveedor_nombre','asc');
    }
    public function scopeProveedor($query, $id){
        return $query->join('categoria_proveedor', 'categoria_proveedor.categoria_proveedor_id','=','proveedor.categoria_proveedor_id')->where('categoria_proveedor.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_id','=',$id);
    }
    public function scopeProveedoresAnticipos($query){
        return $query->join('anticipo_proveedor','anticipo_proveedor.proveedor_id','=','proveedor.proveedor_id')->join('categoria_proveedor', 'categoria_proveedor.categoria_proveedor_id','=','proveedor.categoria_proveedor_id')->where('categoria_proveedor.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_estado','=','1')->select('proveedor.proveedor_id','proveedor_nombre')->distinct()->orderBy('proveedor_nombre','asc')->orderBy('proveedor.proveedor_id','asc');
    }
    public function scopeProveedoresByNombre($query, $buscar){
        return $query->join('categoria_proveedor', 'categoria_proveedor.categoria_proveedor_id','=','proveedor.categoria_proveedor_id')->where('categoria_proveedor.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_estado','=','1')->where(DB::raw('lower(proveedor_nombre)'), 'like', '%'.strtolower($buscar).'%')->orderBy('proveedor_nombre','asc');
    }
    public function scopeexiste($query, $ruc){
        return $query->join('categoria_proveedor', 'categoria_proveedor.categoria_proveedor_id','=','proveedor.categoria_proveedor_id')->where('categoria_proveedor.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_estado','=','1')->where('proveedor_ruc','=',$ruc)->orderBy('proveedor_nombre','asc');
    }
    
    public function scopeProveedoresByRuc($query, $ruc){
        return $query->join('categoria_proveedor', 'categoria_proveedor.categoria_proveedor_id','=','proveedor.categoria_proveedor_id')->where('categoria_proveedor.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_estado','=','1')->where('proveedor_ruc','=',$ruc)->orderBy('proveedor_nombre','asc');
    }
    public function cuentaPagar()
    {
        return $this->belongsTo(Cuenta::class, 'proveedor_cuenta_pagar', 'cuenta_id');
    }
    public function cuentaAnticipo()
    {
        return $this->belongsTo(Cuenta::class, 'proveedor_cuenta_anticipo', 'cuenta_id');
    }
    public function tipoSujeto()
    {
        return $this->belongsTo(Tipo_Sujeto::class, 'tipo_sujeto_id', 'tipo_sujeto_id');
    }
    public function tipoIdentificacion()
    {
        return $this->belongsTo(Tipo_Identificacion::class, 'tipo_identificacion_id', 'tipo_identificacion_id');
    }    
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'ciudad_id');
    }
    public function categoriaProveedor()
    {
        return $this->belongsTo(Categoria_Proveedor::class, 'categoria_proveedor_id', 'categoria_proveedor_id');
    }
}
