<?php

namespace App\Console\Commands;

use App\Http\Controllers\generalController;
use App\Models\Amortizacion_Seguros;
use App\Models\Detalle_Amortizacion;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Prestamo;
use App\Models\Diario;
use App\Models\Prestamo_Banco;
use App\Models\Producto;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class amortizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amortizacion:actualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
           

            $detalles=DB::table('detalle_amortizacion')->join('amortizacion_seguros','amortizacion_seguros.amortizacion_id','=','detalle_amortizacion.amortizacion_id')->get();
            $fechaactual=date('Y-m-d');
            foreach ($detalles as $detalle) {
                if ($detalle->diario_id==null) {
                    if ($detalle->detalle_fecha<=$fechaactual) {
                        $detalleref=Detalle_Amortizacion::findOrFail($detalle->detalle_id);
                    
                        $seguroref=Amortizacion_Seguros::findOrFail($detalle->amortizacion_id);
                    
                        $fecha =$detalle->detalle_fecha;
                        $mes  = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                        $ano  = DateTime::createFromFormat('Y-m-d', $fecha)->format('y');
                        $secuencialDiario=DB::table('diario')->where('empresa_id', '=', $seguroref->sucursal->empresa_id)->where('diario_tipo', '=', 'CASE')->where('diario_mes', '=', $mes)->where('diario_ano', '=', DateTime::createFromFormat('Y-m-d', $fecha)->format('Y'))->max('diario_secuencial');
                        $sec = 1;
                        if ($secuencialDiario) {
                            $sec = $secuencialDiario +1;
                        }
                        $codigoDiario = 'CASE'.$mes.$ano.substr(str_repeat(0, 7).$sec, - 7);
                        $valor=$detalle->detalle_valor;

                        $general = new generalController();
                        $diario = new Diario();
                        $diario->diario_codigo = $codigoDiario;
                        $diario->diario_fecha = $fecha;
                        $diario->diario_referencia = 'COMPROBANTE DE AMORTIZACION DE SEGURO';
                        $diario->diario_tipo_documento = 'AMORTIZACION DE SEGURO';
                        $diario->diario_numero_documento = $mes.$ano.substr(str_repeat(0, 7).$sec, - 7);
                        $diario->diario_beneficiario = $seguroref->transaccionCompra->proveedor->proveedor_nombre;
                        
                        $diario->diario_tipo ='CASE';
                        $diario->diario_secuencial = substr($codigoDiario, 8);
                        $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                        $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                        $diario->diario_comentario = 'COMPROBANTE DE AMORTIZACION DE SEGURO CON FACTURA N°: '. $seguroref->transaccionCompra->transaccion_numero ;
                        $diario->diario_cierre = 0;
                        $diario->diario_estado = 1;
                        $diario->empresa_id = $seguroref->sucursal->empresa_id;
                        $diario->sucursal_id = $seguroref->sucursal_id;
                        $diario->save();
                        $detalleref->diario()->associate($diario);
                        $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo, 'Tipo de Diario -> '.$diario->diario_referencia.'');
                        foreach ($seguroref->transaccionCompra->detalles as $tcdetalle) {
                            $producto=Producto::findOrFail($tcdetalle->producto->producto_id);
                        }
                       

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $valor;
                        $detalleDiario->detalle_haber = 0.00 ;
                        $detalleDiario->detalle_comentario = 'P/R AMORTIZACION AL GASTO SEGURO '.$seguroref->transaccionCompra->proveedor->proveedor_nombre. ' '.$producto->producto_nombre;
                        $detalleDiario->detalle_tipo_documento = 'AMORTIZACION DE SEGURO';
                        $detalleDiario->detalle_numero_documento = $codigoDiario;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $seguroref->cuentadebe->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo, 'En la cuenta del debe -> '.$seguroref->cuentadebe->cuenta_numero.' con el valor de: -> '.$valor);
            
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $valor;
                        $detalleDiario->detalle_comentario = 'P/R AMORTIZACION AL GASTO SEGURO '.$seguroref->transaccionCompra->proveedor->proveedor_nombre. ' '.$producto->producto_nombre;
                        $detalleDiario->detalle_tipo_documento = 'AMORTIZACION DE SEGURO';
                        $detalleDiario->detalle_numero_documento = $codigoDiario;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id =  $producto->cuentaGasto->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo, 'En la cuenta del haber -> '.$producto->cuentaGasto->cuenta_numero.' con el valor de: -> '.$valor);
            
                        $detalleref->save();

                        $seguroref->amortizacion_pago_total=(DB::table('detalle_amortizacion')->where('diario_id', '!=', null)->where('amortizacion_id', '=', $seguroref->amortizacion_id)->sum('detalle_valor'));
                        $seguroref->save();
                    }
                       
                }
            }
            $amortizaciones=DB::table('amortizacion_seguros')->get();
            foreach ($amortizaciones as $amortizacion) {
                $amortizacionesb=DB::table('detalle_amortizacion')->join('amortizacion_seguros','amortizacion_seguros.amortizacion_id','=','detalle_amortizacion.amortizacion_id')->where('diario_id','=',NULL)->where('detalle_amortizacion.amortizacion_id','=',$amortizacion->amortizacion_id)->get();
                if($amortizacionesb==0){
                    $amortiza=Amortizacion_Seguros::findOrFail($amortizacion->amortizacion_id);
                    $amortiza->amortizacion_estado='2';
                    $amortiza->save();
                }
                else{
                    $amortiza=Amortizacion_Seguros::findOrFail($amortizacion->amortizacion_id);
                    $amortiza->amortizacion_estado='1';
                    $amortiza->save();
                }
            }
            
            

        
            
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        // El método line () es un método que viene con la clase de comando, que puede generar nuestra información personalizada
        $this->line('calculate Data Success!');
    }
}
