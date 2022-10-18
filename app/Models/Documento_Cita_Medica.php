<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento_Cita_Medica extends Model
{
    use HasFactory;
    protected $table='documento_cita_medica';
    protected $primaryKey = 'doccita_id';
    public $timestamps=true;
    protected $fillable = [
        'doccita_nombre',           
        'doccita_url',  
        'doccita_estado',  
        'orden_id',        
    ];
    protected $guarded =[
    ];

    public function scopeDocumentoCita($query, $orden_id, $documento_id){
        return $query->join('orden_atencion', 'orden_atencion.orden_id','=','documento_cita_medica.documento_id')
                     ->where('orden_atencion.orden_id','=', $orden_id)
                     ->where('documento_cita_medica.documento_id','=',$documento_id);
    }

}
