<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametrizar_Empresa extends Model
{
    use HasFactory;
    protected $table='parametrizar_empresa';
    protected $primaryKey='parametrizar_id';
    public $timestamps=true;
    protected $fillable=[
        'parametrizar_nombre',
        'parametrizar_valor',
        'parametrizar_estado'
    ];

    protected $guarded=[
    ];

    public function scopeBuscarConfiguracion($query, $parametro){
        return $query->where('parametrizar_nombre', '=',$parametro
            )->where('parametrizar_estado','=',1);
    }
}
