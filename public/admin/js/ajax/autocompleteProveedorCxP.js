(function() {
	$("#buscarProveedor").autocomplete({
		source: function(request, response){
			var localObj = window.location;
			var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+"/proveedor/searchN/"+request.term,
				dataType: "json",
				type: "GET",
				data: {
					buscar: request.term
				},
				success: function(data){
					response($.map(data, function(proveedor){
						return {
							label: proveedor.proveedor_nombre,
                            nombre: proveedor.proveedor_nombre,
							id: proveedor.proveedor_id,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			$("#buscarProveedor").val(ui.item.nombre);
			$("#proveedorID").val(ui.item.id);
			return false;
		}
	});
})();