<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Entidad extends Model
{
    use HasFactory;
    protected $table='entidad';
    protected $primaryKey = 'entidad_id';
    public $timestamps = true;
    protected $fillable = [     
        'entidad_nombre',   
        'entidad_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeEntidades($query)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('entidad_estado', '=', '1');
    }
    public function scopeEntidad($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('entidad_id', '=', $id);
    }
    public function detalles()
    {
        return $this->hasMany(Entidad_Aseguradora::class, 'entidad_id', 'entidad_id');
    }
    public function aseguradoras()
    {
        return $this->hasManyThrough(Cliente::class, Entidad_Aseguradora::class, 'entidad_id', 'cliente_id', 'entidad_id', 'cliente_id');
    }
}
