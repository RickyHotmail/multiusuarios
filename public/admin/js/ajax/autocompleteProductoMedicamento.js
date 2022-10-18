
	(function() {
		$("#buscarProducto").autocomplete({
			source: function(request, response){
				var localObj = window.location;
				var contextPath = localObj.pathname.split("/")[1];
				if(contextPath=='public'){
					contextPath="/"+contextPath;
				}else{
					contextPath='';
				}
				$.ajax({
					url: contextPath+"/medicinas/searchN/"+request.term,
					dataType: "json",
					type: "GET",
					data: {
						buscar: request.term
					},
					success: function(data){
						response($.map(data, function(producto){
							return {
								nombre: producto.producto_nombre,
								label: producto.producto_nombre,
								precio : producto.producto_precio1,
								tieneIva : producto.producto_tiene_iva,
								stock : producto.producto_stock,
								codigo: producto.producto_codigo,
								id:producto.producto_id,
								idmedicamento:producto.medicamento_id
							};
						}));
					},
				});
			},
			select: function(event, ui){
           
				if(parseFloat(ui.item.stock) > 0){
					document.getElementById("buscarProducto").classList.remove('is-invalid');
					document.getElementById("errorStock").classList.add('invisible');
					$("#codigoProducto").val(ui.item.codigo);
					$("#idProductoID").val(ui.item.id);
					$("#buscarProducto").val(ui.item.nombre)
					$("#id_disponible").val(ui.item.stock)
					$("#idmedicamento").val(ui.item.idmedicamento)
				}else{
					document.getElementById("buscarProducto").classList.add('is-invalid');
					document.getElementById("errorStock").classList.remove('invisible');
				}
				return false;
			}
		});
	})();
