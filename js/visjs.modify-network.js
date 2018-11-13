function toJSON(obj) {
    return JSON.stringify(obj, null, 4);
}

function addNode(nodeId, nodeGroup, nodeTitle = '') {
    console.log('adding node of id: ' + nodeId + ' title: ' + nodeTitle);
    try {
        nodes.add({
            id: "" + nodeId + "",
            title: nodeTitle,
            group: nodeGroup
        });
    }
    catch (err) {
        console.log(err);
    }
}

function updateNode(nodeId, nodeTitle = '') {
    try {
        nodes.update({
            id: nodeId,
            title: nodeTitle
        });
    }
    catch (err) {
        console.log(err);
    }
}

function removeNode(nodeId) {
    try {
        nodes.remove({
            id: nodeId
        });
    }
    catch (err) {
        console.log(err);
    }
}

function addEdge(edgeId = (Math.floor(Math.random() * 1000000) + 2000), edgeFrom, edgeTo) {
    try {
        edges.add({
            id: edgeId,
            from: edgeFrom,
            to: edgeTo
        });
    }
    catch (err) {
        console.log(err);
    }
}

function updateEdge(edgeId = (Math.floor(Math.random() * 1000000) + 2000), edgeFrom, edgeTo) {
    try {
        edges.update({
            id: edgeId,
            from: edgeFrom,
            to: edgeTo
        });
    }
    catch (err) {
        console.log(err);
    }
}

function removeEdge(edgeId) {
    try {
        edges.remove({
            id: edgeId
        });
    }
    catch (err) {
        console.log(err);
    }
}

$("#search_node_btn").click(function() {
    var newColor = '#' + Math.floor((Math.random() * 255 * 255 * 255)).toString(16);
    try {
        // oldnode = nodes.get($("#node_search_field").val());
        // nodes.remove($("#node_search_field").val());
        // nodes.add(oldnode);
        // console.log(nodes);

        //alert(nodes['0']['group']);
        //alert(nodes[$("#node_search_field").val()]['group']);
        // Get previous color
        // nodes.update({
        //     id: $("#node_search_field").val(), 
        //     group: Math.floor((Math.random() * 255 * 255 * 255))
        // });
        // don't change color - instead - enlarge the node border?.?
    }
    catch (err) {
        console.log(err);
    }
    var options = {
        // position: {x:positionx,y:positiony}, // this is not relevant when focusing on nodes
        scale: 1.0,
        offset: {x:0,y:0},
        animation: {
          duration: 1000,
          easingFunction: 'easeInOutQuad'
        }
      };
    network.focus($("#node_search_field").val(), options);
});