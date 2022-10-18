<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Marca_Producto extends Model
{
    use HasFactory;
    protected $table='marca_producto';
    protected $primaryKey = 'marca_id';
    public $timestamps = true;
    protected $fillable = [
        'marca_nombre',
        'empresa_id',
        'marca_estado',
    ];
    protected $guarded =[
    ];
    public function scopeMarcaByName($query, $nombre){
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('marca_nombre', '=', $nombre);
    }
    public function scopeMarcas($query)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('marca_estado', '=', '1')->orderBy('marca_nombre', 'asc');
    }
    public function scopeMarca($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('marca_id', '=', $id);
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}