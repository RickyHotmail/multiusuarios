<?php

namespace App\Http\Controllers;

use App\Models\Valor_Referencial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class valorReferencialController extends Controller
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
            $Rcolumna1 = $request->get('Rcolumna1');  
            $Rcolumna2 = $request->get('Rcolumna2');   
            $detalle = $request->get('detalle_id'); 
            /* ---------------------------------------------------- */    
            $valorReferencialp=Valor_Referencial::ValorReferencialdetalle($detalle)->get();
            foreach($valorReferencialp as $referencial){
                $aux=$referencial;
                $referencial->delete();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Eliminar de valor referencial de Laboratorio con nombre->'.$referencial->valor_titulo,'0','');
            }
            for ($i = 1; $i < count($Rcolumna1); ++$i){
                $valorReferencial= new Valor_Referencial();
                $valorReferencial->valor_Columna1 = $Rcolumna1[$i];
                $valorReferencial->valor_Columna2 = $Rcolumna2[$i];
                $valorReferencial->valor_estado = '1';
                $valorReferencial->detalle_id = $detalle;
                $valorReferencial->save();
                $aux=$valorReferencial;
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de valor referencial de Laboratorio con valores->'.$Rcolumna1[$i]. ' '.$Rcolumna2[$i],'0','');
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
}
