<table class="invisible">
    <tbody id="plantillaItemnc">
        <tr id="row_{ID}">
            <td width="150"><input class="form-control text-center" name="Dcantidad[]" value="{Dcantidad}" onkeyup="recalcular('{ID}');"/></td>
            <td>{Dcodigo}<input class="invisible" name="DprodcutoID[]" value="{DprodcutoID}" /><input class="invisible" name="Dcodigo[]" value="{Dcodigo}" /></td>
            <td>{Dnombre}<input class="invisible" name="Dnombre[]" value="{Dnombre}" /></td>
            <td>{Diva}<input class="invisible" name="Diva[]" value="{Diva}" /></td>
            <td width="150"><input class="form-control text-center" id="DViva" name="DViva[]" value="{DViva}" readonly/></td>
            <td width="150"><input class="form-control text-center" name="Dpu[]" value="{Dpu}" onkeyup="recalcular('{ID}');"/></td>
            <td width="150"><input class="form-control text-center" name="Ddescuento[]" value="{Ddescuento}" onkeyup="recalcular('{ID}');"/></td>
            <td width="150"><input class="form-control text-center" id="Dtotal[]" name="Dtotal[]" value="{Dtotal}" readonly/></td>
            <td><a onclick="eliminarItem({ID}, '{Diva}', {Dtotal2}, {Ddescuento});" class="btn btn-danger waves-effect"
                    style="padding: 2px 8px;">X</a></td>
        </tr>
    </tbody>
</table>