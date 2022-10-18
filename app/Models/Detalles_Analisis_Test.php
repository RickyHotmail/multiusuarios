<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalles_Analisis_Test extends Model
{
    use HasFactory;
    protected $table='test_exams';
    protected $primaryKey = 'id';
    public $timestamps=true;

    
    protected $fillable = [    
        'mensaje'
    ];

    protected $guarded =[
        
    ];
}
