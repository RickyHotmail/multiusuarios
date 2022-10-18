<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Documento_Orden_Atencion extends Model
{
    use HasFactory;
    protected $table='documento_orden_atencion';
    protected $primaryKey = 'documento_id';
    public $timestamps=true;
    protected $fillable = [
        'documento_nombre',           
        'documento_estado',  
        'empresa_id',        
    ];
    protected $guarded =[
    ];

    public function scopeDocumentosOrdenesAtencion($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('documento_estado','=','1')->orderBy('documento_nombre','asc');
    }
    public function scopeDocumentoOrdenAtencion($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('documento_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
