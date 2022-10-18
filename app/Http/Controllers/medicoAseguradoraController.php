<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Empleado;
use App\Models\Cliente;
use App\Models\Medico;
use App\Models\Medico_Aseguradora;
use App\Models\Punto_Emision;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class medicoAseguradoraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/denegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('/denegado');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect('/denegado');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('/denegado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/denegado');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect('/denegado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect('/denegado');
    }

    public function aseguradoras($id)
    {
        try{
            $gruposPermiso = DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $permisosAdmin = DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            $clientesAseguradoras = Cliente::ClienteAseguradora()->get();
            $medico = Medico::Medico($id)->first();
            $empleados = Empleado::Empleados()->get();
            $proveedores = Proveedor::Proveedores()->get();
            $nombreMedico = '';
            if ($medico->empleado_id != NULL) {
                foreach ($empleados as $empleado) {
                    if ($medico->empleado_id == $empleado->empleado_id) {
                        $nombreMedico = $empleado->empleado_nombre;
                    }
                }
            }elseif($medico->proveedor_id != NULL){
                foreach ($proveedores as $proveedor) {
                    if ($medico->proveedor_id == $proveedor->proveedor_id) {
                        $nombreMedico = $proveedor->proveedor_nombre;
                    }
                }
            }
            if ($medico) {
                return view('admin.agendamientoCitas.medicoAseguradora.aseguradoras', ['medico' => $medico, 'nombreMedico' => $nombreMedico, 'PE' => Punto_Emision::puntos()->get(), 'clientesAseguradoras' => $clientesAseguradoras, 'gruposPermiso' => $gruposPermiso, 'permisosAdmin' => $permisosAdmin]);
            } else {
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function guardarAseguradoras(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $AseguradorasAsignadas = '';
            $medico_aseguradora = Medico_Aseguradora::where('medico_id', '=', $id)->delete();
            $clientes = DB::table('cliente')->where('cliente_estado', '=', '1')->orderBy('cliente_nombre', 'asc')->get();
            foreach ($clientes as $cliente) {
                if ($request->get($cliente->cliente_id) == "on") {
                    $medico_aseguradora = new Medico_Aseguradora;
                    $medico_aseguradora->aseguradoraM_estado = 1;
                    $medico_aseguradora->cliente_id = $cliente->cliente_id;
                    $medico_aseguradora->medico_id = $id;
                    $medico_aseguradora->save();
                    $AseguradorasAsignadas = $AseguradorasAsignadas . ' - ' . $cliente->cliente_nombre;
                }
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de aseguradoras de medicos con id -> ' . $id, '0', 'Las aseguradoras asignadas  fueron -> ' . $AseguradorasAsignadas);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('medico')->with('success', 'Datos guardados exitosamente');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect('medico')->with('error', 'Oucrrio un error en el procedimiento. Vuelva a intentar.');
        }
    }
}
