
<table class="invisible" id="a">
    <tbody id="plantillaItemProcedimiento">
            <tr class="text-center" id="row_{ID}">
                <td><input class="miCheck" style="margin-top: 5px;" type="checkbox" id="Pcheckbox{ID}" name="Pcheckbox[]" value="{check}" onclick="unlockRow({ID});" />
                <input class="invisible" id="ide{ID}" name="ide[]" value="{PIDE}" /></td>
                <td>{Pcodigo}<!--input class="invisible" name="Pcodigo[]" value="{Pcodigo}" /--> 
                    <!--input class="invisible" name="Pprocedimiento[]" value="{Pprocedimiento}" /-->
                    <!--input class="invisible" name="Pcliente_id[]" value="{Pcliente_id}" /-->
                </td>
                <td>{Pnombre}<!--input class="invisible" name="Pnombre[]" value="{Pnombre}" /></td-->
                <td>{PespecialidadN}<!--input class="invisible" name="Pespecialidad[]" value="{Pespecialidad}" /><input class="invisible" name="PespecialidadN[]" value="{PespecialidadN}" /></td-->                  
                <td>{Pprecio}<!--input class="invisible" name="Pprecio[]" value="{Pprecio}" /--></td>            
                <td><input type="number" class="input-group-text" style="height:30px;" id="Pcosto{ID}" name="Pcosto[]" value="{Pcosto}" step="any"  readonly/></td>
                <td><input type="text" class="input-group-text" style="height:30px;" id="PcodigoT{ID}" name="PcodigoT[]" value="{PcodigoT}" step="any"  readonly/></td>
            </tr>
    </tbody>
</table>
