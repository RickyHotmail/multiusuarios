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
				url: contextPath+"/buscarClienteNombreCedula/"+request.term,
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
							id: cliente.cliente_id,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			$("#buscarCliente").val(ui.item.nombre);
			$("#idCedulaF").val(ui.item.cedula);
			$("#clienteID").val(ui.item.id);
			
			return false;
		}
	});
})();