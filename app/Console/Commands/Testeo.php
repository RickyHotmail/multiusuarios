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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Testeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testeo:actualizar';

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
           
            $prestamos=DB::table('detalle_prestamo')->join('prestamo_banco','prestamo_banco.prestamo_id','=','detalle_prestamo.prestamo_id')->join('banco','banco.banco_id','=','prestamo_banco.banco_id')->join('banco_lista','banco.banco_lista_id','=','banco_lista.banco_lista_id')->get();
            $fechaactual=date('Y-m-d');
            foreach ($prestamos as $prestamo) {
                if ($prestamo->diario_id==null) {

                    if ($prestamo->detalle_fecha<=$fechaactual) {
                        $fecha =$prestamo->detalle_fecha;
                        $mes  = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                        $ano  = DateTime::createFromFormat('Y-m-d', $fecha)->format('y');
                        $secuencialDiario=DB::table('diario')->where('empresa_id', '=', $prestamo->empresa_id)->where('diario_tipo', '=', 'CIPB')->where('diario_mes', '=', $mes)->where('diario_ano', '=', DateTime::createFromFormat('Y-m-d', $fecha)->format('Y'))->max('diario_secuencial');
                        $sec = 1;
                        if ($secuencialDiario) {
                            $sec = $secuencialDiario +1;
                        }
                        $codigoDiario = 'CIPB'.$mes.$ano.substr(str_repeat(0, 7).$sec, - 7);
                        $valor=$prestamo->detalle_total;
                        $detalle=Detalle_Prestamo::findOrFail($prestamo->detalle_id);
                    
                        $prestamoref=Prestamo_Banco::findOrFail($prestamo->prestamo_id);

                        $general = new generalController();
                        $diario = new Diario();
                        $diario->diario_codigo = $codigoDiario;
                        $diario->diario_fecha = $fecha;
                        $diario->diario_referencia = 'COMPROBANTE DE INTERES DE PRESTAMO BANCARIO';
                        $diario->diario_tipo_documento = 'INTERES DE PRESTAMO BANCARIO';
                        $diario->diario_numero_documento = $mes.$ano.substr(str_repeat(0, 7).$sec, - 7);
                        $diario->diario_beneficiario = strtoupper($prestamo->banco_lista_nombre);
                        
                        $diario->diario_tipo ='CIPB';
                        $diario->diario_secuencial = substr($codigoDiario, 8);
                        $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                        $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                        $diario->diario_comentario = 'COMPROBANTE DE INTERES DE PRESTAMO BANCARIO: '.strtoupper($prestamo->banco_lista_nombre) ;
                        $diario->diario_cierre = 0;
                        $diario->diario_estado = 1;
                        $diario->empresa_id = $prestamo->empresa_id;
                        $diario->sucursal_id = $prestamo->sucursal_id;
                        $diario->save();
                        $detalle->diario()->associate($diario);
                        $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo, 'Tipo de Diario -> '.$diario->diario_referencia.'');
          
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $valor;
                        $detalleDiario->detalle_haber = 0.00 ;
                        $detalleDiario->detalle_comentario = 'P/R DEL PAGO DEL INTERES DEL '.strtoupper($prestamoref->banco->bancoLista->banco_lista_nombre). ' CON MONTO DE $'.$prestamoref->prestamo_monto;
                        $detalleDiario->detalle_tipo_documento = 'INTERES DE PRESTAMO BANCARIO';
                        $detalleDiario->detalle_numero_documento = $codigoDiario;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $prestamoref->cuentadebe->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo, 'En la cuenta del debe -> '.$prestamoref->cuentadebe->cuenta_numero.' con el valor de: -> '.$valor);
            
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $valor;
                        $detalleDiario->detalle_comentario = 'P/R EL RECONOCIMIENTO DE GASTOS DEL PRESTAMO DEL'.strtoupper($prestamoref->banco->bancoLista->banco_lista_nombre). ' CON MONTO DE $'.$prestamoref->prestamo_monto;
                        $detalleDiario->detalle_tipo_documento = 'INTERES DE PRESTAMO BANCARIO';
                        $detalleDiario->detalle_numero_documento = $codigoDiario;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $prestamoref->cuentahaber->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo, 'En la cuenta del haber -> '.$prestamoref->cuentahaber->cuenta_numero.' con el valor de: -> '.$valor);
            
                        $detalle->save();

                        $prestamoref->prestamo_total_interes=(DB::table('detalle_prestamo')->where('diario_id', '!=', null)->where('prestamo_id', '=', $prestamoref->prestamo_id)->sum('detalle_valor_interes'));
                        $prestamoref->prestamo_pago_total=$prestamoref->prestamo_total_interes+$prestamoref->prestamo_monto;
                        $prestamoref->save();
                    }
                }
            }
            $prestamos=DB::table('prestamo_banco')->get();
            foreach ($prestamos as $prestamo) {
                $prestamosb=DB::table('detalle_prestamo')->join('prestamo_banco','prestamo_banco.prestamo_id','=','detalle_prestamo.prestamo_id')->join('banco','banco.banco_id','=','prestamo_banco.banco_id')->join('banco_lista','banco.banco_lista_id','=','banco_lista.banco_lista_id')->where('diario_id','=',NULL)->where('prestamo_banco.prestamo_id','=',$prestamo->prestamo_id)->get();
                if(($prestamosb)==0){
                    $prestam=Prestamo_Banco::findOrFail($prestamo->prestamo_id);
                    $prestam->prestamo_estado='2';
                    $prestam->save();
                }
                else{
                    $prestam=Prestamo_Banco::findOrFail($prestamo->prestamo_id);
                    $prestam->prestamo_estado='1';
                    $prestam->save();
                }
            }
            
            

        
            
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        // El método line () es un método que viene con la clase de comando, que puede generar nuestra información personalizada
        $this->line('calculate Data Success!');
    }
}
