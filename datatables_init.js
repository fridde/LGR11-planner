$(document).ready(function() {
    var table1 = $('#chosen').DataTable({
		dom: 'T<"clear">lfrtip',
		tableTools: {
            "sSwfPath": "/lgr11/inc/copy_csv_xls_pdf.swf"
        },
        paging: false,
        "order": [1, 'asc'],
		responsive: true,
		/* "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.7/i18n/Swedish.json"
           }
		*/ 
	});
	if ( $("#chosen").length ) {
 
   new $.fn.dataTable.FixedHeader(table1);
}
	
	
	var table2 = $('#notChosen').DataTable({
        paging: false,
        "order": [1, 'asc'],
		responsive: true,
		/*"language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.7/i18n/Swedish.json"
            }
		*/
	});
	
	if ( $( "#notChosen" ).length ) {
		
   new $.fn.dataTable.FixedHeader(table2); 
}
	
	

});


