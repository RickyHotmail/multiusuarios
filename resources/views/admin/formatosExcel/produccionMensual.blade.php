<table>
    <thead>
        <tr>
            <th style="font-weight: bold">PACIENTE</th>
            <th style="font-weight: bold">FECHA</th>
            <th style="font-weight: bold">ESPECIALIDAD</th>
            <th style="font-weight: bold">DOCTOR</th>


            <th style="font-weight: bold">VALOR</th>
            <th style="font-weight: bold">COPAGO</th>
            <th style="font-weight: bold">CREDITO</th>
            <th style="font-weight: bold"># FACTURA</th>


            <th style="font-weight: bold">CONCEPTO</th>
            <th style="font-weight: bold">OBSERVACIONES</th>
            <th style="font-weight: bold">LUGAR DE ATENCIÓN</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($datos))
            @foreach($datos['ordenes'] as $orden)
                <tr>
                    <td>{{ $orden->paciente->paciente_nombres }}</td>
                    <td>{{ $orden->orden_fecha }}</td>
                    <td>{{ $orden->especialidad->especialidad_nombre }}</td>

                    @if($orden->medico->empleado_id!=null)
                        <td>{{ $orden->medico->empleado->empleado_nombre }}</td>
                    @else
                        <td>{{ $orden->medico->proveedor->proveedor_nombre }}</td>
                    @endif

                    <td>{{ number_format($orden->orden_valor, 2) }}</td>
                    <td>{{ number_format($orden->orden_copago, 2) }}</td>
                    <td>{{ number_format(0, 2) }}</td>

                    @if($orden->factura!=null)
                        <td>{{ $orden->factura->factura_numero }}</td>
                    @else
                        <td>-</td>
                    @endif

                    <td>{{ $orden->producto->producto_nombre }}</td>
                    <td>{{ $orden->orden_observacion }}</td>
                    <td>{{ $orden->sucursal->sucursal_nombre }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>sdasdas</td>
            </tr>
        @endif
    </tbody>
</table>