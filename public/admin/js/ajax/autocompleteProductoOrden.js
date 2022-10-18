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
                    bodega: $("#idBodega").val()
                },
                success: function(data){
                    response($.map(data, function(producto){
                        return {
                            nombre: producto.producto_nombre,
                            label: producto.producto_nombre,
                            precio : producto.producto_precio1,
                            tieneIva : producto.producto_tiene_iva,
                            stock : producto.producto_stock,
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
                $("#id_disponible").val(ui.item.stock);
                $("#id_pu").val( Number(ui.item.precio).toFixed(2));

           
            return false;
        }
    });
})();
