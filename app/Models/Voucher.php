<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $table='voucher';
    protected $primaryKey = 'voucher_id';
    public $timestamps = true;
    protected $fillable = [
        'voucher_nombre',
        'voucher_numero',
        'voucher_valor',
        'voucher_estado',        
        'empresa_id',       
    ];
    protected $guarded =[
    ];   
    public function detalleDiario()
    {
        return $this->hasMany(Detalle_Diario::class, 'voucher_id', 'voucher_id');
    }
}
