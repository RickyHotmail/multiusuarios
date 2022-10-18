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
                url: contextPath+"/productocompra/searchN/"+request.term,
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
                            tieneIva : producto.producto_tiene_iva,
                            codigo: producto.producto_codigo,
                            id: producto.producto_id,
                            cuenta: producto.producto_cuenta_inventario,
                            tipo: producto.producto_tipo
                        };
                    }));
                },
            });
        },
        select: function(event, ui){
            $("#codigoProducto").val(ui.item.codigo);
            $("#idProductoID").val(ui.item.id);
            if(ui.item.tipo == '1'){
                $("#tipoProductoID").val("Bien");
            }else{
                $("#tipoProductoID").val("Servicio");
            }
            $("#buscarProducto").val(ui.item.nombre);
            $("#descripcionProducto").val(ui.item.nombre);
            if(ui.item.tieneIva == "1"){ 
                document.getElementById("tieneIva").checked = true;
            }else{
                document.getElementById("tieneIva").checked = false;
            }
            $("#cuentaProductoID > option[value="+ ui.item.cuenta +"]").attr("selected",true);
            $("#cuentaProductoID").select2().val(ui.item.cuenta).trigger("change");
            return false;
        }
    });
})();
