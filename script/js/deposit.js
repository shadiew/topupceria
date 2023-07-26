"use strict";

$(document).ready(function() {
	$("#method").change(function() {
		var paymeth = $("#method").val();
		$.ajax({
			url: '../deposit/note.php',
			data: 'paymeth=' + paymeth,
			type: 'POST',
			dataType: 'html',
			success: function(msg) {
				$("#note").html(msg);
			}
		});
	});
});