<table class="invisible">
    <tbody id="plantillaItemAlimentacion">
        <tr id="row_{ID}" class="editable">
            <td >{ID} <input type="hidden" name="DID[]" value="{DIDE}"/></td>
            <td >{DCedula}</td>
            <td >{DNombre} <input type="hidden" name="nombre[]" value="{nombre}"/><input type="hidden" name="idalim[]" value="{idalimento}"/></td>
            <td><input type="number" class="form-controltext" id="Valor[]" name="Valor[]"  value="{valor}"  step="any" onkeyup="totalSeleccion();" onclick="totalSeleccion();" {editable}/></td>
            <td>{rol}</td>
        </tr>
    </tbody>
</table>