<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_GR extends Model
{
    use HasFactory;

    protected $table='detalle_gr';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_cantidad',
        'detalle_estado',
        'gr_id',  
        'producto_id', 
        
    ];
    protected $guarded =[
    ];

    public function scopeDetalleProforma($query, $guiaID){
        return $query->join('producto','producto.producto_id','=','detalle_gr.producto_id')->where('proforma_id','=', $guiaID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalle($query, $guiaID){
        return $query->join('producto','producto.producto_id','=','detalle_gr.producto_id')->where('gr_id','=', $guiaID)->orderBy('detalle_id','asc');
    }
    public function guiaremision()
    {
        return $this->belongsTo(Guia_Remision::class, 'gr_id', 'gr_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
