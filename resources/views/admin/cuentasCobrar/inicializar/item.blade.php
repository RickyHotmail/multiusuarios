<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="row_{ID}">
            <td class="filaDelgada15" style="padding-left: 10px !important;">{Cliente}<input type="hidden" name="Dcliente[]" value="{Dcliente}"/></td>
            <td class="filaDelgada15 text-center">{Dnumero}<input type="hidden" name="Dnumero[]" value="{Dnumero}" readonly/></td>
            <td class="filaDelgada15 text-center">{Dvalor}<input type="hidden" name="Dvalor[]" value="{Dvalor}" readonly/></td>
            <td class="filaDelgada15 text-center">{Dsaldo}<input type="hidden" name="Dsaldo[]" value="{Dsaldo}" readonly/></td>
            <td class="filaDelgada15 text-center">{Dfecha}<input type="hidden" name="Dfecha[]" value="{Dfecha}" readonly/></td>
            <td class="filaDelgada15 text-center">{Dvence}<input type="hidden" name="Dvence[]" value="{Dvence}" readonly/></td>
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
        </tr>
    </tbody>
</table>