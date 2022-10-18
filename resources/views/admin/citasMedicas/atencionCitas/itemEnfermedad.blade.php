<table class="invisible">
    <tbody id="plantillaItemEnfermedad">
        <tr class="text-center" id="row_{ID}">
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
            <td>{DenfermedadNombre}<input class="invisible" name="DenfermedadNombre[]" value="{DenfermedadNombre}"/> <input class="invisible" name="DenfermedadId[]" value="{DenfermedadId}"/></td>
            <td><textarea style="height:30px;" name="DobservacionEnfer[]" value="{DobservacionEnfer}"></textarea></td>           
            <td><div class="form-check "><input style="width:20px; height:20px;" class="form-check-input" onclick="unlockRow({ID})" type="checkbox" name="DcboxCasoN[]" id="DcboxCasoN_{ID}"> <input class="invisible" id="DcboxCasoNEstado{ID}" name="DcboxCasoNEstado[]" value="{DcboxCasoNEstado}"/></div></td>
            <td><div class="form-check "><input style="width:20px; height:20px;" class="form-check-input" onclick="unlockRow1({ID})" type="checkbox" name="DcboxDefinitivo[]" id="DcboxDefinitivo_{ID}"> <input class="invisible" id="DcboxDefinitivoEstado{ID}" name="DcboxDefinitivoEstado[]" value="{DcboxDefinitivoEstado}"/> </div></td>
            <td></td>
            <td width="10"></td>
        </tr>
    </tbody>
</table>

<script>
    function unlockRow(id){                 
        $('input[type=checkbox]').on('change', function() {
            if ($("#DcboxCasoN_"+id).is(':checked') ) {                
                document.getElementById("DcboxCasoNEstado"+id).value = "1";
            } else {                             
                document.getElementById("DcboxCasoNEstado"+id).value = "0";                
            }
        });
    }
    function unlockRow1(id){                 
        $('input[type=checkbox]').on('change', function() {
            if ($("#DcboxDefinitivo_"+id).is(':checked') ) {                
                document.getElementById("DcboxDefinitivoEstado"+id).value = "1";
            } else {                             
                document.getElementById("DcboxDefinitivoEstado"+id).value = "0";                
            }
        });
    }
</script>












































