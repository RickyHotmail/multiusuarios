<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Parametrizar_Prioridad extends Model
{
    use HasFactory;
    protected $table='parametrizar_prioridad';
    protected $primaryKey='prioridad_id';
    public $timestamps=true;

    protected $fillable=[
        'prioridad_descripcion',
        'prioridad_desde',
        'prioridad_hasta',
        'prioridad_estado',
        'empresa_id'
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

    protected $guarded=[
    ];

    public function scopePrioridades($query){
        return $query->where('empresa_id', '=', Auth::user()->empresa_id);
    }
}
