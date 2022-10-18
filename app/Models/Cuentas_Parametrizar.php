<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuentas_Parametrizar extends Model
{
    use HasFactory;
    protected $table='cuentas_parametrizar';
    protected $primaryKey = 'parametrizar_id';
    public $timestamps=true;
    protected $fillable = [
        'parametrizar_nombre',
        'parametrizar_orden',
        'parametrizar_estado',                   
    ];
    protected $guarded =[
    ];
    public function scopeCuentas($query){
        return $query->where('parametrizar_estado','=','1')->orderBy('parametrizar_id','asc');
    }
}
