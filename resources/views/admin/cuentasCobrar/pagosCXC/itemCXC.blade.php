<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="row_{ID}">
            <td class="filaDelgada15"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="check{Did}" name="checkCXC{ID}" value="{Did}" onchange="calcularSeleccion('{Did}','{ID}');"><label for="check{Did}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;"></label></div><input class="invisible" name="Did[]" value="{Did}" /></td>
            <td class="filaDelgada15">{Ddocumento}</td>
            <td class="filaDelgada15">{Dnumero}</td>
            <td class="filaDelgada15">{Dsaldo}<input type="hidden" name="Dsaldo[]" value="{Dsaldo}" readonly/></td>
            <td class="filaDelgada15 text-center"><input style="width: 110px !important;" class="text-center" name="Ddescontar[]" value="0.00" onkeyup="totalSeleccion('{ID}');" readonly/></td>
            <td class="filaDelgada15">{Dfecha}</td>
            <td class="filaDelgada15">{Dvence}</td>
        </tr>
    </tbody>
</table>