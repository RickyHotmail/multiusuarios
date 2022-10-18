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
				url: contextPath+"/facturaVentaAnt/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
                    bodega : $("#bodega_id").val(),
				},
				success: function(data){
					if(data[0].length > 0){
						response($.map(data[0], function(factura){
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
								saldo: factura.cuenta_saldo,
								tipo: 'factura',
							};
						}));
					}else{
						response($.map(data[1], function(cxc){
							return {
								idFactura: cxc.cuenta_id, 
								numero: cxc.cuenta_descripcion.slice(19),
								valorFactura: cxc.cuenta_valor_factura,
								fechaFactura: cxc.cuenta_fecha,
								label: cxc.cuenta_descripcion.slice(19),
								clienteNombre: cxc.cliente_nombre,
								clienteCedula: cxc.cliente_cedula,
								clienteID: cxc.cliente_id,
								formaPago: 'CREDITO',
								facserie: cxc.cuenta_descripcion.slice(19,-9),           
								facsecuencial: cxc.cuenta_descripcion.slice(25),
								saldo: cxc.cuenta_saldo,
								tipo: 'cxc',
							};
						}));
					}
				},
			});
		},
		select: function(event, ui){
			if(ui.item.tipo == 'factura'){
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
				$("#tipoDocumento").val(ui.item.tipo);
				document.getElementById("tablaResumenId").classList.remove('invisible');
				document.getElementById("tablaDetalleId").classList.remove('invisible');
				if(Number(ui.item.saldo) <= 0){
					document.getElementById("guardarID").disabled = true;
				}else{
					document.getElementById("guardarID").disabled = false;
				}         
				porcentajeIva = parseFloat(ui.item.ivaFactura) / 100;
				document.getElementById("porcentajeIva").innerHTML = "Tarifa " + ui.item.ivaFactura + "%";
				document.getElementById("iva12").innerHTML = "Iva " + ui.item.ivaFactura+ "%";
				limpiarTabla();
				$("#saldo_factura").val(Number(ui.item.saldo).toFixed(2)); 
				cargarDetalle(ui.item.idFactura);
				cargarAnticipos(ui.item.clienteID);
				return false;
			}else{
				limpiarTabla();
				$("#factura_id").val(ui.item.idFactura);
				$("#buscarFactura").val(ui.item.numero);
				$("#nombreCliente").val(ui.item.clienteNombre);
				$("#idCedula").val(ui.item.clienteCedula);
				$("#factura_fecha").val(ui.item.fechaFactura);
				$("#IdCliente").val(ui.item.clienteID);
				$("#factura_serie").val(ui.item.facserie);
				$("#factura_numero").val(numero(ui.item.facsecuencial)); 
				$("#factura_tipo_pago").val(ui.item.formaPago);
				$("#tipoDocumento").val(ui.item.tipo);
				if(Number(ui.item.saldo) <= 0){
					document.getElementById("guardarID").disabled = true;
				}else{
					document.getElementById("guardarID").disabled = false;
				} 
				$("#saldo_factura").val(Number(ui.item.saldo).toFixed(2)); 
				document.getElementById("tablaResumenId").classList.add('invisible');
				document.getElementById("tablaDetalleId").classList.add('invisible');
				cargarAnticipos(ui.item.clienteID);
				return false;
			}
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