<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Parametrizar_Orden extends Model
{
    use HasFactory;
    protected $table='parametrizar_orden';
    protected $primaryKey="parametrizar_id";
    public $timestamps=true;

    protected $fillable=[
        'parametrizar_descripcion',
        'parametrizar_porcentaje',
        'parametrizar_estado',
        'empresa_id'
    ];

    protected $guarded=[
    ];

    public function scopeParametrizacionOrdenes($query){
        return $query->where('empresa_id', '=', Auth::user()->empresa_id);
    }

    public function valores(){
        return $this->hasMany(Parametrizar_Detalle_Orden::class, 'parametrizar_id', 'parametrizar_id');
    }
}
