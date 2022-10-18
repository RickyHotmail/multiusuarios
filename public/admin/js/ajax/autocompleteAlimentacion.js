(function() {
	$("#buscartransaccion").autocomplete({
		source: function(request, response){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
			document.getElementById("guardarID").disabled = true;
			var localObj = window.location;
    		var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+"/transaccioncompra/searchN",
				dataType: "json",
				type: "POST",
				data: {
					buscar: request.term,
					proveedor: document.getElementById("proveedor_id").value,
				},
				success: function(data){
					response($.map(data, function(transaccion){
						return {
                            idtransaccion: transaccion.transaccion_id, 
							numero: transaccion.transaccion_numero,
							label: transaccion.transaccion_numero,
							fechatransaccion: transaccion.transaccion_fecha,
							proveedorNombre: transaccion.proveedor_nombre,
                            proveedorCedula: transaccion.proveedor_ruc,
							valortransaccion: transaccion.transaccion_total,
							proveedorID: transaccion.proveedor_id,
							facserie: transaccion.transaccion_serie, 
							facsecuencial: transaccion.transaccion_secuencial,
						};
					}));
				},
			});
		},
		select: function(event, ui){
		
			if(Number(ui.item.valortransaccion) <= 0){
				document.getElementById("guardarID").disabled = true;	
			}else{
				
				document.getElementById("guardarID").disabled = false;
				$('#tablaalimentacion .editable').empty();
				$("#transaccion_id").val(ui.item.idtransaccion);
				$("#buscartransaccion").val(ui.item.numero);
				
				document.getElementById("ltransaccion_fecha").innerHTML =(ui.item.fechatransaccion);
				$("#transaccion_fecha").val(ui.item.fechatransaccion);		
				$("#saldo_transaccion").val(Number(ui.item.valortransaccion).toFixed(2)); 
				$("#tvfactura").val(ui.item.valortransaccion);
				document.getElementById("tfactura").innerHTML = Number(ui.item.valortransaccion).toFixed(2);
				cargarempleado(ui.item.idtransaccion);
			}  
			
			
			
			
			return false;
		}
	});
})();
