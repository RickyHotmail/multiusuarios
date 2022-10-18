(function() {
	$("#buscarFactura").autocomplete({
		source: function(request, response){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
			var localObj = window.location;
    		var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+"/facturaVenta/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
                    bodega : $("#bodega_id").val(),					
				},
				success: function(data){
					response($.map(data, function(factura){
						return {
                            idFactura: factura.factura_id, 
							numero: factura.factura_numero,
                            valorFactura: factura.factura_total,
                            fechaFactura: factura.factura_fecha,
                            ivaFactura: factura.factura_porcentaje_iva,
							label: factura.factura_numero,
                            clienteNombre: factura.cliente_nombre,
                            clienteCedula: factura.cliente_cedula,
                            clienteDireccion: factura.cliente_direccion,
                            tipoCliente: factura.tipo_cliente_nombre,
                            vendedor: factura.vendedor_nombre,
							clienteID: factura.cliente_id,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			$("#factura_id").val(ui.item.idFactura);
			$("#buscarFactura").val(ui.item.numero);
			$("#nombreCliente").val(ui.item.clienteNombre);
            $("#idCedula").val(ui.item.clienteCedula);
			$("#idDireccion").val(ui.item.clienteDireccion);
            $("#idVendedor").val(ui.item.vendedor);
            $("#idValorFactura").val(Number(ui.item.valorFactura).toFixed(2));
            $("#idFechaFactura").val(ui.item.fechaFactura);
            $("#idTarifaIva").val(ui.item.ivaFactura);
			$("#clienteID").val(ui.item.clienteID);
            porcentajeIva = parseFloat(ui.item.ivaFactura) / 100;
            document.getElementById("porcentajeIva").innerHTML = "Tarifa " + ui.item.ivaFactura + "%";
            document.getElementById("iva12").innerHTML = "Iva " + ui.item.ivaFactura+ "%";
			$("#idTipoCliente").val(ui.item.tipoCliente);
            limpiarTabla();
            cargarDetalle(ui.item.idFactura);
			return false;
		}
	});
})();