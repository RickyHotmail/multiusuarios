<table class="invisible">
    <tbody id="plantillaItemMedicamento">
        <tr class="text-center" id="row_{ID}">
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
            <td>{PmedicinaNombre}<input class="invisible" name="PmedicinaNombre[]" value="{PmedicinaNombre}"/>
            <input class="invisible" name="Pproducto[]" value="{PproductoId}"/> 
            <input class="invisible" name="PmedicinaId[]" value="{PmedicinaId}"/></td>
            <td><div class="form-check "><input class="form-control2 text-center" value="{Pcantidad}" min="1" type="number" name="Pcantidad[]" id="Pcantidad{ID}" readonly required></div></td>
            <td><input class="form-control" type="text" name="Pindicaciones[]" value="{Pindicaciones}" required></td>           
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
