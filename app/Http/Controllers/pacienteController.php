<?php

namespace App\Http\Controllers;

use App\Models\Tipo_Identificacion;
use App\Models\Entidad;
use App\Models\Provincia;
use App\Models\Pais;
use App\Models\Paciente;
use App\Models\Entidad_Aseguradora;
use App\Models\Ciudad;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Orden_Atencion;
use App\Models\Tipo_Dependencia;
use DateTime;
use Facade\FlareClient\Http\Client;
use Illuminate\Http\Request;


class pacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $clientesAseguradoras = Cliente::ClienteAseguradora()->get();
            $paises = Pais::Paises()->get();       
            $tiposIdentificacion = Tipo_Identificacion::TipoIdentificaciones()->get();
            $pacientes = Paciente::Pacientes()->get();
            return view('admin.agendamientoCitas.paciente.index',['pacientes'=>$pacientes,'tiposDependencias'=>Tipo_Dependencia::TiposDependencias()->get(),'clientesAseguradoras'=>$clientesAseguradoras, 'paises'=>$paises,'tiposIdentificacion'=>$tiposIdentificacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $clientesAseguradoras = Cliente::ClienteAseguradora()->get();
            $paises = Pais::Paises()->get();       
            $tiposIdentificacion = Tipo_Identificacion::TipoIdentificaciones()->get();
            $pacientes = Paciente::Pacientes()->get();        
            return view('admin.agendamientoCitas.paciente.create',['pacientes'=>$pacientes,'tiposDependencias'=>Tipo_Dependencia::TiposDependencias()->get(),'clientesAseguradoras'=>$clientesAseguradoras, 'paises'=>$paises,'tiposIdentificacion'=>$tiposIdentificacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{         
            DB::beginTransaction();   
            $paciente = new Paciente();
            $paciente->paciente_cedula = $request->get('idNumero');
            $paciente->paciente_apellidos = $request->get('idApellidos');
            $paciente->paciente_nombres = $request->get('idNombres');
            $paciente->paciente_direccion = $request->get('idDireccion');
            $paciente->paciente_fecha_nacimiento = $request->get('idFechaNac');
            $paciente->paciente_nacionalidad = $request->get('idNacionalidad');
            $paciente->paciente_celular = $request->get('idCelular');
            $paciente->paciente_email = $request->get('idEmail');
            $paciente->paciente_sexo = $request->get('idSexo');
            $paciente->tipod_id = $request->get('idTipoDependencia');
            $paciente->paciente_cedula_afiliado = $request->get('idCiAfiliado');
            $paciente->paciente_nombre_afiliado = $request->get('idNombreAfiliado');

            //guardar foto cedula paciente
            if($request->file('fotoPaciente')!=null){
                $pacienteDir=$this->crearDocumento($paciente, $request->file('fotoPaciente'), 'paciente');
                $paciente->documento_paciente=$pacienteDir;
            }


            if($request->get('id_dependiente')== "on"){
                $paciente->paciente_dependiente = 1;

                //guardar cedula afiliado
                if($request->file('fotoAfiliado')!=null){
                    $afiliadoDir=$this->crearDocumento($paciente, $request->file('fotoAfiliado'), 'afiliado');
                    $paciente->documento_afiliado=$afiliadoDir;
                }
            }else{
                $paciente->paciente_dependiente = 0;
                $paciente->documento_afiliado="";
            }
            $paciente->paciente_estado = 1;            
            $paciente->ciudad_id = $request->get('idCiudad');
            $paciente->cliente_id = $request->get('idAseguradora');
            $paciente->entidad_id = $request->get('idEntidad');
            $paciente->tipo_identificacion_id = $request->get('idTipoIdentificacion');
            $paciente->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de paciente -> '.$request->get('idApellidos').' '.$request->get('idNombres').' con Cedula -> '.$request->get('idNumero'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('paciente')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('paciente')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $paciente=Paciente::Paciente($id)->first();
            if($paciente){
                return view('admin.agendamientoCitas.paciente.ver',['paciente'=>$paciente, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $clientesAseguradoras = Cliente::ClienteAseguradora()->get();
            $paises = Pais::Paises()->get();
            $tiposIdentificacion = Tipo_Identificacion::TipoIdentificaciones()->get();
            $paciente=Paciente::Paciente($id)->first();
            if($paciente){
                return view('admin.agendamientoCitas.paciente.editar', ['paciente'=>$paciente, 'tiposDependencias'=>Tipo_Dependencia::TiposDependencias()->get(),'clientesAseguradoras'=>$clientesAseguradoras,'tiposIdentificacion'=>$tiposIdentificacion,'paises'=>$paises, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
        try{
            DB::beginTransaction();
            $paciente = Paciente::findOrFail($id);
            $paciente->paciente_cedula = $request->get('idNumero');
            $paciente->paciente_apellidos = $request->get('idApellidos');
            $paciente->paciente_nombres = $request->get('idNombres');
            $paciente->paciente_direccion = $request->get('idDireccion');
            $paciente->paciente_fecha_nacimiento = $request->get('idFechaNac');
            $paciente->paciente_nacionalidad = $request->get('idNacionalidad');
            $paciente->paciente_celular = $request->get('idCelular');
            $paciente->paciente_email = $request->get('idEmail');
            $paciente->paciente_sexo = $request->get('idSexo');
            $paciente->tipod_id = $request->get('idTipoDependencia');

            //guardar foto cedula paciente
            if($request->file('fotoPaciente')!=null){
                $pacienteDir=$this->crearDocumento($paciente, $request->file('fotoPaciente'), 'paciente');
                $paciente->documento_paciente=$pacienteDir;
            }

            if($request->get('id_dependiente')== "on"){
                $paciente->paciente_dependiente = 1;
                $paciente->paciente_cedula_afiliado = $request->get('idCiAfiliado');
                $paciente->paciente_nombre_afiliado = $request->get('idNombreAfiliado');

                //guardar cedula afiliado
                if($request->file('fotoAfiliado')!=null){
                    $afiliadoDir=$this->crearDocumento($paciente, $request->file('fotoAfiliado'), 'afiliado');
                    $paciente->documento_afiliado=$afiliadoDir;
                }
            }else{
                $paciente->paciente_cedula_afiliado = null;
                $paciente->paciente_nombre_afiliado = null;
                $paciente->paciente_dependiente = 0;
                $paciente->documento_afiliado="";
            }

            if ($request->get('idEstado') == "on"){
                $paciente->paciente_estado = 1;
            }else{
                $paciente->paciente_estado = 0;
            }      

            $paciente->ciudad_id = $request->get('idCiudad');
            $paciente->cliente_id = $request->get('idAseguradora');
            $paciente->entidad_id = $request->get('idEntidad');
            $paciente->tipo_identificacion_id = $request->get('idTipoIdentificacion');
            $paciente->save();

            //
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de paciente -> '.$request->get('idApellidos').' '.$request->get('idNombres').' con Cedula -> '.$request->get('idNumero'),'0','con el tipo de Identificacion ->'.$request->get('idTipoIdentificacion'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('paciente')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('paciente')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function subirDocumento(Request $request){
        try{
            $paciente=Paciente::findOrFail($request->paciente_id);

            if($paciente){
                if($request->file('documento')!=null){
                    DB::beginTransaction();
                    $dir=$this->crearDocumento($paciente, $request->file('documento'), $request->tipo);

                    if($request->tipo=="paciente")
                        $paciente->documento_paciente=$dir;
                    else
                        $paciente->documento_afiliado=$dir;

                    $paciente->update();

                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion de paciente -> '.$request->get('idApellidos').' '.$request->get('idNombres').' con Cedula -> '.$request->get('idNumero').", subió una imagen",'0','con el tipo de Identificacion ->'.$request->get('idTipoIdentificacion'));
                    DB::commit();
                    return json_encode(array("result"=>"OK", "documento"=>$dir));
                }
                else
                    return json_encode(array("result"=>"FAIL","mensaje"=> "nada que subir"));
            }
            else
                return json_encode(array("result"=>"FAIL","mensaje"=> "paciente no encontrado"));
        }
        catch(\Exception $e){
            DB::rollBack();
            return json_encode(array("result"=>"FAIL","mensaje"=> $e->getMessage()));
        }
    }

    private function crearDocumento($paciente, $imagen, $tipo){
        $imagenes=[];
        $empresa = Empresa::empresa()->first();

        $ruta = 'DocumentosPacientes/'.$empresa->empresa_ruc.'/'.$paciente->paciente_id;
        $extension = $imagen->extension();

        if ($imagen) {
            if (!is_dir(public_path().'/'.$ruta)) mkdir(public_path().'/'.$ruta, 0777, true);

            $name = 'documento_'.$tipo.'.'.$extension;
            $path = $imagen->move(public_path().'/'.$ruta, $name);
        
            return $ruta.'/'.$name;
        }
        else
            return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function buscarByEntidad($buscar){
        return Entidad_Aseguradora::AseguradorasEntidades($buscar)->get();
    }

    public function buscarByPais($buscar){
        return Provincia::PaisProvincias($buscar)->get();
    }

    public function buscarByProvincia($buscar){
        return Ciudad::ProvinciaCiudades($buscar)->get();
    }

    public function buscarByNombrePaciente($buscar){
        return Paciente::PacientesByNombre($buscar)->get();
    }

    public function buscarByidPaciente($buscar){
        return Paciente::EspecialidadesPaciente($buscar)->get();
    }
}
