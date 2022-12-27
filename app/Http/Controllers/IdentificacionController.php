<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IdentificacionController extends Controller
{
    public function comprobarIdentificacion($ced){
        if(substr($ced,2,1)=='9')  $result=$this->comprobarRucJuridico($ced);
        else if(substr($ced,2,1)=='6')  $result=$this->comprobarRucPublico($ced);
        else  $result=$this->comprobarCedula($ced);

        return response()->json(['result'=>$result], 200);
    }

    private function comprobarCedula($ced){
        try{
            if(!isset($ced)) return false;
            if(strlen($ced)!=10 && strlen($ced)!=13) return false;
            if(!is_numeric($ced)) return false;

            $suma=0;
            for($i=0; $i<9;$i++){
                $parte=0;
                if($i%2==0) $parte+=(substr($ced, $i, 1)*2);
                if($i%2!=0) $parte+=substr($ced, $i, 1);
                if($parte>=10) $parte-=9;

                $suma+=$parte;
            }

            $antes=$suma;

            if($suma>70) $suma=70-$suma;
            else if($suma>50) $suma=60-$suma;
            else if($suma>40) $suma=50-$suma;
            else if($suma>30) $suma=40-$suma;
            else if($suma>20) $suma=30-$suma;
            else if($suma>10) $suma=20-$suma;

            if($suma==substr($ced, 9, 1))  return true;

            return false;
        }
        catch(Exception $ex){
            return false;
        }
    }

    private function comprobarRucJuridico($ruc){
        if(!isset($ruc)) return false;
        if(strlen($ruc)!=13) return false;

        $suma=0;
        
        $suma+=substr($ruc,0,1)*4;
        $suma+=substr($ruc,1,1)*3;
        $suma+=substr($ruc,2,1)*2;
        $suma+=substr($ruc,3,1)*7;
        $suma+=substr($ruc,4,1)*6;
        $suma+=substr($ruc,5,1)*5;
        $suma+=substr($ruc,6,1)*4;
        $suma+=substr($ruc,7,1)*3;
        $suma+=substr($ruc,8,1)*2;

        $res=$suma%11;

        if($res==0 && substr($ruc,9,1)==0) return true;
        if((11-$res)==substr($ruc,9,1)) return true;

        return false;
    }

    private function comprobarRucPublico($ruc){
        if(!isset($ruc)) return false;
        if(strlen($ruc)!=13) return false;

        $suma=0;
        
        $suma+=substr($ruc,0,1)*3;
        $suma+=substr($ruc,1,1)*2;
        $suma+=substr($ruc,2,1)*7;
        $suma+=substr($ruc,3,1)*6;
        $suma+=substr($ruc,4,1)*5;
        $suma+=substr($ruc,5,1)*4;
        $suma+=substr($ruc,6,1)*3;
        $suma+=substr($ruc,7,1)*2;

        $res=$suma%11;

        if($res==0 && substr($ruc,8,1)==0) return true;
        if((11-$res)==substr($ruc,8,1)) return true;

        return false;
    }
}
