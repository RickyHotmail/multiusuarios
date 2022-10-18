<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Comprobante extends Model
{
    use HasFactory;
    protected $table='tipo_comprobante';
    protected $primaryKey = 'tipo_comprobante_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_comprobante_codigo',
        'tipo_comprobante_nombre', 
        'tipo_comprobante_estado'
    ];
    protected $guarded =[
    ];  
    public function scopeTipos($query){
        return $query->where('tipo_comprobante_estado','=','1')->orderBy('tipo_comprobante_codigo','asc');
    }
    public function scopeTipo($query, $id){
        return $query->where('tipo_comprobante_id','=',$id);
    }
    public function scopeTipoByNombre($query, $nombre){
        return $query->where('tipo_comprobante_nombre','=',$nombre);
    }
    public function rangoDocumento()
    {
        return $this->hasOne(Rango_Documento::class, 'tipo_comprobante_id', 'tipo_comprobante_id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
