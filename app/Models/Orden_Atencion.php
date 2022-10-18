<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Orden_Atencion extends Model
{
    use HasFactory;
    protected $table='orden_atencion';
    protected $primaryKey = 'orden_id';
    public $timestamps=true;
    protected $fillable = [
        'orden_codigo',
        'orden_numero', 
        'orden_secuencial',  
        'orden_reclamo', 
        'orden_secuencial_reclamo', 
        'orden_fecha',
        'orden_hora',
        'orden_observacion',
        'orden_iess',
        'orden_frecuencia',
        'orden_dependencia',
        'orden_cedula_afiliado',
        'orden_nombre_afiliado',
        'orden_precio',
        'orden_cobertura_porcentaje',
        'orden_cobertura',
        'orden_copago',
        'orden_estado',
        'sucursal_id',
        'paciente_id',
        'tipo_id',
        'tipod_id',
        'factura_id',
        'medico_id',
        'especialidad_id',
        'entidad_id',
        'cliente_id', //Es una seguradora del paciente Nota: No es un cliente 
        'producto_id', //procedimiento
    ];
    protected $guarded =[
    ];
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'paciente_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id', 'medico_id');
    }
    public function procedimientoA()
    {
        return $this->belongsTo(Aseguradora_Procedimiento::class, 'procedimientoA_id', 'procedimientoA_id');
    }
    public function tipoSeguro()
    {
        return $this->belongsTo(Tipo_Seguro::class, 'tipo_id', 'tipo_id');
    }
    public function factura()
    {
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'orden_id', 'orden_id');
    }
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'especialidad_id');
    }
    public function documentos()
    {
        return $this->hasMany(Documento_Cita_Medica::class, 'orden_id', 'orden_id');
    }
    
    public function scopeOrdenCitaDisponible($query, $medico_id, $especialidad_id, $fecha1, $fecha2){
        return $query->where('medico_id','=',$medico_id
                    )->where('especialidad_id','=',$especialidad_id
                    )->where('orden_fecha', "=", "'$fecha1'"
                    )->whereBetween('orden_hora', ["'$fecha1'","'$fecha2'"]);
    }

    public function scopeOrdenCitaDisponibleHora($query, $medico_id, $especialidad_id, $fecha){
        return $query->where('medico_id','=',$medico_id
                    )->where('especialidad_id','=',$especialidad_id
                    )->where('orden_fecha', "=", "'$fecha'"
                    )->where('orden_hora', "=", "'$fecha'");
    }

    public function scopeOrdenesByFechaSucEsp($query,$fechaI,$fechaF,$sucursal, $esp){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_fecha','>=',$fechaI
                    )->where('orden_fecha','<=',$fechaF
                    )->where('orden_atencion.sucursal_id','=',$sucursal
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('especialidad_id', "=", $esp
                    )->orderBy('orden_atencion.orden_fecha','desc'
                    )->orderBy('orden_atencion.orden_hora','desc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
    }

    public function scopeOrdenesByFechaSuc($query,$fechaI,$fechaF,$sucursal){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_fecha','>=',$fechaI
                    )->where('orden_fecha','<=',$fechaF
                    )->where('orden_atencion.sucursal_id','=',$sucursal
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','desc'
                    )->orderBy('orden_atencion.orden_hora','desc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
                    
    }

    public function scopeOrdenesByFechaSucPac($query,$fechaI,$fechaF,$sucursal, $paciente_id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_fecha','>=',$fechaI
                    )->where('orden_fecha','<=',$fechaF
                    //)->where('orden_atencion.sucursal_id','=',$sucursal
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('orden_atencion.paciente_id','=', $paciente_id
                    )->orderBy('orden_atencion.orden_secuencial','asc');
                    //)->orderBy('orden_atencion.orden_hora','desc');
    }

    public function scopeOrdenesByFechaSucParticulares($query,$fechaI,$fechaF,$sucursal=null, $paciente=null){
        $query=$query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->join('entidad', 'entidad.entidad_id', "=", 'orden_atencion.entidad_id'
                    )->where('orden_fecha','>=',"$fechaI"
                    )->where('orden_fecha','<=',"$fechaF"
                    )->where('orden_iess', '=', 0,
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','desc'
                    )->orderBy('orden_atencion.orden_hora','desc'
                    )->orderBy('orden_atencion.orden_id', 'desc');

        if($sucursal!=null)  $query->where('sucursal.sucursal_id', "=", $sucursal);
        if($paciente!=null)  $query->where('orden_atencion.paciente_id', "=", $paciente);
    
        return $query;
    }

    public function scopeOrdenesByFechaSucIess($query,$fechaI,$fechaF,$sucursal){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_fecha','>=',$fechaI
                    )->where('orden_fecha','<=',$fechaF
                    )->where('orden_atencion.sucursal_id','=',$sucursal
                    )->where('orden_iess', '=', 1,
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','desc'
                    )->orderBy('orden_atencion.orden_hora','desc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
    }

    public function scopeOrdenesByFechaSucNoIess($query,$fechaI,$fechaF,$sucursal){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_fecha','>=',$fechaI
                    )->where('orden_fecha','<=',$fechaF
                    )->where('orden_atencion.sucursal_id','=',$sucursal
                    )->where('orden_iess', '=', 0,
                    )->where('orden_estado', '=', 4,
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','asc'
                    )->orderBy('orden_atencion.orden_hora','asc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
    }
    
    public function scopeOrdenes($query){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'     
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('medico_especialidad','medico_especialidad.especialidad_id','=','orden_atencion.especialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','asc'
                    )->orderBy('orden_atencion.orden_hora','asc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
    }
    public function scopeOrdenesHoy($query){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'     
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    //)->join('medico_especialidad','medico_especialidad.especialidad_id','=','orden_atencion.especialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','orden_atencion.especialidad_id'
                    )->join('medico','medico.medico_id','=','orden_atencion.medico_id'
                    )->where('orden_fecha','=',date("Y-m-d")
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','asc'
                    )->orderBy('orden_atencion.orden_hora','asc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
    }
    public function scopeSignosVitales($query){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->where('orden_fecha','=',date("Y-m-d")
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->orderBy('orden_atencion.orden_fecha','asc'
                    )->orderBy('orden_atencion.orden_hora','asc'
                    )->orderBy('orden_atencion.orden_id', 'desc');
    }
    public function scopeOrden($query, $id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('medico_especialidad','medico_especialidad.especialidad_id','=','orden_atencion.especialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_atencion.orden_id','=',$id)->orderBy('orden_atencion.orden_id','asc');
    }
    public function scopeMedicoOrden($query){
        return $query->join('medico_especialidad','medico_especialidad.mespecialidad_id','=','orden_atencion.mespecialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('medico.empresa_id', '=', Auth::user()->empresa_id)->orderBy('orden_atencion.orden_id','asc');
    }

    public function scopeOrdenFecha($query, $buscar){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('medico_especialidad','medico_especialidad.mespecialidad_id','=','orden_atencion.mespecialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('orden_atencion.orden_estado','=',$buscar)->orderBy('orden_atencion.orden_id','asc');
    }
    public function scopeclienteaseguradora($query, $id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->join('cliente','cliente.cliente_id','=','orden_atencion.paciente_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cliente.cliente_id','=',$id);
    }
    public function scopeHistorial($query, $id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_estado','>=','3'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('paciente_id','=',$id
                    )->orderByDesc('orden_atencion.orden_fecha');
    }
    
    public function scopeOrdenProcedimientoId($query, $id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('medico_especialidad','medico_especialidad.especialidad_id','=','orden_atencion.especialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimientoA_id','=','orden_atencion.procedimientoA_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('aseguradora_procedimiento.cliente_id','=',$id);
    }
    public function scopesecuencialr($query, $id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_atencion.cliente_id','=',$id);
    }
}
