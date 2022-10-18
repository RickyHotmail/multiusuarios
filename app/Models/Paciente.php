<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Paciente extends Model
{
    use HasFactory;
    protected $table='paciente';
    protected $primaryKey = 'paciente_id';
    public $timestamps = true;
    protected $fillable = [        
        'paciente_cedula',
        'paciente_apellidos',
        'paciente_nombres',
        'paciente_direccion',
        'paciente_fecha_nacimiento',
        'paciente_nacionalidad',
        'paciente_celular',
        'paciente_email',
        'paciente_sexo',
        'paciente_dependiente',
        'paciente_tipo_dependencia',
        'paciente_cedula_afiliado',
        'paciente_nombre_afiliado',
        'paciente_estado',
        'ciudad_id',
        'cliente_id',
        'entidad_id',
        'tipo_identificacion_id',
        'tipod_id',
    ];
    protected $guarded =[
    ];
    public function aseguradora()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function tipoDependencia()
    {
        return $this->belongsTo(Tipo_Dependencia::class, 'tipod_id', 'tipod_id');
    }
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'ciudad_id');
    }
    public function scopePacientes($query){
        return $query->join('cliente', 'cliente.cliente_id','=','paciente.cliente_id'
                    )->join('ciudad', 'ciudad.ciudad_id','=','paciente.ciudad_id'
                    )->join('provincia', 'provincia.provincia_id','=','ciudad.provincia_id'
                    )->join('pais', 'pais.pais_id','=','provincia.pais_id'
                    )->join('entidad', 'entidad.entidad_id','=','paciente.entidad_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('paciente_estado','=','1')->orderBy('paciente_apellidos','asc');
    }
    public function scopePaciente($query, $id){
        return $query->join('cliente', 'cliente.cliente_id','=','paciente.cliente_id'
                    )->join('entidad', 'entidad.entidad_id','=','paciente.entidad_id'
                    )->join('ciudad', 'ciudad.ciudad_id','=','paciente.ciudad_id'
                    )->join('provincia', 'provincia.provincia_id','=','ciudad.provincia_id'
                    )->join('pais', 'pais.pais_id','=','provincia.pais_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('paciente_estado','=','1')->where('paciente_id','=',$id);
    }
    public function scopePacientesByNombre($query, $buscar){
        return $query->join('cliente', 'cliente.cliente_id','=','paciente.cliente_id'
                    )->join('ciudad', 'ciudad.ciudad_id','=','paciente.ciudad_id'
                    )->join('provincia', 'provincia.provincia_id','=','ciudad.provincia_id'
                    )->join('pais', 'pais.pais_id','=','provincia.pais_id'
                    )->join('entidad', 'entidad.entidad_id','=','paciente.entidad_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->where(DB::raw('lower(paciente_nombres)'), 'like', '%'.strtolower($buscar).'%'
                    )->orwhere(DB::raw('lower(paciente_apellidos)'), 'like', '%'.strtolower($buscar).'%'
                    )->orwhere(DB::raw('lower(paciente_cedula)'), 'like', '%'.strtolower($buscar).'%'
                    )->orderBy('paciente_apellidos','asc');
    }
    public function scopeEspecialidadesPaciente($query, $id){
        return $query->join('entidad', 'entidad.entidad_id','=','paciente.entidad_id'
                    )->join('cliente', 'cliente.cliente_id','=','paciente.cliente_id'
                    )->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id'
                    )->join('aseguradora_procedimiento', 'aseguradora_procedimiento.cliente_id','=','cliente.cliente_id'
                    )->join('procedimiento_especialidad', 'procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('especialidad', 'especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('tipo_cliente.tipo_cliente_nombre','=','Aseguradora'
                    )->where('paciente.paciente_id','=',$id)->distinct('especialidad.especialidad_nombre');
    }
    public function scopePacienteTipoIdentificacion($query, $id){
        return $query->join('tipo_identificacion', 'tipo_identificacion.tipo_identificacion_id','=','paciente.tipo_identificacion_id'
                    )->join('ciudad', 'ciudad.ciudad_id','=','paciente.ciudad_id'
                    )->join('provincia', 'provincia.provincia_id','=','ciudad.provincia_id'
                    )->join('pais', 'pais.pais_id','=','provincia.pais_id'
                    )->join('entidad', 'entidad.entidad_id','=','paciente.entidad_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('paciente_estado','=','1')->where('paciente_id','=',$id);
    }
}
