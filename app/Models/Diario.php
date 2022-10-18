<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class  Diario extends Model
{
    use HasFactory;
    protected $table='diario';
    protected $primaryKey = 'diario_id';
    public $timestamps=true;
    protected $fillable = [
        'diario_codigo',
        'diario_fecha',       
        'diario_referencia',        
        'diario_tipo_documento',
        'diario_numero_documento',
        'diario_beneficiario', 
        'diario_tipo',
        'diario_secuencial',
        'diario_mes',
        'diario_ano',
        'diario_comentario',
        'diario_cierre',
        'diario_estado',
        'empresa_id',
        'sucursal_id',
        'diario_cierre_id',
        'diario_financiero_id'
    ];
    protected $guarded =[
    ];
    public function detalles(){
        return $this->hasMany(Detalle_Diario::class, 'diario_id', 'diario_id');
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function diariocierre()
    {
        return $this->belongsTo(Diario::class, 'diario_cierre_id', 'diario_id');
    }
    public function diariodinanciero()
    {
        return $this->belongsTo(Diario::class, 'diario_financiero_id', 'diario_id');
    }
    public function movimientocaja()
    {
        return $this->belongsTo(Movimiento_Caja::class, 'diario_id', 'diario_id');
    }
    public function anticipo()
    {
        return $this->belongsTo(Anticipo_Cliente::class, 'diario_id', 'diario_id');
    }
    public function anticipoproveedor()
    {
        return $this->belongsTo(Anticipo_Proveedor::class, 'diario_id', 'diario_id');
    }
    public function pagocuentaCobrar(){
        return $this->belongsTo(Pago_CXC::class, 'diario_id', 'diario_id');
    }
    public function pagocuentapagar(){
        return $this->belongsTo(Pago_CXP::class, 'diario_id', 'diario_id');
    }
    public function notadebito(){
        return $this->belongsTo(Nota_Debito::class, 'diario_id', 'diario_id');
    }
    public function notacrediito(){
        return $this->belongsTo(Nota_Credito::class, 'diario_id', 'diario_id');
    }
    public function egresoCaja(){
        return $this->belongsTo(Egreso_Caja::class, 'diario_id', 'diario_id');
    }
    public function scopeDiarioSecuencial($query, $tipo, $mes, $ano){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_tipo','=',$tipo)->where('diario_mes','=',$mes)->where('diario_ano','=',$ano);
    }
    public function scopeDiarioDepreciacion($query, $tipo, $mes, $ano, $sucursal){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_tipo','=',$tipo)->where('diario_mes','=',$mes)->where('diario_ano','=',$ano)->where('sucursal_id','=',$sucursal);
    }
    public function scopeDiarioTipoDocumento($query, $tipo){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_tipo','=',$tipo);
    }
    public function scopeDiarioCodigo($query, $codigo){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_codigo','=',$codigo);
    }
    public function scopeDiarioXID($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_id','=',$id);
    }
    public function scopeDiarioSucursal($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeDiarioSucursalCierre($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('diario_tipo','=','CDCR')->orderBy('sucursal_nombre','asc');
    }
    public function scopeDiarioTransferenciaDistinc($query){
        return  $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeDiarioChequeDistinc($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('cheque_fecha_emision','desc');

    }
   
    public function scopeDiarioDepositoDistinc($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('deposito_fecha','desc');

    }
    public function scopeDiarioNotaDebitoDistinc($query){
        return  $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('nota_fecha','desc');
    }
    public function scopeDiarioNotaCreditoDistinc($query){
        return  $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('nota_fecha','desc');
    }

    public function scopeDiarioChequeBancoSucursal($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('cheque_fecha_emision','desc');

    }
   
    public function scopeDiarioDepositoBancoSucursal($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('deposito_fecha','desc');

    }
    public function scopeDiarioNotaDebitoBancoSucursal($query){
        return  $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('nota_fecha','desc');
    }
    public function scopeDiarioNotaCreditoBancoSucursal($query){
        return  $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('nota_fecha','desc');
    }
    public function scopeDiarioTransferenciaBancoSucursal($query){
        return  $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('transferencia_fecha','desc');
    }
    /////////////////////Todos
    public function scopeDiarioChequeBancoSucursalbuscar($query,$fechadesde,$fechahasta,$sucursal,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('cuenta_bancaria_numero','=',$cuenta)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('cheque_fecha_emision','desc');

    }
    public function scopeDiarioNotaDebitoBancoSucursalbuscar($query,$fechadesde,$fechahasta,$sucursal,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('cuenta_bancaria_numero','=',$cuenta)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioNotaCreditoBancoSucursalbuscar($query,$fechadesde,$fechahasta,$sucursal,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('cuenta_bancaria_numero','=',$cuenta)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioDepositoBancoSucursalbuscar($query,$fechadesde,$fechahasta,$sucursal,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('cuenta_bancaria_numero','=',$cuenta)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('transferencia_fecha','desc');

    }
    public function scopeDiarioTransferenciaBancoSucursalbuscar($query,$fechadesde,$fechahasta,$sucursal,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('cuenta_bancaria_numero','=',$cuenta)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('transferencia_fecha','desc');

    }
        /////////////////////Sucursal
    public function scopeDiarioChequeSucursal($query,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)->orderBy('cheque_fecha_emision','desc');
    }
    public function scopeDiarioNotaDebitoSucursa($query,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioNotaCreditoSucursal($query,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioDepositoSucursal($query,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)->orderBy('deposito_fecha','desc');

    }
    public function scopeDiarioTransferenciaSucursal($query,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)->orderBy('transferencia_fecha','desc');

    }
    /////////////////////Fecha
    public function scopeDiarioChequeFecha($query, $fechadesde, $fechahasta)
    {
        return $query->join('sucursal', 'sucursal.sucursal_id', '=', 'diario.sucursal_id')
        ->join('detalle_diario', 'detalle_diario.diario_id', '=', 'diario.diario_id')
        ->join('cheque', 'cheque.cheque_id', '=', 'detalle_diario.cheque_id')
        ->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id', '=', 'cheque.cuenta_bancaria_id')
        ->join('banco', 'banco.banco_id', '=', 'cuenta_bancaria.banco_id')
        ->join('banco_lista', 'banco_lista.banco_lista_id', '=', 'banco.banco_lista_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('diario_fecha', '>=', $fechadesde)
        ->where('diario_fecha', '<=', $fechahasta)->orderBy('cheque_fecha_emision','desc');
    }
    public function scopeDiarioTransferenciaFecha($query, $fechadesde, $fechahasta)
    {
        return $query->join('sucursal', 'sucursal.sucursal_id', '=', 'diario.sucursal_id')
        ->join('detalle_diario', 'detalle_diario.diario_id', '=', 'diario.diario_id')
        ->join('transferencia', 'transferencia.transferencia_id', '=', 'detalle_diario.transferencia_id')
        ->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id', '=', 'transferencia.cuenta_bancaria_id')
        ->join('banco', 'banco.banco_id', '=', 'cuenta_bancaria.banco_id')
        ->join('banco_lista', 'banco_lista.banco_lista_id', '=', 'banco.banco_lista_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('diario_fecha', '>=', $fechadesde)
        ->where('diario_fecha', '<=', $fechahasta)->orderBy('transferencia_fecha','desc');
    }
    public function scopeDiarioDepositoFecha($query, $fechadesde, $fechahasta)
    {
        return $query->join('sucursal', 'sucursal.sucursal_id', '=', 'diario.sucursal_id')
        ->join('detalle_diario', 'detalle_diario.diario_id', '=', 'diario.diario_id')
        ->join('deposito', 'deposito.deposito_id', '=', 'detalle_diario.deposito_id')
        ->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id', '=', 'deposito.cuenta_bancaria_id')
        ->join('banco', 'banco.banco_id', '=', 'cuenta_bancaria.banco_id')
        ->join('banco_lista', 'banco_lista.banco_lista_id', '=', 'banco.banco_lista_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('diario_fecha', '>=', $fechadesde)
        ->where('diario_fecha', '<=', $fechahasta)->orderBy('deposito_fecha','desc');
    }
    public function scopeDiarioNotaDebitoBancoFecha($query, $fechadesde, $fechahasta)
    {
        return $query->join('sucursal', 'sucursal.sucursal_id', '=', 'diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco', 'banco.banco_id', '=', 'cuenta_bancaria.banco_id')
        ->join('banco_lista', 'banco_lista.banco_lista_id', '=', 'banco.banco_lista_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('diario_fecha', '>=', $fechadesde)
        ->where('diario_fecha', '<=', $fechahasta)->orderBy('nota_fecha','desc');
    }
    public function scopeDiarioNotaCreditoBancoFecha($query, $fechadesde, $fechahasta)
    {
        return $query->join('sucursal', 'sucursal.sucursal_id', '=', 'diario.sucursal_id')
       ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco', 'banco.banco_id', '=', 'cuenta_bancaria.banco_id')
        ->join('banco_lista', 'banco_lista.banco_lista_id', '=', 'banco.banco_lista_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('diario_fecha', '>=', $fechadesde)
        ->where('diario_fecha', '<=', $fechahasta)->orderBy('nota_fecha','desc');
    }
          /////////////////////Banco
    public function scopeDiarioChequeBanco($query,$banco){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)->orderBy('cheque_fecha_emision','desc');

    }
    public function scopeDiarioNotaDebitoBanco($query,$banco){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioNotaCreditoBanco($query,$banco){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioDepositoBanco($query,$banco){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)->orderBy('deposito_fecha','desc');

    }
    public function scopeDiarioTransferenciaBanco($query,$banco){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)->orderBy('transferencia_fecha','desc');
    }
          /////////////////////Banco-Cuenta
    public function scopeDiarioChequeBancoCuenta($query,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('cheque_fecha_emision','desc');

    }
    public function scopeDiarioNotaDebitoBancoCuenta($query,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioNotaCreditoBancoCuenta($query,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioDepositoBancoCuenta($query,$banco,$cuenta){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id','=',$banco)
        ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('deposito_fecha','desc');

    }
    public function scopeDiarioTransferenciaCuenta($query,$banco,$cuenta){
        return $query->join('sucursal', 'sucursal.sucursal_id', '=', 'diario.sucursal_id')
        ->join('detalle_diario', 'detalle_diario.diario_id', '=', 'diario.diario_id')
        ->join('transferencia', 'transferencia.transferencia_id', '=', 'detalle_diario.transferencia_id')
        ->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id', '=', 'transferencia.cuenta_bancaria_id')
        ->join('banco', 'banco.banco_id', '=', 'cuenta_bancaria.banco_id')
        ->join('banco_lista', 'banco_lista.banco_lista_id', '=', 'banco.banco_lista_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('banco_lista.banco_lista_id', '=', $banco)
        ->where('cuenta_bancaria_numero', '=', $cuenta)->orderBy('transferencia_fecha','desc');
    }

      /////////////////////FechaSucursal
      public function scopeDiarioChequeFechaSucursal($query,$fechadesde,$fechahasta,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('cheque_fecha_emision','desc');

    }
    public function scopeDiarioNotaDebitoFechaSucursal($query,$fechadesde,$fechahasta,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioNotaCreditoFechaSucursal($query,$fechadesde,$fechahasta,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

    }
    public function scopeDiarioDepositoFechaSucursal($query,$fechadesde,$fechahasta,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('deposito_fecha','desc');

    }
    public function scopeDiarioTransferenciaFechaSucursal($query,$fechadesde,$fechahasta,$sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
        ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
        ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('diario_fecha','>=',$fechadesde)
        ->where('diario_fecha','<=',$fechahasta)->orderBy('transferencia_fecha','desc');

    }
///////////////FechaBanco
///////////////FechaBanco
public function scopeDiarioChequeFechaBanco($query,$fechadesde,$fechahasta,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('cheque_fecha_emision','desc');

}
public function scopeDiarioNotaDebitoFechaBanco($query,$fechadesde,$fechahasta,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioNotaCreditoFechaBanco($query,$fechadesde,$fechahasta,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioDepositoFechaBanco($query,$fechadesde,$fechahasta,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('deposito_fecha','desc');

}
public function scopeDiarioTransferenciaFechaBanco($query,$fechadesde,$fechahasta,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('transferencia_fecha','desc');

}
///FECHA-BANCO-CUENTA
public function scopeDiarioChequeFechaBancoCuenta($query,$fechadesde,$fechahasta,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('cuenta_bancaria_numero','=',$cuenta)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('cheque_fecha_emision','desc');

}
public function scopeDiarioNotaDebitoFechaBancoCuenta($query,$fechadesde,$fechahasta,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('cuenta_bancaria_numero','=',$cuenta)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioNotaCreditoFechaBancoCuenta($query,$fechadesde,$fechahasta,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('cuenta_bancaria_numero','=',$cuenta)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioDepositoFechaBancoCuenta($query,$fechadesde,$fechahasta,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('cuenta_bancaria_numero','=',$cuenta)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('deposito_fecha','desc');

}
public function scopeDiarioTransferenciaFechaBancoCuenta($query,$fechadesde,$fechahasta,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('cuenta_bancaria_numero','=',$cuenta)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('transferencia_fecha','desc');

}

/////////////////////Sucursal-Banco
public function scopeDiarioChequeSucursalBanco($query,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)->orderBy('cheque_fecha_emision','desc');

}
public function scopeDiarioNotaDebitoSucursalBanco($query,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)->orderBy('nota_fecha','desc');

}
public function scopeDiarioNotaCreditoSucursalBanco($query,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)->orderBy('nota_fecha','desc');

}
public function scopeDiarioDepositoSucursalBanco($query,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)->orderBy('deposito_fecha','desc');

}
public function scopeDiarioTransferenciaSucursalBanco($query,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)->orderBy('transferencia_fecha','desc');

}

/////////////////////Sucursal-Banco-Cuenta
public function scopeDiarioChequeSucursalBancoCuenta($query,$sucursal,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('cuenta_bancaria_numero','=',$cuenta)
    ->where('sucursal_nombre','=',$sucursal)->orderBy('cheque_fecha_emision','desc');

}
public function scopeDiarioNotaDebitoSucursalBancoCuenta($query,$sucursal,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioNotaCreditoSucursalBancoCuenta($query,$sucursal,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioDepositoSucursalBancoCuenta($query,$sucursal,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('deposito_fecha','desc');

}
public function scopeDiarioTransferenciaSucursalBancoCuenta($query,$sucursal,$banco,$cuenta){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('cuenta_bancaria_numero','=',$cuenta)->orderBy('transferencia_fecha','desc');

}
//////FechaSucursalBanco
public function scopeDiarioChequeFechaSucursalBanco($query,$fechadesde,$fechahasta,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('cheque','cheque.cheque_id','=','detalle_diario.cheque_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('cheque_fecha_emision','desc');

}
public function scopeDiarioNotaDebitoFechaSucursalBanco($query,$fechadesde,$fechahasta,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_debito_banco','nota_debito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_debito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioNotaCreditoFechaSucursalBanco($query,$fechadesde,$fechahasta,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('nota_credito_banco','nota_credito_banco.diario_id','=','diario.diario_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','nota_credito_banco.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('nota_fecha','desc');

}
public function scopeDiarioDepositoFechaSucursalBanco($query,$fechadesde,$fechahasta,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('deposito','deposito.deposito_id','=','detalle_diario.deposito_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','deposito.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('deposito_fecha','desc');

}
public function scopeDiarioTransferenciaFechaSucursalBanco($query,$fechadesde,$fechahasta,$sucursal,$banco){
    return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')
    ->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')
    ->join('transferencia','transferencia.transferencia_id','=','detalle_diario.transferencia_id')
    ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
    ->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')
    ->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id')
    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
    ->where('banco_lista.banco_lista_id','=',$banco)
    ->where('sucursal_nombre','=',$sucursal)
    ->where('diario_fecha','>=',$fechadesde)
    ->where('diario_fecha','<=',$fechahasta)->orderBy('transferencia_fecha','desc');

}

    public function scopereporteDiario($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeDiarioTipo($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->orderBy('diario_tipo_documento','asc');
    }
    public function scopeDiarioByBuscar($query, $fechaI, $fechaF,$buscar,$sucursal){
        return $query->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario_fecha','>=',$fechaI)
        ->where('diario_fecha','<=',$fechaF)->where('diario.sucursal_id','=',$sucursal)
        ->where(function($query) use($buscar){
            $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                  ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                  ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                  ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                  ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                  ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                  ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
        });
    }
    public function scopeDiariosCierre($query,$fechaI,$fechaF,$sucursal){
        return $query->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario_fecha','>=',$fechaI)->where('diario_fecha','<=',$fechaF)->where('sucursal_id','=',$sucursal);
    }
    public function scopeDiario($query, $id){
        return $query->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_id','=',$id)->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id');
    }
    public function scopeDiariosDescuadrados($query, $sucursal,$fechaI,$fechaF){
        $query->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario_fecha','>=',$fechaI)->where('diario_fecha','<=',$fechaF);
        if($sucursal  > 0){
            $query->where('sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeDiariosCierres($query, $sucursal,$fechaI,$fechaF){
        $query->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario_tipo','=','CDCR')->where('diario_fecha','>=',$fechaI)->where('diario_fecha','<=',$fechaF);
        if($sucursal > 0){
            $query->where('sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeDiariosuma($query, $id){
        return $query->select(DB::raw('SUM(detalle_haber) as total_diario'))->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_id','=',$id)->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id');
    }
    public function scopeDiarios($query){
        return $query->where('diario.empresa_id','=',Auth::user()->empresa_id)->join('sucursal','sucursal.sucursal_id','=','diario.sucursal_id');
    }
}
