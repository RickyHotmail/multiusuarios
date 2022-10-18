<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Diagnostico extends Model
{
    use HasFactory;
    protected $table ='detalle_diagnostico';
    protected $primaryKey = 'detalled_id';
    public $timestamps=true;
    protected $fillable = [        
        'detalled_estado',   
        'diagnostico_id',        
        'enfermedad_id',        
    ];
    protected $guarded =[
    ];  

    public function enfermedad()
    {
        return $this->belongsTo(Enfermedad::class, 'enfermedad_id', 'enfermedad_id');
    }
}
