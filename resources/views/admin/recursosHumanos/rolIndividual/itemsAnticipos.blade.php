<table class="invisible">
    <tbody id="plantillaItemAnticpos">
        <tr id="row{ID}">
            <td> <input type="checkbox" id="check"  name="check[]"  value="{ID}" onclick="getRow()"> 
                <input type="hidden"  id="IDE" name="IDE[]" value="{IDE}" required readonly>
            </td>   
            <td width="150">{Fecha} <input type="hidden" class="form-controltext"   name="TFecha[]" value="{Fecha}" required readonly></td>
            <td width="150">{Valor} <input type="hidden" class="form-controltext"   name="TValor[]" value="{Valor}" required readonly></td>
            <td width="150"> {Saldo}<input type="hidden" class="form-controltext" id="TSaldo{ID}"   name="TSaldo[]" value="{Saldo}" required readonly></td>
            <td width="150"> <input type="number" class="form-controltext"  id="Descontar{ID}"  name="TDescontar[]" value="{Descontar}" min="0"  step="0.01" onchange="SumaAdelantos({ID});" onkeyup="SumaAdelantos({ID});" >
            <input type="number" class="invisible"   name="TDescont[]" value="{Descontar}" required readonly>
            </td>
           
            <td width="150"><a href="{{ url("asientoDiarioEgreso/ver/{Diario}") }}" target="_blank">{NDiario} </a> </td> 
        
       
        </tr>
    </tbody>
</table>