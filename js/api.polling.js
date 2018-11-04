(function polling() {
	$.get("http://138.197.50.244/network2/api", function() {

	});
	setTimeout(polling, 5000);
})();