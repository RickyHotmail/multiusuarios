<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Firma_Electronica extends Model
{
    use HasFactory;
    protected $table='firma_electronica';
    protected $primaryKey = 'firma_id';
    public $timestamps=true;
    protected $fillable = [
        'firma_ambiente',
        'firma_archivo',
        'firma_password',
        'firma_pubKey', 
        'firma_privKey',
        'firma_fecha',
        'firma_disponibilidad',
        'firma_estado',        
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeFirma($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('firma_estado','=','1')->orderBy('firma_id','asc');
    }
    public function Empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
