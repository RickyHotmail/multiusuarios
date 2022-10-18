<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;
    protected $table='auditoria';
    protected $primaryKey = 'auditoria_id';
    public $timestamps=true;
    protected $fillable = [
        'auditoria_fecha',
        'auditoria_hora',
        'auditoria_maquina', 
        'auditoria_adicional', 
        'auditoria_descripcion',
        'auditoria_numero_documento',
        'auditoria_estado',
        'user_id',
        'auditoria_documento_respaldo'
    ];
    protected $guarded = [
    ];
}
