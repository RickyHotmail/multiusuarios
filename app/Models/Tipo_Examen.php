<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Examen extends Model
{
    use HasFactory;
    protected $table='tipo_examen';
    protected $primaryKey = 'tipo_id';
    public $timestamps = true;
    protected $fillable = [
        'tipo_nombre',
        'tipo_estado',
        'tipo_muestra_id',
        'tipo_recipiente_id',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeTipoExamenes($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1')->orderBy('tipo_nombre','asc');
    }
    public function scopeTipoExamen($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_id','=',$id);
    }   
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function tiporecipiente(){
        return $this->belongsTo(Tipo_Recipiente::class, 'tipo_recipiente_id', 'tipo_recipiente_id');
    }
    public function tipomuestra(){
        return $this->belongsTo(Tipo_Muestra::class, 'tipo_muestra_id', 'tipo_muestra_id');
    }
}
