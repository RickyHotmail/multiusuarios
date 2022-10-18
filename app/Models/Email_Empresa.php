<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Email_Empresa extends Model
{
    use HasFactory;
    protected $table='email_empresa';
    protected $primaryKey = 'email_id';
    public $timestamps=true;
    protected $fillable = [
        'email_servidor',
        'email_email',
        'email_usuario', 
        'email_pass',
        'email_puerto',
        'email_mensaje',
        'email_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeEmail($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('email_estado','=','1')->orderBy('email_email','asc');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
