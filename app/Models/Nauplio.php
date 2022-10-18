<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nauplio extends Model
{
    use HasFactory;
    protected $table='nauplio';
    protected $primaryKey = 'nauplio_id';
    public $timestamps = true;
    protected $fillable = [     
        'nauplio_nombre',   
        'nauplio_estado',
        'laboratorio_id',
    ];
    protected $guarded =[
    ];
    public function scopenauplios($query)
    {
        return $query->join('laboratorio_camaronera','laboratorio_camaronera.laboratorio_id','=','nauplio.laboratorio_id')->where('laboratorio_camaronera.empresa_id', '=', Auth::user()->empresa_id)->where('nauplio_estado', '=', '1');
    }
    public function scopenauplio($query,$id)
    {
        return $query->join('laboratorio_camaronera','laboratorio_camaronera.laboratorio_id','=','nauplio.laboratorio_id')->where('laboratorio_camaronera.empresa_id', '=', Auth::user()->empresa_id)->where('nauplio_id', '=', $id);
    }
    public function scopeLabortorio($query,$id)
    {
        return $query->join('laboratorio_camaronera','laboratorio_camaronera.laboratorio_id','=','nauplio.laboratorio_id')->where('laboratorio_camaronera.empresa_id', '=', Auth::user()->empresa_id)->where('nauplio.laboratorio_id', '=', $id);
    }
    public function laboratorio(){
        return $this->belongsTo(Laboratorio_Camaronera::class, 'laboratorio_id', 'laboratorio_id');
    }
}
