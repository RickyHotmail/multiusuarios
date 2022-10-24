<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servidor_Correo extends Model
{
    use HasFactory;
    protected $table='servidor_correo';
    protected $primaryKey = 'servidor_id';
    public $timestamps = true;
    protected $fillable = [
        'servidor_host',
        'servidor_username',
        'servidor_password',
        'servidor_from',
        'servidor_embeddedImage',
        'servidor_port',
        'servidor_secure',
        'servidor_estado'
    ];

    protected $guarded =[
    ];  

    public function scopeServidorCorreo($query){
        return $query->where('servidor_estado','=',1);
    }
}
