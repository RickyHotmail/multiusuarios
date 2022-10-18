<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque_Cliente extends Model
{
    use HasFactory;
    protected $table='cheque_cliente';
    protected $primaryKey = 'cheque_id';
    public $timestamps=true;
    protected $fillable = [
        'cheque_numero',
        'cheque_cuenta',       
        'cheque_valor',        
        'cheque_dueno',
        'cheque_estado',
        'banco_lista_id',
        'deposito_id',            
    ];
    protected $guarded =[
    ];
    public function deposito(){
        return $this->belongsTo(Deposito::class, 'deposito_id', 'deposito_id');
    }
}
