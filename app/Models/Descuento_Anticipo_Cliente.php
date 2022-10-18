<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descuento_Anticipo_Cliente extends Model
{
    use HasFactory;
    protected $table='descuento_anticipo_cliente';
    protected $primaryKey = 'descuento_id';
    public $timestamps=true;
    protected $fillable = [
        'descuento_fecha',
        'descuento_valor',     
        'descuento_descripcion',
        'descuento_estado',
        'anticipo_id', 
        'diario_id',
        'factura_id'
    ];
    protected $guarded =[
    ];
    public function scopeDescuentosAnticipoByFactura($query, $id){
        return $query->where('factura_id','=',$id);
    }
    public function scopeDescuentosAnticipoByFacturaCorte($query, $id, $fecha_corte){
        return $query->where('factura_id','=',$id)->where('descuento_fecha','<=',$fecha_corte);
    }
    public function scopeDescuentosAnticipoByFacturaAfertCorte($query, $id, $fecha_corte){
        return $query->where('factura_id','=',$id)->where('descuento_fecha','>',$fecha_corte);
    }
    public function scopeDescuentosAnticipoByCXC($query, $numero){
        return $query->where('descuento_descripcion','=',$numero);
    }
    public function scopeDescuentosAnticipoByCXCCorte($query, $numero, $fecha_corte){
        return $query->where('descuento_descripcion','=',$numero)->where('descuento_fecha','<=',$fecha_corte);
    }
    public function scopeDescuentosAnticipoByCXCAfterCorte($query, $numero, $fecha_corte){
        return $query->where('descuento_descripcion','=',$numero)->where('descuento_fecha','>',$fecha_corte);
    }
    public function scopeDescuentosAnticipoByClienteFecha($query, $cliente_id, $fecha){
        return $query->join('anticipo_cliente','anticipo_cliente.anticipo_id','=','descuento_anticipo_cliente.anticipo_id')->where('anticipo_cliente.cliente_id','=',$cliente_id)->where('descuento_fecha','<=',$fecha);
    }
    public function scopeDescuentosAnticipo($query, $anticipo_id, $fecha){
        return $query->join('anticipo_cliente','anticipo_cliente.anticipo_id','=','descuento_anticipo_cliente.anticipo_id')->where('anticipo_cliente.anticipo_id','=',$anticipo_id)->where('descuento_fecha','<=',$fecha);
    }
    public function scopeDescuentosByAnticipo($query, $anticipo_id){
        return $query->join('anticipo_cliente','anticipo_cliente.anticipo_id','=','descuento_anticipo_cliente.anticipo_id')->where('anticipo_cliente.anticipo_id','=',$anticipo_id);
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function factura(){
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
}
