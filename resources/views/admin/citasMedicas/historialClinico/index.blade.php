@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Historial Clínico</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Historia</th>
                    <th>ID</th>
                    <th>Apellidos/Nombres</th> 
                    <th>Edad</th>  
                    <th>Teléfonos</th>  
                    <th>E-mail</th>                                                                         
                </tr>
            </thead>
            <tbody>
            <?php $count = 0;?>
            @foreach($pacientes as $paciente)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url('historialClinico/'.$paciente->paciente_id) }}/ver"  class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver historial"><i class="fa fa-calendar-check"></i></a> 
                    </td>    
                    <td>{{ $paciente->paciente_cedula}}</td>   
                    <td>{{ $paciente->paciente_cedula}}</td>         
                    <td>{{ $paciente->paciente_apellidos}} {{ $paciente->paciente_nombres }} </td>      
                    <td><input name="edadPaciente"  id="edadPaciente_<?php echo $count;?>" style="background: transparent; border: none; width: 25px;text-align: center"></td>          
                    <script>
                        var edadPaciente = edad('{{$paciente->paciente_fecha_nacimiento}}');
                        document.getElementById("edadPaciente_<?php echo $count;?>").value = edadPaciente;
                    </script>
                    <td>{{ $paciente->paciente_celular}}</td>           
                    <td>{{ $paciente->paciente_email}}</td>               
                </tr>
                <?php $count = $count + 1;?>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection
<script>
    function edad(fechaNacimiento){
        //El siguiente fragmento de codigo lo uso para igualar la fecha de nacimiento con la fecha de hoy del paciente
        let d = new Date(),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;
        d=[year, month, day].join('-')
        /*------------*/
        var hoy = new Date(d);//fecha del sistema con el mismo formato que "fechaNacimiento"
        var cumpleanos = new Date(fechaNacimiento);
        //alert(cumpleanos+" "+hoy);
        //Calculamos años
        var edad = hoy.getFullYear() - cumpleanos.getFullYear();
        var m = hoy.getMonth() - cumpleanos.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
            edad--;
        }
        // calculamos los meses
        var meses=0;
        if(hoy.getMonth()>cumpleanos.getMonth()){
            meses=hoy.getMonth()-cumpleanos.getMonth();
        }else if(hoy.getMonth()<cumpleanos.getMonth()){
            meses=12-(cumpleanos.getMonth()-hoy.getMonth());
        }else if(hoy.getMonth()==cumpleanos.getMonth() && hoy.getDate()>cumpleanos.getDate() ){
            if(hoy.getMonth()-cumpleanos.getMonth()==0){
                meses=0;
            }else{
                meses=11;
            }
        }
        // Obtener días: día actual - día de cumpleaños
        let dias  = hoy.getDate() - cumpleanos.getDate();
        if(dias < 0) {
            // Si días es negativo, día actual es mayor al de cumpleaños,
            // hay que restar 1 mes, si resulta menor que cero, poner en 11
            meses = (meses - 1 < 0) ? 11 : meses - 1;
            // Y obtener días faltantes
            dias = 30 + dias;
        }
        console.log(`${edad}`);
        var msg = `${edad}`;
        return msg;
    }   
</script>