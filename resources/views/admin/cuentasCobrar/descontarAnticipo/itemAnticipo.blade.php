<table class="invisible">
    <tbody id="plantillaItemAnticipo">
        <tr id="rowA_{ID}">
            <td><input type="checkbox" id="check{AID}" name="checkAnt{ID}" value="{AID}" onchange="calcularSeleccion('{AID}','{ID}');"><input type="hidden" name="AID[]" value="{AID}"></td>
            <td>{AFecha}</td>
            <td class="derecha-texto">{AMonto}</td>
            <td class="derecha-texto">{ASaldo}<input type="hidden" name="ASaldo[]" value="{ASaldo}"/></td>
            <td class="derecha-texto"><input type="text" class="form-control text-center" name="ADescontar[]"  value="0.00" onkeyup="totalSeleccion('{ID}');" readonly/></td>
            <td>{ADiario}</td>
        </tr>
    </tbody>
</table>