<table class="invisible">
    <tbody id="plantillaItemFacturacion">
        <tr class="text-center" id="row_{ID}">
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
            <td>{FproductoNombre}<input class="invisible" name="FproductoNombre[]" value="{FproductoNombre}"/> <input class="invisible" name="FprocedimientoAId[]" value="{FprocedimientoAId}"/>
            <input class="invisible" name="FproductoId[]" value="{FproductoId}"/></td>
            <td><textarea style="height:30px;" name="Fobservacion[]" value="{Fobservacion}"></textarea></td>           
            <td>{Fcosto}<input class="invisible" name="Fcosto[]" value="{Fcosto}"/> </td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>