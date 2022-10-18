@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">FICHA EMPLEADO</td></tr>
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">DATOS DE GENERALES</h5>
    @endsection
    <br>
    <br>
        <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" style="width: 150px;">Nombre:</td>
                <td>{{ $empleado->empleado_nombre}}</td>                
                <td style="width: 120px;" rowspan="4" ><div style="width:100px;height:100px;border:1px solid #000;" >tu foto aqui!!</div></td>               
            </tr>
            <tr class="letra14">
                <td class="negrita" style="width: 125px;">CI:</td>
                <td>{{$empleado->empleado_cedula}}</td>
            </tr> 
            <tr class="letra14">
                <td class="negrita" style="width: 150px;">Lugar de Nacimiento:</td>
                <td>{{ $empleado->empleado_lugar_nacimiento }}</td>
            </tr>
            <tr class="letra14">
                <td class="negrita" style="width: 125px;">Nacionalidad:</td>
                <td>{{$empleado->empleado_nacionalidad}}</td>
            </tr>
        </table>
        <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" style="width: 150px;">Direccion:</td>
                <td>{{ $empleado->empleado_direccion}}</td>               
            </tr>                      
        </table>
        <br>
        <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" style="width: 150px;">Sexo:</td>
                <td>{{ $empleado->empleado_sexo}}</td>
                <td class="negrita" style="width: 100px;">Estatura:</td>
                <td>{{$empleado->empleado_estatura}}cm</td>               
            </tr>
            <tr class="letra14">               
                <td class="negrita" style="width: 125px;">Grupo Sanguineo:</td>
                <td>{{ $empleado->empleado_grupo_sanguineo }}</td>
                <td class="negrita" style="width: 125px;">Estado Civil:</td>
                <td>{{ $empleado->empleado_estado_civil}}</td>
            </tr>
            <tr class="letra14">               
                <td class="negrita" style="width: 125px;">Fecha Nacimiento:</td>
                <td>{{$empleado->empleado_fecha_nacimiento}}</td>
                <td class="negrita" style="width: 125px;">Edad:</td>
                <td>{{ $empleado->empleado_edad }}</td>
            </tr>            
            <tr class="letra14">
                <td class="negrita" style="width: 125px;">Telefono:</td>
                <td>{{ $empleado->empleado_telefono}}</td>
                <td class="negrita" style="width: 100px;">Celular:</td>
                <td>{{$empleado->empleado_celular}}</td>               
            </tr> 
            <tr class="letra14">              
                <td class="negrita" style="width: 125px;">Correo:</td>
                <td align="left">{{ $empleado->empleado_correo }}</td>
            </tr>            
        </table>
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">DATOS LABORALES</h5>
        <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" style="width: 150px;">Fecha Ingreso:</td>
                <td>{{ $empleado->empleado_fecha_ingreso}}</td>
                <td class="negrita" style="width: 200px;">Cargo:</td>
                <td>{{$empleado->cargo->empleado_cargo_nombre}}</td>
                
            </tr>               
            <tr class="letra14">
                <td class="negrita" style="width: 125px;">Departamento:</td>
                @if(isset($empleado->departamento->departamento_nombre))
                    <td>{{$empleado->departamento->departamento_nombre }}</td>
                @endif
                <td class="negrita" style="width: 125px;">Tipo de Empleado:</td>
                @if(isset($empleado->tipo->tipo_descripcion))
                    <td>{{ $empleado->tipo->tipo_descripcion}}</td>
                @endif
            </tr>
            <tr class="letra14">                
                <td class="negrita" style="width: 125px;">Jornada:</td>
                <td>{{$empleado->empleado_jornada}}</td>
                <td class="negrita" style="width: 125px;">Sueldo:</td>
                <td>{{ $empleado->empleado_sueldo }}</td>
            </tr>        
        </table>
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">DATOS DE FAMILIARES</h5>
        <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" style="width: 30px;">Nombre de Contacto:</td>
                <td>{{ $empleado->empleado_contacto_nombre}}</td>                               
            </tr> 
            <tr class="letra14">
                <td class="negrita" style="width: 200px;">Direccion:</td>
                <td>{{ $empleado->empleado_contacto_direccion}}</td>                               
            </tr>           
        </table>
        <table style="white-space: normal!important; border-collapse: collapse;">            
            <tr class="letra14">
                <td class="negrita" style="width: 200px;">Telefono:</td>
                <td>{{ $empleado->empleado_contacto_telefono}}</td>                                                        
            </tr>
        </table>
        <table style="white-space: normal!important; border-collapse: collapse;">            
            <tr class="letra14">               
                <td class="negrita" style="width: 200px;">Celuar:</td>
                <td>{{ $empleado->empleado_contacto_celular}}</td>                                     
            </tr>
        </table>
        <table style="white-space: normal!important; border-collapse: collapse;">            
            <tr class="letra14">               
                <td class="negrita" style="width: 200px;">Carga Familiar:</td>
                <td>{{ $empleado->empleado_carga_familiar}}</td>                               
            </tr>
        </table>

@endsection