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
	$.get("data_list.php", function(data) {
		$("#song_select2").html(data);
	});
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

$("input:text").focus(function() { $(this).select(); } );

$("#transition_go").click(function() {
	/**
	 * get the differences between current networkId and the SELECTED ONE IN [] of classes transition_selection[]
	 */
	$.get(api_base_url + "/network_diffs", {
        'token': api_public_token,
        'oldNetworkId': network_id,
        'newNetworkId': Base64.encode(encodeURI($("#song_select2").val()))
    })
    .done(function(json) {
		$.each(json, function(index, value) {
			var nodeId = value[0];
			console.log(nodeId);
			//console.log(edges.get());
			$.each(edges.get(), function(index2, value2) {
				if (nodeId == index2) {
					console.log(value2['id']);
					edges.remove({
						id: value2['id']
					});
					edges.add({
						id: value2['id'],
						from: value[0],
						to: value[1]
					});
					return false;
				}
				// if index2 === nodeId, get the value[2][edgeId]
				// then remove that edge
				// then add the new edge
				//
				//
			});
			//alert(edges.get());
		});
    });


});