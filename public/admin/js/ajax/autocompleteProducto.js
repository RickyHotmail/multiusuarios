
	(function() {
		$("#buscarProducto").autocomplete({
			source: function(request, response){
				if($("#clienteID").val() != ''){
					var localObj = window.location;
					var contextPath = localObj.pathname.split("/")[1];
					if(contextPath=='public'){
						contextPath="/"+contextPath;
					}else{
						contextPath='';
					}
					$.ajax({
						async: false,
						url: contextPath+"/productoVenta/searchN/"+request.term,
						dataType: "json",
						type: "GET",
						data: {
							buscar: request.term,
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
									tipop: producto.producto_tipo,
									cv: producto.producto_compra_venta,
									id:producto.producto_id,
									empresa: producto.empresa_estado_cambiar_precio
								};
							}));
						},
					});
				}else{
					bootbox.alert({
						message: "Seleccione un cliente primero.",
						size: 'small'
					});
				}
			},
			select: function(event, ui){
				if(parseFloat(ui.item.stock) > 0 || ui.item.tipop == '2'|| ui.item.cv == '2'){
					var localObj = window.location;
					var contextPath = localObj.pathname.split("/")[1];
					if(contextPath=='public'){
						contextPath="/"+contextPath;
					}else{
						contextPath='';
					}
					$("#id_pu").val(ui.item.precio);
					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
						async: false,
						url: contextPath+"/productoVenta/precio/searchN",
						dataType: "json",
						type: "POST",
						data: {
							producto: ui.item.id,
							cliente: $("#clienteID").val(),
							tipoPago: $("#factura_tipo_pago").val(),
							plazo: $("#factura_dias_plazo").val(),
						},
						success: function(data){
							$("#id_pu").val(data); 
						},
					});
					if($("#id_pu").val() == 0.00 && ui.item.cv == '3' && ui.item.tipop != '2'){
						bootbox.alert({
						message: "El producto seleccionado no tiene un precio configurado con ese tipo de pago y esos dias de plazo.",
							size: 'small'
						});
					}
					if(ui.item.empresa == "1"){
						document.getElementById("id_pu").readOnly = false;
					}else{
						if(ui.item.tipop == "1"){
							document.getElementById("id_pu").readOnly = true;
						}else{
							document.getElementById("id_pu").readOnly = false;
						}
					}
					document.getElementById("buscarProducto").classList.remove('is-invalid');
					document.getElementById("errorStock").classList.add('invisible');
					$("#codigoProducto").val(ui.item.codigo);
					$("#idProductoID").val(ui.item.id);
					$("#buscarProducto").val(ui.item.nombre)
					$("#id_disponible").val(ui.item.stock);
					$("#idtipoProducto").val(ui.item.tipop);
					$("#idCV").val(ui.item.cv);
					if(ui.item.tipop == '2'){
						$("#id_disponible").val(0);
					}
					if(ui.item.tieneIva == "1"){ 
						document.getElementById("tieneIva").checked = true;
					}else{
						document.getElementById("tieneIva").checked = false;
					}
					calcularTotal();
				}else{
					document.getElementById("buscarProducto").classList.add('is-invalid');
					document.getElementById("errorStock").classList.remove('invisible');
				}
				return false;
			}
		});
	})();
