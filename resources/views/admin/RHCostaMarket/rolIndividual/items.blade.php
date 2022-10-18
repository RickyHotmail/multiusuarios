<table class="invisible">
    <tbody id="plantillaItemAlimentacion">
        <tr id="row{ID}">
        <td > <input type="checkbox" id="checkal"   name="checkali[]"  value="{ID}" onclick="getalimentacion({ID})"> 
                <input type="hidden"  id="IDEali" name="IDEali[]" value="{IDE}" required readonly>
            </td>   
            <td class="text-center">{factura} </td>
            <td class="text-center">{Fecha} </td>
            <td> <input type="number" class="form-controltext"  id="AValor{ID}" name="Valor[]" value="{Valor}" required readonly>
            </td> 
        </tr>
    </tbody>
</table>