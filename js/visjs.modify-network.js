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
        alert(err);
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
        alert(err);
    }
}

function removeNode(nodeId) {
    try {
        nodes.remove({
            id: nodeId
        });
    }
    catch (err) {
        alert(err);
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
        alert(err);
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
        alert(err);
    }
}

function removeEdge(edgeId) {
    try {
        edges.remove({
            id: edgeId
        });
    }
    catch (err) {
        alert(err);
    }
}