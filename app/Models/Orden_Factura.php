<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Orden_Factura extends Model
{
    use HasFactory;
    protected $table ='orden_factura';
    protected $primaryKey = 'orden_id';
    public $timestamps=true;
    protected $fillable = [   
        'orden_observacion',        
        'orden_estado',        
        'expediente_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeDetalleFacturas($query){
        return $query->join('expediente','expediente.expediente_id','=','orden_factura.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id       
                    )->where('orden_factura.orden_estado','=','1');                       
    }
    public function scopeDetalleFactura($query, $id){
        return $query->join('expediente','expediente.expediente_id','=','orden_factura.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id      
                    )->where('orden_factura.orden_id','=',$id);
    }
    public function detalleOfactura()
    {
        return $this->hasMany(Detalle_OFactura::class, 'orden_id', 'orden_id');
    }
}
