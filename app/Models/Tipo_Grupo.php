<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Grupo extends Model
{
    use HasFactory;
    protected $table='tipo_grupo';
    protected $primaryKey = 'tipo_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_nombre', 
        'tipo_icono',
        'tipo_orden',
        'tipo_estado',
        'grupo_id',
    ];
    protected $guarded =[
    ]; 
    public function permisos(){
        return $this->hasMany(Permiso::class, 'tipo_id', 'tipo_id');
    }
    
}
