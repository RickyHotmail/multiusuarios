<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    use HasFactory;
    protected $table='cliente';
    protected $primaryKey = 'cliente_id';
    public $timestamps=true;
    protected $fillable = [
        'cliente_cedula',
        'cliente_nombre', 
        'cliente_abreviatura', 
        'cliente_direccion',    
        'cliente_telefono',
        'cliente_celular',
        'cliente_email',
        'cliente_fecha_ingreso', 
        'cliente_lleva_contabilidad',    
        'cliente_tiene_credito',
        'cliente_cuenta_cobrar',
        'cliente_cuenta_anticipo',
        'cliente_prestamo',
        'cliente_estado',
        'ciudad_id',
        'tipo_identificacion_id',
        'tipo_cliente_id',
        'credito_id',
        'categoria_cliente_id',
        'lista_id',
    ];
    protected $guarded =[
    ];
    public function scopeClientes($query){
        return $query->join('categoria_cliente', 'categoria_cliente.categoria_cliente_id','=','cliente.categoria_cliente_id')->where('categoria_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')->orderBy('cliente_nombre','asc');
    }
    public function scopeCliente($query, $id){
        return $query->join('categoria_cliente', 'categoria_cliente.categoria_cliente_id','=','cliente.categoria_cliente_id')->where('categoria_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_id','=',$id);
    }
    public function scopeClientesAnticipos($query){
        return $query->join('anticipo_cliente','anticipo_cliente.cliente_id','=','cliente.cliente_id')->join('categoria_cliente', 'categoria_cliente.categoria_cliente_id','=','cliente.categoria_cliente_id')->where('categoria_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')->select('cliente.cliente_id','cliente_nombre')->distinct()->orderBy('cliente_nombre','asc')->orderBy('cliente.cliente_id','asc');
    }         
    public function scopeClientesByNombre($query, $buscar){
        return $query->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')
        ->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')
        ->where(DB::raw('lower(cliente_nombre)'), 'like', '%'.strtolower($buscar).'%')->orderBy('cliente_nombre','asc');
    }
    public function scopeClientesByCedula($query, $cedula){
        return $query->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->join('credito','credito.credito_id','=','cliente.credito_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')->where('cliente_cedula','=',$cedula)->orderBy('cliente_nombre','asc');
    }
    public function scopeexiste($query, $cedula){
        return $query->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->join('credito','credito.credito_id','=','cliente.credito_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')
        ->where('cliente_cedula','=',$cedula);
    }
    public function scopeClientesByCedulaRuc($query, $cedula){
        return $query->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id'
        )->join('credito','credito.credito_id','=','cliente.credito_id'
        )->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')
        ->where('cliente_cedula','=',$cedula)
        ->orwhere('cliente_cedula','=',$cedula.'001')
        ->orderBy('cliente_nombre','asc');
    }

    public function scopeClientesByCedulaNombre($query, $cedula){
        return $query->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')
        ->join('credito','credito.credito_id','=','cliente.credito_id')
        //->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente_estado','=','1')
        ->where('cliente_cedula','like', '%'.$cedula.'%')
        ->orwhere('cliente_cedula','=','%'.$cedula.'001')
        ->orwhere(DB::raw('lower(cliente_nombre)'),'like','%'.strtolower($cedula).'%')
        ->orderBy('cliente_nombre','asc');
    }


    public function scopeClienteAseguradora($query){
        return $query->join('tipo_cliente', 'cliente.tipo_cliente_id', '=', 'tipo_cliente.tipo_cliente_id'
                    )->where('tipo_cliente.tipo_cliente_nombre', '=', 'Aseguradora'
                    )->where('cliente.cliente_estado','=', 1);
      //  $clientesAseguradoras = DB::table('cliente')->select('cliente_id', 'cliente_cedula', 'cliente_nombre', 'categoria_cliente_id', 'cliente_estado', 'tipo_cliente.tipo_cliente_nombre')->join('tipo_cliente', 'cliente.tipo_cliente_id', '=', 'tipo_cliente.tipo_cliente_id')->where('tipo_cliente.tipo_cliente_nombre', '=', 'Aseguradora')->get();
    }
    public function scopeClientesSaldoAnticipo($query, $fecha){
        return $query->join('categoria_cliente', 'categoria_cliente.categoria_cliente_id','=','cliente.categoria_cliente_id')->where('categoria_cliente.empresa_id','=',Auth::user()->empresa_id)->where('cliente_estado','=','1')->orderBy('cliente_nombre','asc');
    }
    public function cuentaCobrar()
    {
        return $this->belongsTo(Cuenta::class, 'cliente_cuenta_cobrar', 'cuenta_id');
    }
    public function cuentaAnticipo()
    {
        return $this->belongsTo(Cuenta::class, 'cliente_cuenta_anticipo', 'cuenta_id');
    }
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'ciudad_id');
    }
    public function tipoIdentificacion()
    {
        return $this->belongsTo(Tipo_Identificacion::class, 'tipo_identificacion_id', 'tipo_identificacion_id');
    }
    public function tipoCliente()
    {
        return $this->belongsTo(Tipo_Cliente::class, 'tipo_cliente_id', 'tipo_cliente_id');
    }
    public function credito()
    {
        return $this->belongsTo(Credito::class, 'credito_id', 'credito_id');
    }
    public function categoriaCliente()
    {
        return $this->belongsTo(Categoria_Cliente::class, 'categoria_cliente_id', 'categoria_cliente_id');
    }
    public function listaPrecio()
    {
        return $this->belongsTo(Lista_Precio::class, 'lista_id', 'lista_id');
    }
}
