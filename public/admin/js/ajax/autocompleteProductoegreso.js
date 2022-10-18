(function() {
    $("#buscarProducto").autocomplete({
        source: function(request, response){
            var localObj = window.location;
            var contextPath = localObj.pathname.split("/")[1];
            if(contextPath=='public'){
                contextPath="/"+contextPath;
            }else{
                contextPath='';
            }
            $.ajax({
                url: contextPath+"/producto/searchN/"+request.term,
                dataType: "json",
                type: "GET",
                data: {
                    buscar: request.term,
                },
                success: function(data){
                    response($.map(data, function(producto){
                        if(producto.producto_tipo == '1'){
                            return {
                                nombre: producto.producto_nombre,
                                label: producto.producto_nombre,
                                codigo: producto.producto_codigo,
                                precio : producto.producto_precio_costo,
                                id: producto.producto_id,
                                stock : producto.producto_stock,
                                cuenta: producto.producto_cuenta_gasto,
                                tipo: producto.producto_tipo
                            };
                        }
                    }));
                },
            });
        },
        select: function(event, ui){
            if(parseFloat(ui.item.stock) > 0){
                document.getElementById("buscarProducto").classList.remove('is-invalid');
				document.getElementById("errorStock").classList.add('invisible');
                $("#codigoProducto").val(ui.item.codigo);
                $("#idProductoID").val(ui.item.id);
                $("#id_pu").val( Number(ui.item.precio).toFixed(2));
                $("#id_disponible").val(ui.item.stock);
                $("#buscarProducto").val(ui.item.nombre);
                $("#descripcionProducto").val(ui.item.nombre);
                
                calcularTotal();
            }else{
                document.getElementById("buscarProducto").classList.add('is-invalid');
                document.getElementById("errorStock").classList.remove('invisible');
            }
            return false;
        }
    });
})();