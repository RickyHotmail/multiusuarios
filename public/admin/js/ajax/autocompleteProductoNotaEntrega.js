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
                        return {
                            nombre: producto.producto_nombre,
                            label: producto.producto_nombre,
                            codigo: producto.producto_codigo,
                            precio : producto.producto_precio1,
                            id: producto.producto_id,
                            stock : producto.producto_stock,
                            tipo: producto.producto_tipo,
                            cv: producto.producto_compra_venta,
                            empresa: producto.empresa_estado_cambiar_precio
                        };
                    }));
                },
            });
        },
        select: function(event, ui){
           
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
                $("#id_pu").val( Number(ui.item.precio).toFixed(2));
                $("#buscarProducto").val(ui.item.nombre);
                $("#descripcionProducto").val(ui.item.nombre);
                $("#idtipoProducto").val(ui.item.tipo);
                $("#id_disponible").val(ui.item.stock);
                $("#idCV").val(ui.item.cv);
                if(ui.item.tipo == '2'){
                    $("#id_disponible").val(0);
                }
                
                calcularTotal();
        }
    });
})();