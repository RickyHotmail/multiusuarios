<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Parametrizacion_Impresion extends Model
{
    use HasFactory;
    protected $table='parametrizacion_impresion_factura';
    protected $primaryKey = 'parametrizacioni_id';
    public $timestamps=true;
    protected $fillable = [
        'user_id',
        'parametrizacioni_tipo',    
        'parametrizacioni_estado'
    ];

    protected $guarded =[
    ];

    public function scopeParametrizacionImpresion($query){
        return $query->where('user_id','=',Auth::user()->user_id)
                     ->where('parametrizacioni_estado','=','1');
    }
}
