<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Rol extends Model
{
    use HasFactory;
    protected $table='detalle_rol';
    protected $primaryKey = 'detalle_rol_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_rol_fecha_inicio',
        'detalle_rol_fecha_fin',       
        'detalle_rol_sueldo', 
        'detalle_rol_porcentaje',        
        'detalle_rol_dias',  
        'detalle_rol_valor_dia',
        'detalle_rol_total_dias',
        'detalle_rol_transporte',
        'detalle_rol_horas_extras',
        'detalle_rol_valor_he',
        'detalle_rol_bonificacion_dias',
        'detalle_rol_horas_suplementarias',
        'detalle_rol_otra_bonificacion',
        'detalle_rol_otros_ingresos',
        'detalle_rol_sueldo_rembolsable',
        'detalle_rol_fondo_reserva',
        'detalle_rol_impuesto_renta',
        'detalle_rol_iess',
        'detalle_rol_multa',
        'detalle_rol_quincena',
        'detalle_rol_total_anticipo',
        'detalle_rol_total_comisariato',
        'detalle_rol_prestamo_quirografario',
        'detalle_rol_prestamo_hipotecario',
        'detalle_rol_prestamo',
        'detalle_rol_ext_salud',
        'detalle_rol_ley_sol',
        'detalle_rol_total_permiso',
        'detalle_rol_permiso_no_rem',
        'detalle_rol_otros_egresos',
        'detalle_rol_liquido_pagar',
        'detalle_rol_contabilizado',
        'detalle_rol_cosecha',
        'detalle_rol_iess_asumido',
        'detalle_rol_aporte_patronal',
        'detalle_rol_aporte_iecesecap',
        'detalle_rol_vacaciones',
        'detalle_rol_vacaciones_anticipadas',
        'detalle_rol_decimo_tercero',
        'detalle_rol_decimo_cuarto',
        'detalle_rol_decimo_terceroacum',
        'detalle_rol_decimo_cuartoacum',
        'detalle_rol_total_ingreso',
        'detalle_rol_total_egreso',
        'detalle_rol_estado',
        'cabecera_rol_id',
    ];
    protected $guarded =[
    ];
    public function scopeDetalleRol($query, $id){
        return $query->join('cabecera_rol','cabecera_rol.cabecera_rol_id','=','detalle_rol.cabecera_rol_id')->where('cabecera_rol.cabecera_rol_id','=', $id);
    }
    public function rol(){
        return $this->belongsTo(Rol_Consolidado::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
}
