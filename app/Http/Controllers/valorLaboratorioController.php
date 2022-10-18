<?php

namespace App\Http\Controllers;

use App\Models\Valor_Laboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class valorLaboratorioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $nombreV = $request->get('Vnombre');  
            $detalleId = $request->get('VdetalleId');  
            $detalle = $request->get('detalle_id'); 
            /* ---------------------------------------------------- */ 
            $valorlaboratorio=Valor_Laboratorio::ValorLaboratoriodetalle($detalle)->get();
            foreach($valorlaboratorio as $laboratorio){  
                $laboratorio->delete();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Eliminar de valor de Laboratorio con nombre->'.$laboratorio->valor_nombre,'0','');
            }
             
            for ($i = 1; $i < count($nombreV); ++$i){
                $valorlaboratorio = new Valor_Laboratorio();
                $valorlaboratorio->valor_nombre = $nombreV[$i];
                $valorlaboratorio->valor_estado = '1';
                $valorlaboratorio->detalle_id = $detalle;
                $valorlaboratorio->save();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de valor Laboratorio con nombre-> '.$valorlaboratorio->valor_nombre,'0','');
            }  
            /*Inicio de registro de auditoria */
           
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('/examen/'.$request->get('examen_id').'/agregarValores')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/examen/'.$request->get('examen_id').'/agregarValores')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
    public function buscarBy($id){
        return Valor_Laboratorio::ValorLaboratorioexamen($id)->get();
    }
}
