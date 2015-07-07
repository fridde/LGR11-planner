$(document).ready(function() {
	
	$("#colName").change(function(){
		
		var colName = $("#colName").val();
		
		$.ajax({
			data: '',
			type: 'POST',
			url: 'update.php?type=changeName' +  '&c=' + $("#colCode").val() + '&col_name=' + colName
		});
	});
	
	$("#contentlist").sortable({
		axis: 'y',
		update: function (event, ui) {
			if($("#codeGiven").length){
				var data = $(this).sortable('serialize');
				
				// POST to server using $.post or $.ajax
				$.ajax({
					data: data,
					type: 'POST',
					url: 'update.php?type=changeOrder' +  '&c=' + $("#colCode").val()
					
				});
			}
		}
	});	
	
});
