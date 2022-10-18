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
							id: cliente.cliente_id,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			$("#buscarCliente").val(ui.item.nombre);
			$("#clienteID").val(ui.item.id);
			return false;
		}
	});
})();