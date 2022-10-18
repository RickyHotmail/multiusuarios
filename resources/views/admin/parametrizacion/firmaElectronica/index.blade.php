@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Parámetros Electrónicos</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Firma Electrónica</h5>
        <form class="form-horizontal" method="POST" action="{{ url("firmaElectronica") }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">     
                @if(!empty($firmaElectronica))
                <div class="form-group row">
                    <div class="col-sm-12">  
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-copy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{$firmaElectronica->firma_archivo}}</span>
                            </div>
                        </div>      
                                                            
                    </div>
                </div>         
                @endif 
                <div class="form-group row">
                    <label for="firma_archivo" class="col-sm-2 col-form-label">Archivo</label>
                    <div class="col-sm-10">        
                        <div class="custom-file">
                            <input type="file" accept=".p12" class="custom-file-input" id="firma_archivo" name="firma_archivo" required>
                            <label class="custom-file-label" for="firma_archivo">Seleccionar archivo</label>
                        </div>                                    
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idPass" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" name="idPass" class="form-control" placeholder="Password" value="@if(!empty($firmaElectronica)){{$firmaElectronica->firma_password}}@endif"  required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-2 col-form-label">Fecha de la Firma</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="idFecha" name="idFecha" value="@if(!empty($firmaElectronica)){{$firmaElectronica->firma_fecha}}@endif" required>
                    </div>
                </div>                
            </div>   
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp;Guardar</button>                
            </div>            
            <!-- /.card-footer -->
        </form>
        @if(!empty($firmaElectronica))
        <hr>
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Configuración</h5>
        <form  method="POST" action="{{ route('firmaElectronica.update', [$firmaElectronica->firma_id]) }}">
            @method('PUT')
            @csrf
            <div class="card-body">   
                <div class="form-group row">
                    <label for="idPuerto" class="col-sm-2 col-form-label">Disponibilidad</label>
                    <div class="col-sm-1">     
                        <div class="form-check">
                            @if($firmaElectronica)
                                @if($firmaElectronica->firma_disponibilidad == '1')
                                    <input class="form-check-input" type="radio" name="idDisponibilidad" id="idDisponibilidad" value='1' checked>
                                @else
                                    <input class="form-check-input" type="radio" name="idDisponibilidad" id="idDisponibilidad" value='1'>
                                @endif
                            @else
                                <input class="form-check-input" type="radio" name="idDisponibilidad" id="idDisponibilidad" value='1' checked>
                            @endif
                            <label class="form-check-label" for="idDisponibilidad">Normal</label>
                        </div>
                    </div>
                    <div class="col-sm-9">     
                        <div class="form-check">
                            @if($firmaElectronica)
                                @if($firmaElectronica->firma_disponibilidad == '2')
                                    <input class="form-check-input" type="radio" name="idDisponibilidad" id="idDisponibilidad" value='2' checked>
                                @else
                                    <input class="form-check-input" type="radio" name="idDisponibilidad" id="idDisponibilidad" value='2'>
                                @endif
                            @else
                                <input class="form-check-input" type="radio" name="idDisponibilidad" id="idDisponibilidad" value='2'>
                            @endif
                            <label class="form-check-label" for="idDisponibilidad">Indisponibilidad del Sistema</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idAmbiente" class="col-sm-2 col-form-label">Ambiente</label>
                    <div class="col-sm-1">     
                        <div class="form-check">
                            @if($firmaElectronica)
                                @if($firmaElectronica->firma_ambiente == '1')
                                    <input class="form-check-input" type="radio" name="idAmbiente" id="idAmbiente" value='1' checked>
                                @else
                                    <input class="form-check-input" type="radio" name="idAmbiente" id="idAmbiente" value='1'>
                                @endif
                            @else
                                <input class="form-check-input" type="radio" name="idAmbiente" id="idAmbiente" value='1' checked>
                            @endif
                            <label class="form-check-label" for="idAmbiente">Prueba</label>
                        </div>
                    </div>
                    <div class="col-sm-9">     
                        <div class="form-check">
                            @if($firmaElectronica)
                                @if($firmaElectronica->firma_ambiente == '2')
                                    <input class="form-check-input" type="radio" name="idAmbiente" id="idAmbiente" value='2' checked>
                                @else
                                    <input class="form-check-input" type="radio" name="idAmbiente" id="idAmbiente" value='2'>
                                @endif
                            @else
                                <input class="form-check-input" type="radio" name="idAmbiente" id="idAmbiente" value='2'>
                            @endif
                            <label class="form-check-label" for="idAmbiente">Produccion</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp;Guardar</button>                
            </div>
        </form>
        @endif
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection