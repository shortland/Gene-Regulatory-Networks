(function polling() {
	if (($("#api_stream_id").val() + "").length > 0) {
		$.get("http://138.197.50.244/network2/api/get_network_changes?networkId=" + $("#api_stream_id").val() + "&epoch=" + updateTimestamp, function(data) {
			$.each(data, function( index, value ) {
				addNode(value['id'], value['parent'], "Node Id: " + value['id'] + "<br>Parent Id: " + value['parent']);
				addEdge((Math.floor(Math.random() * 1000000) + 2000), value['id'], value['next']);
			});

			updateTimestamp = Math.floor(Date.now() / 1000);
		});
		//network.fit();
	}

	setTimeout(polling, 5000);
})();