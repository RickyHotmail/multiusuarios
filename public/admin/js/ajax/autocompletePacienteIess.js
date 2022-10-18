(function() {
	$("#buscarPaciente").autocomplete({
		source: function(request, response){
			var localObj = window.location;
			var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+"/paciente/searchN/"+request.term,
				dataType: "json",
				type: "GET",
				data: {
					buscar: request.term
				},
				success: function(data){
					response($.map(data, function(paciente){
						return {
							nombre: paciente.paciente_nombres,
							label: paciente.paciente_apellidos+" "+paciente.paciente_nombres,	
							apellidos: paciente.paciente_apellidos,	
							cedula: paciente.paciente_cedula,
							aseguradora: paciente.cliente_nombre,
							aseguradoraID: paciente.cliente_id,
							empresa: paciente.entidad_nombre,
							idEmpresa: paciente.entidad_id,
							dependiente: paciente.paciente_dependiente,
							tipoDependencia: paciente.tipod_id,
							cedulaAsegurado: paciente.paciente_cedula_afiliado,
							nombreAsegurado: paciente.paciente_nombre_afiliado,
							documentoPaciente: paciente.documento_paciente,
							documentoAfiliado: paciente.documento_afiliado,
							id: paciente.paciente_id,
																	
						};
					}));
				},
				error: function(data){
					console.log(data);
				}
			});
		},
		select: function(event, ui){
			$("#buscarPaciente").val(ui.item.apellidos+" "+ui.item.nombre);
			$("#idPaciente").val(ui.item.id);
			$("#idCedula").val(ui.item.cedula);
			$("#idAseguradora").val(ui.item.aseguradora);
			$("#idEmpresa").val(ui.item.empresa);
			$("#idCedulaAsegurado").val(ui.item.cedulaAsegurado);
			$("#idNombreAsegurado").val(ui.item.nombreAsegurado);
			$("#identidad").val(ui.item.idEmpresa);
			cargarReclamo(ui.item.aseguradoraID);
			$("#es_dependiente > option[value="+ ui.item.dependiente +"]").attr("selected",true);
			$("#IdTipoDependencia > option[value="+ ui.item.tipoDependencia +"]").attr("selected",true);
			$("#ClienteId").val(ui.item.aseguradoraID);	

			if(ui.item.documentoPaciente!=""){
				$("#a-documento-paciente").attr("href", ui.item.documentoPaciente)
				$("#a-documento-paciente").css("display", "revert")
			}

			if(ui.item.dependiente==1){
				$("#marcoAfiliado").css("display", "revert")

				if(ui.item.documentoAfiliado!=""){
					$("#a-documento-afiliado").attr("href", ui.item.documentoAfiliado)
					$("#a-documento-afiliado").css("display", "revert")
				}
				else{
					$("#a-documento-afiliado").attr("href", "")
					$("#a-documento-afiliado").css("display", "none")
				}
			}
			else
				$("#marcoAfiliado").css("display", "none")
				
			cargarDatosDependencia();
			cargarEspecialidadesPaciente();
			return false;
		}
	});
})();
