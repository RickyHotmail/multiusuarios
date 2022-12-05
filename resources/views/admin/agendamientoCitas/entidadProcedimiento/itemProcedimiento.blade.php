<?php $count = 0 ?>
<table class="invisible" id="a">
    <tbody id="plantillaItemProcedimiento">
        <tr class="text-center" id="row_{ID}">
            <td><input class="miCheck" style="margin-top: 5px;" type="checkbox" id="Pcheckbox{ID}" name="Pcheckbox[]" {check} onclick="unlockRow({ID});" />
            <input class="invisible" id="PcheckboxEstado{ID}" name="PcheckboxEstado[]" value="{PcheckboxEstado}" /></td>
            <td>{Pcodigo}<input class="invisible" name="Pcodigo[]" value="{Pcodigo}" /> <input class="invisible" name="Pprocedimiento[]" value="{Pprocedimiento}" />
            <input class="invisible" name="Pcliente_id[]" value="{Pcliente_id}" /></td>
            <td>{Pdescripcion}<input class="invisible" name="Pnombre[]" value="{Pnombre}" /></td>            
            <td>{PespecialidadN}<input class="invisible" name="Pespecialidad[]" value="{Pespecialidad}" /><input class="invisible" name="PespecialidadN[]" value="{PespecialidadN}" /></td>
            <td>
                <div class="input-group-prepend">
                    <div class="input-group-text" style="width:35px; height:30px;">$</div>                    
                    <input type="number" class="input-group-text center" style="height:30px;" id="Pcosto{ID}" name="Pcosto[]" value="{Pcosto}" step="$"  readonly/>
                </div>
            </td>
            <td>
                <div class="input-group-prepend">
                    <input type="text" class="input-group-text" style="width:40px; height:30px;" id="Ptipo{ID}" name="Ptipo[]" value="{Ptipo}" step="$" readonly/>
                    <input type="number" class="input-group-text" style="height:30px;" id="Pcobertura{ID}" name="Pcobertura[]" value="{Pcobertura}" step="0.01" min="0" readonly/>
                </div>
            </td>
            <td>
                <div class="input-group-prepend">
                    <input type="text" class="input-group-text" style="width:40px; height:30px;" value="$" readonly/>
                    <input type="number" class="input-group-text" style="height:30px;" id="Pcobertura2{ID}" value="{Pcobertura2}" readonly/>
                </div>
            </td>
            <td>
                <div class="input-group-prepend">
                    <input type="text" class="input-group-text" style="width:40px; height:30px;" value="$" readonly/>
                    <input type="number" class="input-group-text" style="height:30px;" id="Pcopago{ID}" value="{Pcopago}" step="0.01" min="0" readonly/>
                </div>
            </td>
            
            <?php $count ++ ?>
        </tr>
    </tbody>
</table>
<script>
     
</script>