<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descuento_Anticipo_Proveedor extends Model
{
    use HasFactory;
    protected $table='descuento_anticipo_proveedor';
    protected $primaryKey = 'descuento_id';
    public $timestamps=true;
    protected $fillable = [
        'descuento_fecha',
        'descuento_valor',    
        'descuento_descripcion',   
        'descuento_estado',        
        'anticipo_id', 
        'diario_id',
        'transaccion_id'
    ];
    protected $guarded =[
    ];
    public function scopeDescuentosAnticipoByFactura($query, $id){
        return $query->where('transaccion_id','=',$id);
    }
    public function scopevalidar($query, $id){
        return $query->where('diario_id','=',$id);
    }
    public function scopeDescuentosAnticipoByFacturaCorte($query, $id, $fecha_corte){
        return $query->where('transaccion_id','=',$id)->where('descuento_fecha','<=',$fecha_corte);
    }
    public function scopeDescuentosAnticipoByFacturaAfterCorte($query, $id, $fecha_corte){
        return $query->where('transaccion_id','=',$id)->where('descuento_fecha','>',$fecha_corte);
    }
    public function scopeDescuentosAnticipoByCXP($query, $numero){
        return $query->where('descuento_descripcion','=',$numero);
    }
    public function scopeDescuentosAnticipoByCXPCorte($query, $numero, $fecha_corte){
        return $query->where('descuento_descripcion','=',$numero)->where('descuento_fecha','<=',$fecha_corte);;
    }
    public function scopeDescuentosAnticipoByCXPAfterCorte($query, $numero, $fecha_corte){
        return $query->where('descuento_descripcion','=',$numero)->where('descuento_fecha','>',$fecha_corte);;
    }
    public function scopeDescuentosAnticipoByProveedorFecha($query, $proveedor_id, $fecha){
        return $query->join('anticipo_proveedor','anticipo_proveedor.anticipo_id','=','descuento_anticipo_proveedor.anticipo_id')->where('anticipo_proveedor.proveedor_id','=',$proveedor_id)->where('descuento_fecha','<=',$fecha);
    }
    public function scopeDescuentosAnticipo($query, $anticipo_id, $fecha){
        return $query->join('anticipo_proveedor','anticipo_proveedor.anticipo_id','=','descuento_anticipo_proveedor.anticipo_id')->where('anticipo_proveedor.anticipo_id','=',$anticipo_id)->where('descuento_fecha','<=',$fecha);
    }
    public function scopeDescuentosByAnticipo($query, $anticipo_id){
        return $query->join('anticipo_proveedor','anticipo_proveedor.anticipo_id','=','descuento_anticipo_proveedor.anticipo_id')->where('anticipo_proveedor.anticipo_id','=',$anticipo_id);
    }
    public function scopeAnticiposPagosFecha($query, $transaccion_id,$fecha_ini, $fecha_fin,$todo){
        $query->where('transaccion_id','=',$transaccion_id)->orderBy('descuento_fecha','asc');
        if($todo != 1){
            $query->where('descuento_fecha','>=',$fecha_ini)->where('descuento_fecha','<=',$fecha_fin);
        }
        return $query;
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function transaccionCompra(){
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
    public function anticipo(){
        return $this->belongsTo(Anticipo_Proveedor::class, 'anticipo_id', 'anticipo_id');
    }
}
