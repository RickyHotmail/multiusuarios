<table class="invisible">
    <tbody id="plantillaItemnc">
        <tr id="row_{ID}">
            <td>{Dcantidad}<input class="invisible" name="Dcantidad[]" value="{Dcantidad}" /></td>
            <td>{Dcodigo}<input class="invisible" name="DprodcutoID[]" value="{DprodcutoID}" /><input class="invisible" name="Dcodigo[]" value="{Dcodigo}" /></td>
            <td>{Dnombre}<input class="invisible" name="Dnombre[]" value="{Dnombre}" /></td>
            <td>{Diva}<input class="invisible" name="Diva[]" value="{Diva}" /></td>
            <td>{DViva}<input class="invisible" name="DViva[]" value="{DViva}" /></td>
            <td>{Dpu}<input class="invisible" name="Dpu[]" value="{Dpu}" /></td>
            <td>{Ddescuento}<input class="invisible" name="Ddescuento[]" value="{Ddescuento}" /></td>
            <td>{Dtotal}<input class="invisible" name="Dtotal[]" value="{Dtotal}" /></td>
            <td><a onclick="eliminarItem({ID}, '{Diva}', {Dtotal2}, {Ddescuento});" class="btn btn-danger waves-effect"
                    style="padding: 2px 8px;">X</a></td>
        </tr>
    </tbody>
</table>