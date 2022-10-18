<table class="invisible">
    <tbody id="plantillaItem">
        <tr id="row_{ID}">
            <td> <input type="checkbox" name="checkbox[]" checked value="{ID}"></td>   
            <td>{Dcedula}</td>
            <td>{Dnombre}</td>
            <td width="20"> 
                <input type="number" class="form-controltext"   name="Ddias[]" value="{Ddias}" onclick="recalcular('{ID}','{Dsueldo}');" onkeyup="recalcular('{ID}','{Dsueldo}');">
            
            </td>
            <td width="150"><input type="number" class="form-controltext"  name="DCSueldo[]"  value="{DCSueldo}" required readonly><input class="invisible" name="Dsueldo[]" value="{Dsueldo}" /></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dvacaciones[]" value="0.00" onclick="recalcularfondo('{ID}','{Dsueldo}');"  onkeyup="recalcularfondo('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dmaternidad[]" value="0.00" onclick="recalcularfondo('{ID}','{Dsueldo}');" onkeyup="recalcularfondo('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dsubsidiado[]" value="0.00" onclick="recalcularfondo('{ID}','{Dsueldo}');" onkeyup="recalcularfondo('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dextras[]" value="0"  {horasextras} onclick="recalcularfondo('{ID}','{Dsueldo}');" onkeyup="recalcularfondo('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dhoras_suplementarias[]" value="0.00" onclick="recalcularfondo('{ID}','{Dsueldo}');" onkeyup="recalcularfondo('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dtransporte[]" value="0.00" onclick="recalcularfondo('{ID}','{Dsueldo}');" onkeyup="recalcularfondo('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dotrosbon[]" value="0.00" onclick="recalculartotalIngreso('{ID}','{Dsueldo}');" onkeyup="recalculartotalIngreso('{ID}','{Dsueldo}');">
             </td>
            <td  width="150"><input type="text" class="form-controltext"  name="Dfondo[]"  value="{Dfondo}" required readonly> 
            <input class="invisible" name="fondo[]"  value="{fondo}" required readonly>
            <input class="invisible" name="fondofecha[]"  value="{fondofecha}" required readonly>
            <input class="invisible" name="porcefondo[]"  value="{porcefondo}" required readonly>
            </td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dotrosin[]" value="0.00" onclick="recalculartotalIngreso('{ID}','{Dsueldo}');" onkeyup="recalculartotalIngreso('{ID}','{Dsueldo}');" ></td>
            <td width="150"> <input type="text" class="form-controltext"   name="Dtercero[]" value="{Dtercero}" required readonly>
            <input class="invisible" name="tercero[]"  value="{tercero}" required readonly>
            <input class="invisible" name="terceroacu[]"  value="{terceroacu}" required readonly>
            </td>
            <td width="150"> <input type="text" class="form-controltext"   name="Dcuarto[]" value="{Dcuarto}"required readonly >
            <input class="invisible" name="cuarto[]"  value="{cuarto}" required readonly>
            <input class="invisible" name="cuartoacu[]"  value="{cuartoacu}" required readonly>
            <input class="invisible" name="sueldobasico[]"  value="{sueldobasico}" required readonly>
            </td>
            <td  width="150"><input type="text" class="form-controltext"  name="DTingresos[]"  value="{DTingresos}" required readonly> 
            <input class="invisible" name="diastraba[]"  value="{diastraba}" required readonly>
            </td>
            <td width="150"> <input type="text" class="form-controltext"   name="Iess[]" value="{Iess}" required readonly>
            <input class="invisible" name="%iess[]"  value="{%iess}" required readonly>
            </td>
            <td width="150"> <input type="text" class="form-controltext"   name="Iessasu[]" value="{Iessasu}" required readonly>
            <input class="invisible" name="%iessasu_check[]"  value="{%iessasu_check}" required readonly>
            </td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dsalud[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dalimentacion[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dppqq[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dhipotecarios[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dprestamos[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');"></td>
            <td width="150"> <input type="number" class="form-controltext"   name="Danticipos[]" value="{Danticipos}" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');"></td>
           
            <td width="150"> <input type="number" class="form-controltext"   name="Dmultas[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');" ></td>
            <td width="150"> <input type="number" class="form-controltext"   name="impurenta[]" value="{impurenta}" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');"  onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');" >
            <input class="invisible" name="impuesto[]"  value="{impuesto}" required readonly>
            </td>
            <td width="150"> <input type="number" class="form-controltext"   name="Dotrosegre[]" value="0.00" onclick="recalculartotalEgresos('{ID}','{Dsueldo}');" onkeyup="recalculartotalEgresos('{ID}','{Dsueldo}');" ></td>
            <td width="150"> <input type="text" class="form-controltext"   name="totalegre[]" value="{totalegre}" required readonly>
            </td>
            <td width="150"> <input type="text" class="form-controltext"   name="total[]" value="{total}" required readonly>
            </td>
        </td>
        </tr>
    </tbody>
</table>