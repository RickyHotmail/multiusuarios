<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Proforma extends Model
{
    use HasFactory;
    protected $table='detalle_proforma';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_cantidad',
        'detalle_precio_unitario',       
        'detalle_descuento',        
        'detalle_iva',       
        'detalle_total', 
        'detalle_estado',
        'proforma_id',   
        'producto_id'
    ];
    protected $guarded =[
    ];

    public function scopeDetalleProforma($query, $proformaID){
        return $query->join('producto','producto.producto_id','=','detalle_proforma.producto_id')->where('proforma_id','=', $proformaID)->orderBy('detalle_id','asc');
    }
   

    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'proforma_id', 'proforma_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
