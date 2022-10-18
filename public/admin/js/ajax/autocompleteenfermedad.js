(function() {
	$("#select2").autocomplete({
		source: function(request, response){
			var localObj = window.location;
			var contextPath = localObj.pathname.split("/")[1];
			if(contextPath=='public'){
				contextPath="/"+contextPath;
			}else{
				contextPath='';
			}
			$.ajax({
				url: contextPath+'/enfermedad/searchN',
				dataType: "json",
				type: "GET",
				data: {
					buscar: request.term
				},
				success: function(data){
					response($.map(data, function(cliente){
						return {
							nombre: cliente.enfermedad_id,
							id: cliente.enfermedad_nombre,
						};
					}));
				},
			});
		},
		select: function(event, ui){
			alert('asd');
			
			return false;
		}	

	});
})();
