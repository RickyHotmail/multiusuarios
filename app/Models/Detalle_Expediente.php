<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Expediente extends Model
{
    use HasFactory;
    protected $table ='detalle_expediente';
    protected $primaryKey = 'detallee_id';
    public $timestamps=true;
    protected $fillable = [        
        'detallee_nombre',
        'detallee_tipo',
        'detallee_medida',        
        'detallee_url',        
        'detallee_multiple',        
        'detallee_valor',        
        'detallee_estado',        
        'expediente_id',        
    ];
    protected $guarded =[
    ];    
}
