<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol_Permiso extends Model
{
    use HasFactory;
    protected $table='rol_permiso';
    protected $primaryKey = 'rol_permiso_id';
    public $timestamps=true;
    protected $fillable = [
        'permiso_id', 
        'rol_id', 
    ];
    protected $guarded = [
    ];
    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_id', 'permiso_id');
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }
}
