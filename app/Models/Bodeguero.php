<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bodeguero extends Model
{
    use HasFactory;
    protected $table ='bodeguero';
    protected $primaryKey = 'bodeguero_id';
    public $timestamps = true;
    protected $fillable = [        
        'bodeguero_cedula',
        'bodeguero_nombre', 
        'bodeguero_direccion',
        'bodeguero_telefono',
        'bodeguero_email',
        'bodeguero_fecha_ingreso',
        'bodeguero_fecha_salida',
        'bodeguero_estado',
        'bodega_id',
    ];
    protected $guarded =[
    ];

    public function scopeBodegueros($query){
        return $query->join('bodega','bodega.bodega_id','=','bodeguero.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('bodeguero_estado','=','1')->orderBy('bodeguero_nombre','asc');
    }
    public function scopeBodeguero($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','bodeguero.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('bodeguero_id','=',$id);
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
}
