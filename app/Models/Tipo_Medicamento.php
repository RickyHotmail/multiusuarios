<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Medicamento extends Model
{
    use HasFactory;
    protected $table='tipo_medicamento';
    protected $primaryKey = 'tipo_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_nombre', 
        'tipo_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeTipoMedicamentos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1');
    }
    public function scopeTipoMedicamento($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_id','=',$id);
    }    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
