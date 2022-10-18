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
				url: contextPath+"/factura/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
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
							clienteID: factura.cliente_id,
                            formaPago: factura.factura_tipo_pago,
                            facserie: factura.factura_serie,                            
                            facsecuencial: factura.factura_secuencial,
                            facdiaspalzo: factura.factura_dias_plazo,
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
            $("#factura_fecha").val(ui.item.fechaFactura);
            $("#idTarifaIva").val(ui.item.ivaFactura);
			$("#IdCliente").val(ui.item.clienteID);
            $("#factura_serie").val(ui.item.facserie);
            $("#factura_numero").val(numero(ui.item.facsecuencial)); 
			$("#factura_tipo_pago").val(ui.item.formaPago);        
            credito(ui.item.formaPago,ui.item.facdiaspalzo);
            limpiarTabla();
            cargarDetalle(ui.item.idFactura);
			return false;
		}
	});
})();
function numero(num){
	numtmp='"'+num+'"';
	largo=numtmp.length-2;
	numtmp=numtmp.split('"').join('');
	if(largo==9)return numtmp;
	ceros='';
	pendientes=9-largo;
	for(i=0;i<pendientes;i++)ceros+='0';
	return ceros+numtmp;

}