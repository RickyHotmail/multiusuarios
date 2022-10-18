<table class="invisible">
    <tbody id="plantillaItemingresos">
        <tr id="row_{ID}" class="editable">
            <td >{nombre}</td>
            <td id="{rubro}">{valor} 
            </td>
            <input type="hidden" name="rolid[]" value="{idrol}"/>
            <input type="hidden" name="idrubro[]" value="{idrubro}"/>
            <input type="hidden" name="tiporubro[]" value="{tipo}"/>
            <input type="hidden" name="rubro[]"   value="{rubro}"/>
            <input type="hidden" name="valor[]" id="V{rubro}" value="{valor}"/>
            <input type="hidden"name="prubro[]" id="p{rubro}" value="PAGADO"/>
        </tr>
    </tbody>
</table>