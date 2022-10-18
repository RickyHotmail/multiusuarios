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
				url: contextPath+"/facturaCompraAnt/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
                    proveedor_id : $("#proveedor_id").val(),
				},
				success: function(data){
					if(data[0].length > 0){
						response($.map(data[0], function(factura){
							return {
								idFactura: factura.transaccion_id, 
								numero: factura.transaccion_numero,
								valorFactura: factura.transaccion_total,
								fechaFactura: factura.transaccion_fecha,
								ivaFactura: factura.transaccion_porcentaje_iva,
								label: factura.transaccion_numero,
								proveedorNombre: factura.proveedor_nombre,
								proveedorRuc: factura.proveedor_ruc,
								proveedorID: factura.proveedor_id,
								formaPago: factura.transaccion_tipo_pago,
								facserie: factura.transaccion_serie,                            
								facsecuencial: factura.transaccion_secuencial,
								saldo: factura.cuenta_saldo,
								tipo: 'factura',
							};
						}));
					}else{
						response($.map(data[1], function(cxp){
							return {
								idFactura: cxp.cuenta_id, 
								numero: cxp.cuenta_descripcion.substring(39),
								valorFactura: cxp.cuenta_valor_factura,
								fechaFactura: cxp.cuenta_fecha,
								label: cxp.cuenta_descripcion.substring(39),
								proveedorNombre: cxp.proveedor_nombre,
								proveedorRuc: cxp.proveedor_ruc,
								proveedorID: cxp.proveedor_id,
								formaPago: 'CREDITO',
								facserie: cxp.cuenta_descripcion.substring(39,45),           
								facsecuencial: cxp.cuenta_descripcion.substring(45),
								saldo: cxp.cuenta_saldo,
								tipo: 'cxp',
							};
						}));
					}
				},
				error: function(data){
					console.log(data);
				},
			});
		},
		select: function(event, ui){
			if(ui.item.tipo == 'factura'){
				$("#factura_id").val(ui.item.idFactura);
				$("#buscarFactura").val(ui.item.numero);
				$("#nombreProveedor").val(ui.item.proveedorNombre);
				$("#idCedula").val(ui.item.proveedorRuc);
				$("#factura_fecha").val(ui.item.fechaFactura);
				$("#idTarifaIva").val(ui.item.ivaFactura);
				$("#IdProveedor").val(ui.item.proveedorID);
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
				cargarAnticipos(ui.item.proveedorID);
				return false;
			}else{
				limpiarTabla();
				$("#factura_id").val(ui.item.idFactura);
				$("#buscarFactura").val(ui.item.numero);
				$("#nombreProveedor").val(ui.item.proveedorNombre);
				$("#idCedula").val(ui.item.proveedorRuc);
				$("#factura_fecha").val(ui.item.fechaFactura);
				$("#IdProveedor").val(ui.item.proveedorID);
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
				cargarAnticipos(ui.item.proveedorID);
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