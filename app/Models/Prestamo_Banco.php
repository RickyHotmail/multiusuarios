<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Prestamo_Banco extends Model
{
    use HasFactory;
    protected $table ='prestamo_banco';
    protected $primaryKey = 'prestamo_id';
    public $timestamps=true;
    protected $fillable = [
        'prestamo_inicio',        
        'prestamo_fin',
        'prestamo_monto',
        'prestamo_interes',
        'prestamo_plazo',
        'prestamo_total_interes',
        'prestamo_pago_total',
        'prestamo_observacion',
        'prestamo_estado',
        'cuenta_debe',
        'cuenta_haber',
        'sucursal_id',
        'banco_id',
        'empresa_id',
    ];
    protected $guarded =[
    ]; 
    public function scopePrestamos($query){
        return $query->where('prestamo_banco.empresa_id','=',Auth::user()->empresa_id)->where('prestamo_estado','=','1');
    }
    public function scopePrestamo($query, $id){
        return $query->where('prestamo_banco.empresa_id','=',Auth::user()->empresa_id)->where('prestamo_estado','=','1')->where('prestamo_id','=',$id);
    } 
    public function scopePrestamoBuscar($query, $sucursal, $banco, $estado){
         $query->where('prestamo_banco.empresa_id','=',Auth::user()->empresa_id)
        ->where('prestamo_estado','=','1');
        
        if($sucursal!='0'){
            $query->where('sucursal_id','=',$sucursal);
        }
        if($banco!='0'){
            $query->where('banco_id','=',$banco);
        }
        if($estado!='0'){
            $query->where('prestamo_estado','=',$estado);
        }
        return $query;
    }
    public function detalles(){
        return $this->hasMany(Detalle_Prestamo::class, 'prestamo_id', 'prestamo_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function scopeBancosDistinsc($query){
        return $query->join('banco','banco.banco_id','=','prestamo_banco.banco_id')->join('sucursal','sucursal.sucursal_id','=','prestamo_banco.sucursal_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('prestamo_banco.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeSucursalDistinsc($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','prestamo_banco.sucursal_id')
        ->where('prestamo_banco.empresa_id','=',Auth::user()->empresa_id);
    }
    public function banco(){
        return $this->belongsTo(Banco::class, 'banco_id', 'banco_id');
    } 
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }  
    public function cuentadebe(){
        return $this->belongsTo(Cuenta::class, 'cuenta_debe', 'cuenta_id');
    }
    public function cuentahaber(){
        return $this->belongsTo(Cuenta::class, 'cuenta_haber', 'cuenta_id');
    }
}
