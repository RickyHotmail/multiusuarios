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
				url: contextPath+"/documentoAnulado/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
                    bodega : $("#bodega_id").val(),
                    tipoDocumento : $("#tipo_documento").val(),
				},
				success: function(data){
					response($.map(data, function(documento){
                        if($("#tipo_documento").val() == 1){
                            return {
                                idDoc: documento.factura_id, 
                                serie:  documento.factura_serie,
                                secuencial: documento.factura_secuencial,
                                numero: documento.factura_numero,
                                fechaDoc: documento.factura_fecha,
                                ivaFDoc: documento.factura_porcentaje_iva,
                                label: documento.factura_numero,
                                clienteNombre: documento.cliente_nombre,
                                clienteCedula: documento.cliente_cedula,
                                tipoPago: documento.factura_tipo_pago,
                            };
                        }
						if($("#tipo_documento").val() == 2){
                            return {
                                idDoc: documento.nc_id, 
                                serie:  documento.nc_serie,
                                secuencial: documento.nc_secuencial,
                                numero: documento.nc_numero,
                                fechaDoc: documento.nc_fecha,
                                ivaFDoc: documento.nc_porcentaje_iva,
                                label: documento.nc_numero,
                                clienteNombre: documento.cliente_nombre,
                                clienteCedula: documento.cliente_cedula,
                                tipoPago: documento.factura_tipo_pago,
                            };
                        }
                        if($("#tipo_documento").val() == 3){
                            return {
                                idDoc: documento.nd_id, 
                                serie:  documento.nd_serie,
                                secuencial: documento.nd_secuencial,
                                numero: documento.nd_numero,
                                fechaDoc: documento.nd_fecha,
                                ivaFDoc: documento.nd_porcentaje_iva,
                                label: documento.nd_numero,
                                clienteNombre: documento.cliente_nombre,
                                clienteCedula: documento.cliente_cedula,
                                tipoPago: documento.nd_tipo_pago,
                            };
                        }
					}));
				},
			});
		},
		select: function(event, ui){
			$("#doc_id").val(ui.item.idDoc);
            $("#factura_serie").val(ui.item.serie);
            secuencial = String(ui.item.secuencial);
            $("#factura_numero").val(secuencial.padStart(9, 0));
			$("#buscarFactura").val(ui.item.numero);
			$("#nombreCliente").val(ui.item.clienteNombre);
            $("#idCedula").val(ui.item.clienteCedula);
            $("#factura_fecha").val(ui.item.fechaDoc);
            $("#idTarifaIva").val(ui.item.ivaFDoc);
            $("#factura_tipo_pago").val(ui.item.tipoPago);
            porcentajeIva = parseFloat(ui.item.ivaFDoc) / 100;
            document.getElementById("porcentajeIva").innerHTML = "Tarifa " + ui.item.ivaFDoc + "%";
            document.getElementById("iva12").innerHTML = "Iva " + ui.item.ivaFDoc+ "%";
            limpiarTabla();
            cargarDetalle(ui.item.idDoc);
			return false;
		}
	});
})();