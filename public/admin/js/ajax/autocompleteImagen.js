
	(function() {
		$("#buscarImagen").autocomplete({
			source: function(request, response){
				var localObj = window.location;
				var contextPath = localObj.pathname.split("/")[1];
				if(contextPath=='public'){
					contextPath="/"+contextPath;
				}else{
					contextPath='';
				}
				$.ajax({
					url: contextPath+"/imagenes/searchN/"+request.term,
					dataType: "json",
					type: "GET",
					data: {
						buscar: request.term
					},
					success: function(data){
						response($.map(data, function(imagen){
							return {
								nombre: imagen.producto_nombre,
								id: imagen.imagen_id,
								label: imagen.producto_nombre
							};
						}));
					},
				});
			},
			select: function(event, ui){
				//console.log("imagen "+JSON.stringify(ui))
				//if(parseFloat(ui.item.stock) > 0){
					document.getElementById("buscarImagen").classList.remove('is-invalid');
					$("#buscarImagen").val(ui.item.nombre)
					$("#idImagen").val(ui.item.id)

					//$("#btAnadirImagen").click();

					//$("#buscarProducto").val(ui.item.nombre)

					/*
						$("#codigoProducto").val(ui.item.codigo);
						$("#idProductoID").val(ui.item.id);
						$("#buscarProducto").val(ui.item.nombre)
						$("#id_disponible").val(ui.item.stock)
						$("#idmedicamento").val(ui.item.idmedicamento)
					*/
				//}else{
				//	document.getElementById("buscarProducto").classList.add('is-invalid');
				//	document.getElementById("errorStock").classList.remove('invisible');
				//}

				return false;
			}
		});
	})();
