<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="row_{ID}">
             <td class="text-center">{desde}<input type="hidden"  name="Tdesde[]" value="{desde}"  ></td>
            <td class="text-center">{hasta}<input type="hidden"   name="Thasta[]" value="{hasta}"  ></td>
            <td width="20" class="text-center"> 
                {porcentaje} % <input type="hidden" class="form-controltext"   name="porcentaje[]" value="{porcentaje}"  >
            </td>
            <td width="20" class="text-center"> 
                {dias}  <input type="hidden" class="form-controltext"   name="Tdias[]" value="{dias}"  >
            </td>
            <td width="150" class="text-center">{DCSueldo}<input type="hidden" class="form-controltext"  name="TCSueldo[]"  value="{DCSueldo}"  required readonly><input class="invisible" name="Dsueldo[]" value="{Dsueldo}" /></td>
            <td width="150" class="text-center">{vacaciones} <input type="hidden" class="form-controltext"   name="Tvacaciones[]" value="{vacaciones}"   ></td>
           
            <td width="150" class="text-center">{extras} <input type="hidden" class="form-controltext"   name="Textras[]" value="{extras}"   ></td>
            <td width="150" class="text-center">{horas_suplementarias} <input type="hidden" class="form-controltext"   name="Thoras_suplementarias[]" value="{horas_suplementarias}"  ></td>
            <td width="150" class="text-center">{transporte} <input type="hidden" class="form-controltext"   name="Ttransporte[]" value="{transporte}"  ></td>
            <td width="150" class="text-center">{otrosbon} <input type="hidden" class="form-controltext"   name="Totrosbon[]" value="{otrosbon}"  >
             </td>
            <td width="150" class="text-center">{otrosin} <input type="hidden" class="form-controltext"   name="Totrosin[]" value="{otrosin}"   ></td> 
            <td  width="150" class="text-center">{ingresos}<input type="hidden" class="form-controltext"  name="TTingresos[]"  value="{ingresos}" required readonly>
            </td>
            <td width="150" class="text-center">{salud} <input type="hidden" class="form-controltext"   name="Tsalud[]" value="{salud}" ></td>
            <td width="150" class="text-center">{ppqq} <input type="hidden" class="form-controltext"   name="Tppqq[]" value="{ppqq}" ></td>
            <td width="150" class="text-center">{hipotecarios} <input type="hidden" class="form-controltext"   name="Thipotecarios[]" value="{hipotecarios}" ></td>
            <td width="150" class="text-center">{prestamos} <input type="hidden" class="form-controltext"   name="Tprestamos[]" value="{prestamos}" ></td> 
            <td width="150" class="text-center">{multas} <input type="hidden" class="form-controltext"   name="Tmultas[]" value="{multas}"  ></td>
            <td width="150" class="text-center">{otrosegre} <input type="hidden" class="form-controltext"   name="Totrosegre[]" value="{otrosegre}"  ></td>
            <td width="150" class="text-center">{Ley_salud} <input type="hidden" class="form-controltext"   name="Tley_salud[]" value="{Ley_salud}"  ></td>
            <td width="150" class="text-center">{totalegresos} <input type="hidden" class="form-controltext"   name="totalegre[]" value="{totalegresos}" readonly ></td>
            <td width="150" class="text-center">{subtotal} <input type="hidden" class="form-controltext"   name="subtotal[]" value="{subtotal}" readonly ></td>
            <td><a onclick="eliminarItem({ID});" class="btn btn-danger waves-effect"
                    style="padding: 2px 8px;">X</a></td>
            
            
       
        </tr>
    </tbody>
</table>