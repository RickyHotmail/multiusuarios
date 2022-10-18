<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Medicamento extends Model
{
    use HasFactory;
    protected $table='medicamento';
    protected $primaryKey = 'medicamento_id';
    public $timestamps=true;
    protected $fillable = [        
       
        'medicamento_composicion',
        'medicamento_indicacion',
        'medicamento_contraindicacion',
        'medicamento_estado',
        'tipo_id', 
        'producto_id',        
    ];
    protected $guarded =[
    ];
    public function scopeMedicamentos($query){
        return $query->join('tipo_medicamento','tipo_medicamento.tipo_id','=','medicamento.tipo_id'
                    )->join('producto','producto.producto_id','=','medicamento.producto_id')
                    ->where('tipo_medicamento.empresa_id','=',Auth::user()->empresa_id
                    )->where('medicamento_estado','=','1')->orderBy('producto_nombre','asc');
    }
    public function scopeMedicamento($query, $id){
        return $query->join('tipo_medicamento','tipo_medicamento.tipo_id','=','medicamento.tipo_id'
                    )->where('tipo_medicamento.empresa_id','=',Auth::user()->empresa_id
                    )->where('medicamento_id','=',$id);
    }
    public function empresa() {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function tipoMedicamento() {
        return $this->belongsTo(Tipo_Medicamento::class, 'tipo_id', 'tipo_id');
    }
    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
