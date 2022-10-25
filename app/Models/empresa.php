<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Empresa extends Model
{
    use HasFactory;
    protected $table='empresa';
    protected $primaryKey = 'empresa_id';
    public $timestamps=true;
    protected $fillable = [
        'empresa_ruc',
        'empresa_nombreComercial',
        'empresa_razonSocial', 
        'empresa_direccion',
        'empresa_telefono',
        'empresa_celular',
        'empresa_logo',
        'empresa_ciudad', 
        'empresa_cedula_representante', 
        'empresa_representante', 
        'empresa_contador', 
        'empresa_cedula_contador', 
        'empresa_fecha_ingreso',
        'empresa_email',
        'empresa_llevaContabilidad', 
        'empresa_tipo',  
        'empresa_contribuyenteEspecial',  
        'empresa_contabilidad',
        'empresa_electronica',
        'empresa_nomina',
        'empresa_medico',
        'empresa_estado_cambiar_precio', 
        'empresa_estado', 

    ];
    protected $guarded =[
    ];
    public function scopeEmpresas($query){
        return $query->where('empresa_estado','=','1')->orderBy('empresa_razonSocial','asc');
    }
    public function scopeEmpresa($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id);
    }
    public function firmaElectronica(){
        return $this->hasOne(Firma_Electronica::class,'empresa_id','empresa_id');
    }
    public function emailEmpresa(){
        return $this->hasOne(Email_Empresa::class,'empresa_id','empresa_id');
    }

    public function documentosOrdenAtencion(){
        return $this->hasMany(Documento_Orden_Atencion::class,'empresa_id','empresa_id');
    }

    public function suscripcion(){
        return $this->belongsTo(Suscripcion::class, 'empresa_id', 'empresa_id');
    }
}
