$("#controls_toggle_upload").click(function(){
	$("#exampleModalLabel").html("Upload Network Files");
	if (!$("#view_upload_file").is(":visible")) {
		$("#view_upload_file").show();
	}
	else {
		$(".modal_items").hide();
	}
});