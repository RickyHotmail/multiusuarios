<table class="invisible">
    <tbody id="plantillaItemAlimentacion">
        <tr id="row_{ID}" class="editable">
            <td >{ID} <input type="hidden" name="DID[]" value="{DIDE}"/></td>
            <td >{DCedula}</td>
            <td >{DNombre} <input type="hidden" name="nombre[]" value="{nombre}"/><input type="hidden" name="idalim[]" value="{idalimento}"/><input type="hidden" name="idrol[]" value="{idrol}"/></td>
            <td ><input type="number" class="form-controltext" id="Valor[]" name="Valor[]"  value="{valor}" step="any" {editable} onkeyup="totalSeleccion();" onclick="totalSeleccion();"/></td>
            <td>{rol}</td>
        </tr>
    </tbody>
</table>