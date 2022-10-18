<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="rowde_{ID}">
             <td class="text-center">{desde}</td><input type="hidden"  name="Tdesde[]" value="{desde}"  >
            <td class="text-center">{hasta}</td><input type="hidden"   name="Thasta[]" value="{hasta}"  >
            <td width="20" class="text-center"> 
                {porcentaje} % 
            </td><input type="hidden" class="form-controltext"   name="porcentaje[]" value="{porcentaje}"  >
            <td width="20" class="text-center"> 
                {dias}  
            </td><input type="hidden" class="form-controltext"   name="Tdias[]" value="{dias}"  >
            <td width="150" class="text-center">{DCSueldo}</td><input type="hidden" class="form-controltext"  name="TCSueldo[]"  value="{DCSueldo}"  required readonly><input class="invisible" name="Dsueldo[]" value="{Dsueldo}" />
            
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect"
                    style="padding: 2px 8px;">X</a></td>
            
            
       
        </tr>
    </tbody>
</table>