
    (function() {
        $("#buscarProducto").autocomplete({
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
                    url: contextPath+"/analisis/searchN",
                    dataType: "json",
                    async: false,
                    type: "POST",
                    data: {
                        buscar: request.term
                       
                    },
                    success: function(data){
                        response($.map(data, function(producto){
                            return {
                                nombre: producto.producto_nombre,
								label: producto.producto_nombre,
								codigo: producto.producto_codigo,
								id:producto.producto_id
                            };
                        }));
                    },
                });
            },
            select: function(event, ui){
                $("#codigoProducto").val(ui.item.codigo);
					$("#idProductoID").val(ui.item.id);
					$("#buscarProducto").val(ui.item.nombre)
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
                        url: contextPath+"/procedimiento/searchN",
                        dataType: "json",
                        async: false,
                        type: "POST",
                        data: {
                            buscar: ui.item.id,
                            Aseguradora : document.getElementById("idAseguradora").value,
                            especialidad : document.getElementById("idespecialidad").value,
                            entidad : document.getElementById("identidad").value,
                        },                      
                        success: function(data){
                           
                            $("#codigoProducto").val(' ');
                            $("#id_Precio").val('0.00');
                            $("#id_por_Cober").val('0.00');
                            $("#id_Cobertura").val('0.00');
                            if(data[0]){    
                                $("#codigoProducto").val(data[0].procedimientoA_codigo);
                                $("#id_Precio").val(Number(data[0].procedimientoA_valor).toFixed(2));
                            }
                           
                            if(data[1]){
                                $("#id_por_Cober").val( Number(data[1].ep_valor).toFixed(2));
                                $("#id_Cobertura").val(Number((data[0].procedimientoA_valor*data[1].ep_valor)/100).toFixed(2));
                            }
                          
                            calculatotales();
                        },
                        error: function(data) { 
                            console.log(data);       
                        },
                    });    
                return false;
            }
        });
    })();
 