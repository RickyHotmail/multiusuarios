<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Mantenimiento extends Model
{
    use HasFactory;
    protected $table="tipo_mantenimiento";
    protected $primaryKey="tipo_id";
    public $timestamps=true;
    protected $fillable=[
        'tipo_nombre',
        'tipo_estado',
        'empresa_id',
    ];

    protected $guarded =[

    ];

    public function scopeTipo($query, $id){
        return $query->where('tipo_id', "=", $id);
    }

    public function scopeTipos($query){
        //return $query->where('empresa_id', "=", Auth::user()->empresa_id);
        return $query->where('empresa_id', "=", 1);
    }
}