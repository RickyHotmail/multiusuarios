<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametrizar_Detalle_Orden extends Model
{
    use HasFactory;
    protected $table='parametrizar_detalle_orden';
    protected $primaryKey='parametrizard_id';
    public $timestamps=true;

    protected $fillable=[
        'parametrizard_descripcion',
        'parametrizard_valor',
        'parametrizard_estado',
        'parametrizar_id'
    ];

    protected $guarded=[
    ];

}
