(function() {
	$("#buscarCliente").autocomplete({
		source: function(request, response){
			var localObj = window.location;
    		var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+"/cliente/searchN/"+request.term,
				dataType: "json",
				type: "GET",
				data: {
					buscar: request.term
				},
				success: function(data){
					response($.map(data, function(cliente){
						return {
							nombre: cliente.cliente_nombre,
							label: cliente.cliente_nombre,
							cedula: cliente.cliente_cedula,
							direccion: cliente.cliente_direccion,
							telefono: cliente.cliente_telefono,
							id: cliente.cliente_id,
							tipoCliente: cliente.tipo_cliente_nombre,
							tieneCredito: cliente.cliente_tiene_credito,
							saldopendiente: cliente.saldo_pendiente,
							montoCredito: cliente.cliente_credito,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			eliminarTodo();
			$("#buscarCliente").val(ui.item.nombre);
			$("#idCedula").val(ui.item.cedula);
			$("#idDireccion").val(ui.item.direccion);
			$("#idTelefono").val(ui.item.telefono);
			$("#clienteID").val(ui.item.id);
			$("#idTipoCliente").val(ui.item.tipoCliente);
			$("#saldoPendienteID").val(Number(ui.item.saldopendiente).toFixed(2));
			$("#idMontoCredito").val(ui.item.montoCredito);
			$("#idTieneCredito").val(ui.item.tieneCredito);
			return false;
		}
	});
})();