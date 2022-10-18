<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="row_{ID}">
            <td>{cuenta}<input type="hidden" name="DidCuenta[]" value="{idCuenta}" /></td>
            <td>{tipo}<input type="hidden" name="Dtipo[]" value="{tipo}" /></td>
            <td>{descripcion}<input type="hidden" name="Ddescripcion[]" value="{descripcion}" /></td>
            <td class="text-center">{haber}<input type="hidden" name="Dhaber[]" value="{haber}" /></td>
           
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
        </tr>
    </tbody>
</table>
<table class="invisible">
    <tbody id="plantillaItemPadre">
        <tr id="row_{ID}">
            <td><b>{cuenta}</b><input type="hidden" name="DidCuenta[]" value="{idCuenta}" /></td>
            <td><input type="hidden" name="Dtipo[]" value="{tipo}" /></td>
            <td><input type="hidden" name="Ddescripcion[]" value="{descripcion}" /></td>
            <td class="text-center"><input type="hidden" name="Dhaber[]" value="{haber}" /></td>
            <td></td>
        </tr>
    </tbody>
</table>