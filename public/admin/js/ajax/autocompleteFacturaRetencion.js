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
				url: contextPath+"/facturaVentaRetencionRecibida/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
                    bodega : $("#bodega_id").val(),
					tipoDocumento : $("#tipo_doc").val(),					
				},
				success: function(data){
					if($("#tipo_doc").val()=='0'){					
						response($.map(data, function(factura){
							return {
								idFactura: factura.factura_id, 
								serie:  factura.factura_serie,
								secuencial: factura.factura_secuencial,
								numero: factura.factura_numero,
								fechaFactura: factura.factura_fecha,
								ivaFactura: factura.factura_porcentaje_iva,
								label: factura.factura_numero,
								clienteNombre: factura.cliente_nombre,
								clienteCedula: factura.cliente_cedula,
								tipoPago: factura.factura_tipo_pago,
								rango_id: factura.rango_id,
							};
						}));
					}else{
						response($.map(data, function(nDebito){
							return {
								idNotaDebito: nDebito.nd_id, 
								serie:  nDebito.nd_serie,
								secuencial: nDebito.nd_secuencial,
								numero: nDebito.nd_numero,
								fechaFactura: nDebito.nd_fecha,
								ivaFactura: nDebito.nd_porcentaje_iva,
								label: nDebito.nd_numero,
								clienteNombre: nDebito.cliente_nombre,
								clienteCedula: nDebito.cliente_cedula,
								tipoPago: nDebito.nd_tipo_pago,
								rango_id: nDebito.rango_id,
							};
						}));
					}
				},
			});
		},
		select: function(event, ui){
			if(document.getElementById("tipo_doc").value == '0'){
				document.getElementById("idTotalRetenido").value = '0.00';
				document.getElementById("id_total_iva").value = '0.00';
				document.getElementById("id_total_fuente").value = '0.00';
				$("#factura_id").val(ui.item.idFactura);
				$("#factura_serie").val(ui.item.serie);
				secuencial = String(ui.item.secuencial);
				$("#factura_numero").val(secuencial.padStart(9, 0));
				$("#buscarFactura").val(ui.item.numero);
				$("#nombreCliente").val(ui.item.clienteNombre);
				$("#idCedula").val(ui.item.clienteCedula);
				$("#factura_fecha").val(ui.item.fechaFactura);
				$("#idTarifaIva").val(ui.item.ivaFactura);
				$("#factura_tipo_pago").val(ui.item.tipoPago);
				$("#rango_id").val(ui.item.rango_id);
				porcentajeIva = parseFloat(ui.item.ivaFactura) / 100;
				document.getElementById("porcentajeIva").innerHTML = "Tarifa " + ui.item.ivaFactura + "%";
				document.getElementById("iva12").innerHTML = "Iva " + ui.item.ivaFactura+ "%";
				limpiarTabla();
				cargarDetalle(ui.item.idFactura);
				$("#retencion_fecha").val(ui.item.fechaFactura);
				return false;
			}else{
				document.getElementById("idTotalRetenido").value = '0.00';
				document.getElementById("id_total_iva").value = '0.00';
				document.getElementById("id_total_fuente").value = '0.00';
				$("#factura_id").val(ui.item.idNotaDebito);
				$("#factura_serie").val(ui.item.serie);
				secuencial = String(ui.item.secuencial);
				$("#factura_numero").val(secuencial.padStart(9, 0));
				$("#buscarFactura").val(ui.item.numero);
				$("#nombreCliente").val(ui.item.clienteNombre);
				$("#idCedula").val(ui.item.clienteCedula);
				$("#factura_fecha").val(ui.item.fechaFactura);
				$("#idTarifaIva").val(ui.item.ivaFactura);
				$("#factura_tipo_pago").val(ui.item.tipoPago);
				$("#rango_id").val(ui.item.rango_id);
				porcentajeIva = parseFloat(ui.item.ivaFactura) / 100;
				document.getElementById("porcentajeIva").innerHTML = "Tarifa " + ui.item.ivaFactura + "%";
				document.getElementById("iva12").innerHTML = "Iva " + ui.item.ivaFactura+ "%";
				limpiarTabla();
				cargarDetalle(ui.item.idNotaDebito);
				$("#retencion_fecha").val(ui.item.fechaFactura);
				return false;

			}
		}
	});
})();