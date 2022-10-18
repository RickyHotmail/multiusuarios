<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>PAGUPASOFT</title>
        <link rel="stylesheet" href="admin/css/pdf/documentosPDF.css" media="all" />
        
        <style>
            @page {
                margin-top: 70px;
            }
        </style>
    </head>
    <body>
        @if($titulo!="")
            <h1 style="text-align: center; font-size: 12px; font-weight: bold">DOCUMENTO {{ $titulo }}</h1>
        @endif
        <div class="col-md-12">
            @foreach($imagenes as $foto)
                <img width=@if($tamanio) 100% @else 45%  @endif src="{{ $foto }}">
            @endforeach
        </div>
    </body>
</html>