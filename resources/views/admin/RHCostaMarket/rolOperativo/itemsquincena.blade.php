<table class="invisible">
    <tbody id="plantillaItemQuincena">
        <tr id="row{ID}">
            <td> <input type="checkbox" id="Qcheck"  name="Qcheck[]"  value="{ID}" onclick="QgetRow()"> 
                <input type="text" class="invisible"  id="QIDE" name="QIDE[]" value="{IDE}" >
            </td>   
            <td width="150">{Fecha} <input type="hidden" class="form-controltext"   name="QTFecha[]" value="{Fecha}" required readonly></td>
            <td width="150">{Valor} <input type="hidden" class="form-controltext"   name="QTValor[]" value="{Valor}" required readonly></td>
            <td width="150">{Saldo} <input type="hidden" class="form-controltext"  id="QTSaldo{ID}" name="QTSaldo[]" value="{Saldo}" required readonly></td>
            <td width="150"> <input type="number" class="form-controltext"  id="QDescontar{ID}"  name="QTDescontar[]" value="{Descontar}" min="0"  step="any" onchange="SumaQuincena({ID});" onkeyup="SumaQuincena({ID});" >
            <input type="number" class="invisible"   name="QTDescont[]" value="{Descontar}" required readonly>
            </td>
           
            <td width="150"><a href="{{ url("asientoDiarioEgreso/ver/{Diario}") }}" target="_blank">{NDiario} </a> </td> 
        
       
        </tr>
    </tbody>
</table>