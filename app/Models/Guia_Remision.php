<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Guia_Remision extends Model
{
    use HasFactory;
    protected $table='guia_remision';
    protected $primaryKey = 'gr_id';
    public $timestamps=true;
    protected $fillable = [
        'gr_numero',
        'gr_serie',
        'gr_secuencial',
        'gr_fecha', 
        'gr_fecha_inicio', 
        'gr_fecha_fin', 
        'gr_punto_partida', 
        'gr_punto_destino', 
        'gr_ruta', 
        'gr_placa', 
        'gr_motivo',
        'gr_comentario',
        'gr_doc_aduanero',
        'gr_emision', 
        'gr_ambiente', 
        'gr_autorizacion',        
        'gr_xml_nombre',  
        'gr_xml_estado', 
        'gr_xml_mensaje', 
        'gr_xml_respuestaSRI',
        'gr_xml_fecha',
        'gr_xml_hora',
        'gr_estado', 
        'cliente_id', 
        'factura_id',        
        'transportista_id',
        'bodega_id', 
        'documento_anulado_id', 
        'rango_id',        
        
    ];
    protected $guarded =[
    ];
    public function scopeClientesDistinsc($query){
        return $query->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('tipo_identificacion','tipo_identificacion.tipo_identificacion_id','=','cliente.tipo_identificacion_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_estado','=','1')->orderBy('cliente.cliente_nombre','asc');
    }
    public function scopeEstadoDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('gr_estado','asc');
    }
    public function scopeSucursalDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeGuias($query){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_estado','=','1')->orderBy('gr_numero','asc');
    }
    public function scopeGuiasHoy($query){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_estado','=','1')->orderBy('gr_numero','asc');
    }
    public function scopeGuiasOrdenes($query){
        return $query->join('orden_despacho','orden_despacho.gr_id','=','guia_remision.gr_id')->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_estado','=','1')->orderBy('gr_numero','asc');
    }
    public function scopeGuiasTodos($query){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasTodosDiferentes($query,$fechatodo,$fecha_desde,$fecha_hasta,$cliente,$estado,$sucursal){
        $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('gr_numero','asc');
        if($fechatodo != 'on'){
            $query ->where('gr_fecha', '>=', $fecha_desde)->where('gr_fecha', '<=', $fecha_hasta);
        }  
        if($estado != '--TODOS--'){
            $query ->where('guia_remision.gr_estado', '=', $estado);
        } 
        if($sucursal != '0'){
            $query ->where('sucursal.sucursal_id', '=', $sucursal);
        }
        if($cliente != '0'){
            $query->where('cliente.cliente_id', '=', $cliente);
        }   
        return $query;
    }
    public function scopeGuiasFecha($query,$fechadesde,$fechahasta){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=', $fechadesde)
        ->where('gr_fecha', '<=', $fechahasta)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('gr_fecha','>=',$fechaInicio)->where('gr_fecha','<=',$fechaFin);
    }
    public function scopeGuiasbyFechaSucrusal($query, $fechaInicio, $fechaFin, $sucursal){
        return $query->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha','>=',$fechaInicio)->where('gr_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal);
    }
    public function scopeGuiasbyNumero($query, $numero){
        return $query->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('gr_numero', 'like', '%'.$numero.'%');
    }
    public function scopeGuiasCliente($query,$cliente){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente_nombre','=',$cliente)
        ->orderBy('gr_numero','asc');
    }
    public function scopeGuiasSucursal($query,$sucursal){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->orderBy('gr_numero','asc');
    }
    public function scopeGuiasEstado($query,$estado){
        return $query->join('transportista', 'transportista.transportista_id', '=', 'guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)->orderBy('gr_numero','asc');
    }
    public function scopeGuia($query, $id){
        return $query->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_id','=',$id);
    }
    public function scopeGuiaOrden($query, $id){
        return $query->join('orden_despacho','orden_despacho.gr_id','=','guia_remision.gr_id')->join('vendedor','vendedor.vendedor_id','=','orden_despacho.vendedor_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('guia_remision.gr_id','=',$id);
    }
    public function scopeGuiaFiltrar($query, $fechaInicio, $fechaFin,$numeroDoc){
        return $query->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_fecha','>=',$fechaInicio)->where('gr_fecha','<=',$fechaFin)->where('gr_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeGuiasucursalFiltrar($query, $fechaInicio, $fechaFin,$numeroDoc,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_fecha','>=',$fechaInicio)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->where('gr_fecha','<=',$fechaFin)->where('gr_numero','like','%'.$numeroDoc.'%');
    }

    public function scopeGuiasFechaClientesurcursal($query, $desde,$hasta, $cliente, $sucursal){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('guia_remision.gr_fecha', '>=',$desde)->where('guia_remision.gr_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasFechaEstadosurcursal($query, $desde,$hasta, $estado, $sucursal){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('gr_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasFechaEstadoCliente($query, $desde,$hasta, $cliente, $estado){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('gr_estado','=',$estado)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasFechaEstado($query, $desde,$hasta, $estado){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('gr_estado','=',$estado)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasEstadoClientesurcursal($query, $estado, $cliente, $sucursal){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasEstadoCliente($query, $estado, $cliente){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)
        ->where('cliente.cliente_nombre', '=', $cliente)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasEstadosurcursal($query, $estado,$sucursal){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasFechaCliente($query, $desde,$hasta, $cliente){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasFechasurcursal($query, $desde,$hasta, $sucursal){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->orderBy('gr_numero','asc');
    }
    public function scopeGuiasClientesurcursal($query,$cliente, $sucursal){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','guia_remision.cliente_id')->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)->orderBy('gr_numero','asc');
    }

    public function scopeGuiaDetalle($query, $id){
        return $query->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('orden_despacho','orden_despacho.gr_id','=','guia_remision.gr_id')->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')->join('producto','producto.producto_id','=','detalle_orden.producto_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_despacho.gr_id','=',$id);
    }
    
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','guia_remision.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('guia_remision.rango_id','=',$id);
    }

    public function detalles(){
        return $this->hasMany(Detalle_GR::class, 'gr_id', 'gr_id');
    }
    
    public function ordenes(){
        return $this->hasMany(Orden_Despacho::class, 'gr_id', 'gr_id');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function Factura()
    {
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    
    public function Transportista()
    {
        return $this->belongsTo(Transportista::class, 'transportista_id', 'transportista_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function documentoAnulado(){
        return $this->belongsTo(Documento_Anulado::class, 'documento_anulado_id', 'documento_anulado_id');
    }

    public function empresa(){
        return $this->hasOneThrough(Empresa::class, Transportista::class,'transportista_id','empresa_id','transportista_id','empresa_id');
    }

}
