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
							ruc: proveedor.proveedor_ruc,
							id: proveedor.proveedor_id,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			$("#buscarProveedor").val(ui.item.nombre);
			$("#idRUC").val(ui.item.ruc);
			$("#proveedorID").val(ui.item.id);
			var localObj = window.location;
			var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+"/facturasCompraactiva/searchN/"+ui.item.id,
				dataType: "json",
				type: "GET",
				data: {
					buscar: ui.item.id
				},
				success: function(data){
					document.getElementById("factura_id").innerHTML = "<option value='0' label>--Seleccione una opcion--</option>";
					for (var i=0; i<data.length; i++) {
						document.getElementById("factura_id").innerHTML += "<option value='"+data[i].transaccion_id+"'>"+data[i].transaccion_numero+"</option>";
					}           
				},
			});
			return false;
		}
	});
})();