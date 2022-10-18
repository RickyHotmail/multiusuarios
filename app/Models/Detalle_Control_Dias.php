<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Control_Dias extends Model
{
    use HasFactory;
    protected $table ='detalle_control_dias';
    protected $primaryKey = 'detalle_id';
    public $timestamps = true;
    protected $fillable = [       
        'control_dia1',
        'control_dia2',
        'control_dia3',
        'control_dia4',
        'control_dia5',
        'control_dia6',
        'control_dia7',
        'control_dia8',
        'control_dia9', 
        'control_dia10',
        'control_dia11',
        'control_dia12',
        'control_dia13',
        'control_dia14',
        'control_dia15',
        'control_dia16',
        'control_dia17', 
        'control_dia18',     
        'control_dia19',  
        'control_dia20',
        'control_dia21',
        'control_dia22',
        'control_dia23',
        'control_dia24',
        'control_dia25',
        'control_dia26',
        'control_dia27',
        'control_dia28',
        'control_dia29',
        'control_dia30',
        'control_dia31',
        'detalle_estado',
        'control_id'
    ];
    protected $guarded =[
    ];
    
}
