$("#controls_toggle_upload").click(function() {
	$("#exampleModalLabel").html("Network File Upload");
	$(".modal_items").hide();
	if (!$("#view_upload_file").is(":visible")) {
		$("#view_upload_file").show();
	}
});

$("#controls_toggle_select").click(function() {
	$("#exampleModalLabel").html("Network Data File Selection");
	$.get("data_list.php", function(data) {
		$("#song_select").html(data);
	}).done(function() {
		$(".modal_items").hide();
		if (!$("#view_data_selection").is(":visible")) {
			$("#view_data_selection").show();
		}
	});
});

$("#controls_toggle_options").click(function() {
	$("#exampleModalLabel").html("Network Display Options");
	$(".modal_items").hide();
	if (!$("#view_display_selection").is(":visible")) {
		$("#view_display_selection").show();
	}
});

$("#controls_toggle_api").click(function() {
	$("#exampleModalLabel").html("API Stream");
	$(".modal_items").hide();
	if (!$("#view_stream_selection").is(":visible")) {
		$("#view_stream_selection").show();
	}
});

$("#radial").click(function() {
	alert("Not yet implemented.");
});