(function polling() {
	if (($("#api_stream_id").val() + "").length > 0) {
		var edgeList = [];
		$.get("http://138.197.50.244/network2/api/get_network_changes?networkId=" + $("#api_stream_id").val() + "&epoch=" + updateTimestamp, function(data) {
			$.each(data, function(index, value) {
				addNode(
					value['id'], 
					value['parent'], 
					"Node Id: " + value['id'] + "<br>Parent Id: " + value['parent']
				);
				edgeList.push({
					'edgeId': (Math.floor(Math.random() * 10000000) + 2000),
					'from' : value['id'],
					'to': value['next']
				});
			});
			if (edgeList.length > 0) {
				setTimeout(function() {
					addEdges(edgeList);
				}, 2000);
			}
			updateTimestamp = Math.floor(Date.now() / 1000);
		});
	}
	setTimeout(polling, 2000);
})();

function addEdges(edgeList) {
	console.log("adding edgelist data:", edgeList);
	$.each(edgeList, function(index, value) {
		addEdge(
			value['edgeId'], 
			value['from'], 
			value['to']
		);
	});
}