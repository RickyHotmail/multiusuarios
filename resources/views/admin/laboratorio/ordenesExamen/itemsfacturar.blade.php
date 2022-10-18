<table class="invisible">
    <tbody id="plantillaItemFactura">
        <tr id="row_{ID}">
            <td>{Dcantidad}<input class="invisible" name="Dcantidad[]" value="{Dcantidad}" /></td>
            <td>{Dcodigo}<input class="invisible" name="DprodcutoID[]" value="{DprodcutoID}" /><input class="invisible" name="Dcodigo[]" value="{Dcodigo}" /></td>
            <td>{Dnombre}<input class="invisible" name="Dnombre[]" value="{Dnombre}" /></td>
            <td>$ {valor}<input class="invisible" name="DViva[]" value="{valor}" /></td>
            <td>{%Cobertura}<input class="invisible" name="D%Cobertura[]" value="{%Cobertura}"/></td>
            <td>$ {Cobertura}<input class="invisible" name="DCobertura[]" value="{Cobertura}" /></td>
            <td>$ {Copago}<input class="invisible" name="DCopago[]" value="{Copago}" /></td>
            <td><a onclick="eliminarItem({ID}, {Copago});" class="btn btn-danger waves-effect"
                    style="padding: 2px 8px;">X</a></td>
        </tr>
    </tbody>
</table>