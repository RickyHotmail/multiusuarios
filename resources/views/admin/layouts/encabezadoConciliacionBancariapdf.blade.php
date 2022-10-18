<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PAGUPASOFT</title>
    <link rel="stylesheet" href="admin/css/pdf/documentosPDF.css" media="all" />
</head>

<body>
    <header>
        <div style="float: right; font-size: 12px; text-align: right;">
            <?php echo('Fecha: '.date("d/m/Y")); ?><br><?php echo('Hora: '.date("H:i:s")); ?></div>
        <table>
            <tr>
                <td style="width: 180px;">
                    <table>
                        <tr>
                            <td class="centrar">@if(!empty($empresa->empresa_logo))<img class="logo"
                                    src="logos/{{$empresa->empresa_logo}}">@endif</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td class="letra20 negrita">{{ $empresa->empresa_nombreComercial }}</td>
                        </tr>
                        @if($empresa->empresa_nombreComercial != $empresa->empresa_razonSocial)
                        <tr>
                            <td class="letra15">{{ $empresa->empresa_razonSocial }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="letra15">{{ $empresa->empresa_ruc }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            @yield('titulo')  
            <tr style="height: 180px;">
                <td></td>
            </tr>                               
        </table>         
    </header>
    <footer>
        <table>
            <tr>
                <td class="letra12">
                    <p class="izq">
                        NEOPAGUPA
                    </p>
                </td>
                <td class="letra12">
                    <p class="page">
                        Página
                    </p>
                </td>
            </tr>
        </table>
    </footer>
    <div id="content">        
        @yield('contenido')
    </div>
</body>

</html>