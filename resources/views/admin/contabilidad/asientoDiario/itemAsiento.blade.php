<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="row_{ID}">
            <td>{codigo}<input type="hidden" name="DidCuenta[]" value="{idCuenta}" /></td>
            <td>{cuenta}</td>
            <td>{descripcion}<input type="hidden" name="Ddescripcion[]" value="{descripcion}" /></td>
            <td class="text-center">{debe}<input type="hidden" name="Ddebe[]" value="{debe}" /></td>
            <td class="text-center">{haber}<input type="hidden" name="Dhaber[]" value="{haber}" /></td>
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
        </tr>
    </tbody>
</table>
<table class="invisible">
    <tbody id="plantillaItemPadre">
        <tr id="row_{ID}">
            <td><b>{codigo}</b><input type="hidden" name="DidCuenta[]" value="{idCuenta}" /></td>
            <td><b>{cuenta}</b></td>
            <td><input type="hidden" name="Ddescripcion[]" value="{descripcion}" /></td>
            <td class="text-center"><input type="hidden" name="Ddebe[]" value="{debe}" /></td>
            <td class="text-center"><input type="hidden" name="Dhaber[]" value="{haber}" /></td>
            <td></td>
        </tr>
    </tbody>
</table>