<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Medico extends Model
{
    use HasFactory;
    protected $table='medico';
    protected $primaryKey = 'medico_id';
    public $timestamps = true;
    protected $fillable = [        
        'medico_estado',
        'empresa_id',
        'empleado_id',
        'proveedor_id',
        'user_id',
    ];
    protected $guarded =[
    ];
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function detalles()
    {
        return $this->hasMany(Medico_Especialidad::class, 'medico_id', 'medico_id');
    }    
    public function aseguradoras()
    {
        return $this->hasManyThrough(Cliente::class, Medico_Aseguradora::class, 'medico_id', 'cliente_id', 'medico_id', 'cliente_id');
    }
    public function scopeMedicos($query)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('medico_estado', '=', '1');
    }
    public function scopeMedico($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('medico_id', '=', $id);
    }
    public function scopeMedicoME($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('empleado_id', '=', $id);
    }
}
