<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Vendedor extends Model
{
    use HasFactory;
    protected $table='vendedor';
    protected $primaryKey = 'vendedor_id';
    public $timestamps=true;
    protected $fillable = [
        'vendedor_cedula',
        'vendedor_nombre', 
        'vendedor_direccion',
        'vendedor_telefono',
        'vendedor_email',
        'vendedor_comision_porcentaje',
        'vendedor_fecha_ingreso',
        'vendedor_fecha_salida',
        'vendedor_estado',
        'zona_id',
    ];
    protected $guarded =[
    ];
    public function scopeVendedores($query){
        return $query->join('zona','zona.zona_id','=','vendedor.zona_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('vendedor_estado','=','1')->orderBy('vendedor_nombre','asc');
    
    }
    public function scopeVendedor($query, $id){
        return $query->join('zona','zona.zona_id','=','vendedor.zona_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('vendedor_id','=',$id);
    
    }  
    public function zona(){
        return $this->belongsTo(Zona::class, 'zona_id', 'zona_id');
    }
}
