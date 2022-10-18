<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Documento_Orden_Paciente extends Model
{
    use HasFactory;
    protected $table='documento_orden_paciente';
    protected $primaryKey = 'docpaciente_id';
    public $timestamps=true;
    protected $fillable = [
        'docpaciente_url',           
        'docpaciente_estado',  
        'orden_id',
        'documento_id'
    ];

    protected $guarded =[
    ];

    public function scopeDocumentosOrdenesPacientes($query, $ordenId){
        return $query->join('orden_atencion', 'orden_atencion.orden_id', 'documento_orden_paciente.orden_id'
            )->where('orden_atencion.orden_id', '=', $ordenId
            )->where('orden_atencion.empresa_id','=',Auth::user()->empresa_id);

        //return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('documento_estado','=','1')->orderBy('documento_nombre','asc');
    }
    public function scopeDocumentoOrdenPaciente($query, $ordenId, $documentoId){
        return $query->where('orden_id', '=', $ordenId
            )->where('docpaciente_id','=', Auth::user()->documentoId);


        //return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('documento_id','=',$id);
    }
    public function ordenAtencion(){
        return $this->belongsTo(Orden_Atencion::class, 'orden_id', 'orden_id');
    }

    public function Documento_Orden_Atencion(){
        return $this->belongsTo(Documento_Orden_Atencion::class, 'documento_id', 'documento_id');
    }
}
