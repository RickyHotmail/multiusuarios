@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("parametrizacionRol") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Configurar Parametrizacion de Rol</h3>
            <button type="submit" class="btn btn-success btn-sm float-right"><i class="fa fa-save"></i>&nbsp;Guardar</button>
        </div>
        <div class="card-body">                       
            <div class="form-group row">
                <label for="idDias" class="col-sm-2 col-form-label">Dias de Trabajo</label>
                <div class="col-sm-9">
                <input type="number" class="form-control" id="idDias" name="idDias" placeholder="Dias de Trabajo" value="@if(isset($parametrizar->parametrizar_dias_trabajo)){{$parametrizar->parametrizar_dias_trabajo}}@endif"   required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idBasico" class="col-sm-2 col-form-label">Sueldo Basico</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idBasico" name="idBasico" placeholder="Sueldo Basico" value="@if(isset($parametrizar->parametrizar_sueldo_basico)){{$parametrizar->parametrizar_sueldo_basico}}@endif"  step="any" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPersonal" class="col-sm-2 col-form-label">IEES Personal</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idPersonal" name="idPersonal" value="@if(isset($parametrizar->parametrizar_iess_personal)){{$parametrizar->parametrizar_iess_personal}}@endif" min="0" max="100" step="any"  placeholder="IEES Personal" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPatronal" class="col-sm-2 col-form-label">IEES Patronal</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idPatronal" name="idPatronal" value="@if(isset($parametrizar->parametrizar_iess_patronal)){{$parametrizar->parametrizar_iess_patronal}}@endif" min="0" max="100" step="any"  placeholder="IEES Patronal" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idReserva" class="col-sm-2 col-form-label">Fondo Reserva</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idReserva" name="idReserva" value="@if(isset($parametrizar->parametrizar_fondos_reserva)){{$parametrizar->parametrizar_fondos_reserva}}@endif" min="0" max="100" step="any"  placeholder="Fondo Reserva" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idExtras" class="col-sm-2 col-form-label">Horas Extras</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idExtras" name="idExtras" value="@if(isset($parametrizar->parametrizar_horas_extras)){{$parametrizar->parametrizar_horas_extras}}@endif" min="0" max="100" step="any"  placeholder="Horas Extras" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idExtras" class="col-sm-2 col-form-label">Porcentaje Horas Extras</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idExtrasPor" name="idExtrasPor" value="@if(isset($parametrizar->parametrizar_porcentaje_he)){{$parametrizar->parametrizar_porcentaje_he}}@endif" min="0" max="100" step="any"  placeholder="Porcentaje Horas Extras" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idSecap" class="col-sm-2 col-form-label">Iece Secap</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idSecap" name="idSecap" value="@if(isset($parametrizar->parametrizar_iece_secap)){{$parametrizar->parametrizar_iece_secap}}@endif" min="0" max="100" step="any" placeholder="Iece Secap" required>
                </div>
            </div>
            <input type="hidden" class="form-control" id="idiessgeren" name="idiessgeren"  min="0" max="100" step="any" value="17.60" required>
        </div>              
    </div>
</form>
@endsection