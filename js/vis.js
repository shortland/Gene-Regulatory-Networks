/*
 * decaffeinate suggestions:
 * DS102: Remove unnecessary code created because of implicit returns
 * DS207: Consider shorter variations of null checks
 * DS208: Avoid top-level this
 * Full docs: https://github.com/decaffeinate/decaffeinate/blob/master/docs/suggestions.md
 */

const root = typeof exports !== 'undefined' && exports !== null ? exports : this;

// Help with the placement of nodes
const RadialPlacement = function() {
  // stores the key -> location values
  let values = d3.map();
  // how much to separate each location by
  let increment = 20;
  // how large to make the layout
  let radius = 200;
  // where the center of the layout should be
  let center = {"x":0, "y":0};
  // what angle to start at
  let start = -120;
  let current = start;

  // Given an center point, angle, and radius length,
  // return a radial position for that angle
  const radialLocation = function(center, angle, radius) {
    const x = (center.x + (radius * Math.cos((angle * Math.PI) / 180)));
    const y = (center.y + (radius * Math.sin((angle * Math.PI) / 180)));
    return {"x":x,"y":y};
  };

  // Main entry point for RadialPlacement
  // Returns location for a particular key,
  // creating a new location if necessary.
  const placement = function(key) {
    let value = values.get(key);
    if (!values.has(key)) {
      value = place(key);
    }
    return value;
  };

  // Gets a new location for input key
  var place = function(key) {
    const value = radialLocation(center, current, radius);
    values.set(key,value);
    current += increment;
    return value;
  };

   // Given a set of keys, perform some 
  // magic to create a two ringed radial layout.
  // Expects radius, increment, and center to be set.
  // If there are a small number of keys, just make
  // one circle.
  const setKeys = function(keys) {
    // start with an empty values
    values = d3.map();
  
    // number of keys to go in first circle
    const firstCircleCount = 360 / increment;

    // if we don't have enough keys, modify increment
    // so that they all fit in one circle
    if (keys.length < firstCircleCount) {
      increment = 360 / keys.length;
    }

    // set locations for inner circle
    const firstCircleKeys = keys.slice(0,firstCircleCount);
    firstCircleKeys.forEach(k => place(k));

    // set locations for outer circle
    const secondCircleKeys = keys.slice(firstCircleCount);

    // setup outer circle
    radius = radius + (radius / 1.8);
    increment = 360 / secondCircleKeys.length;

    return secondCircleKeys.forEach(k => place(k));
  };

  placement.keys = function(_) {
    if (!arguments.length) {
      return d3.keys(values);
    }
    setKeys(_);
    return placement;
  };

  placement.center = function(_) {
    if (!arguments.length) {
      return center;
    }
    center = _;
    return placement;
  };

  placement.radius = function(_) {
    if (!arguments.length) {
      return radius;
    }
    radius = _;
    return placement;
  };

  placement.start = function(_) {
    if (!arguments.length) {
      return start;
    }
    start = _;
    current = start;
    return placement;
  };

  placement.increment = function(_) {
    if (!arguments.length) {
      return increment;
    }
    increment = _;
    return placement;
  };

  return placement;
};

const Network = function() {
  // variables we want to access
  // in multiple places of Network
  const width = 4000;
  const height = 3000;
  // allData will store the unfiltered data
  let allData = [];
  let curLinksData = [];
  let curNodesData = [];
  const linkedByIndex = {};
  // these will hold the svg groups for
  // accessing the nodes and links display
  let nodesG = null;
  let linksG = null;
  // these will point to the circles and lines
  // of the nodes and links
  let node = null;
  let link = null;
  // variables to refect the current settings
  // of the visualization
  let layout = "force";
  let filter = "all";
  let sort = "songs";
  // groupCenters will store our radial layout for
  // the group by artist layout.
  let groupCenters = null;

  // our force directed layout
  const force = d3.layout.force();
  // color function used to color nodes
  const nodeColors = d3.scale.category20();
  // tooltip used to display details
  const tooltip = Tooltip("vis-tooltip", 230);

  // charge used in artist layout
  const charge = node => -Math.pow(node.radius, 2.0) / 2;

  // Starting point for network visualization
  // Initializes visualization and starts force layout
  const network = function(selection, data) {
    // format our data
    allData = setupData(data);

    // create our svg and groups
    const vis = d3.select(selection).append("svg")
      .attr("width", width)
      .attr("height", height);
    linksG = vis.append("g").attr("id", "links");
    nodesG = vis.append("g").attr("id", "nodes");

    // setup the size of the force environment
    force.size([width, height]);

    setLayout("force");
    setFilter("all");

    // perform rendering and start force layout
    return update();
  };

  // The update() function performs the bulk of the
  // work to setup our visualization based on the
  // current layout/sort/filter.
  //
  // update() is called everytime a parameter changes
  // and the network needs to be reset.
  var update = function() {
    // filter data to show based on current filter settings.
    curNodesData = filterNodes(allData.nodes);
    curLinksData = filterLinks(allData.links, curNodesData);

    // sort nodes based on current sort and update centers for
    // radial layout
    if (layout === "radial") {
      const artists = sortedArtists(curNodesData, curLinksData);
      updateCenters(artists);
    }

    // reset nodes in force layout
    force.nodes(curNodesData);

    // enter / exit for nodes
    updateNodes();

    // always show links in force layout
    if (layout === "force") {
      force.links(curLinksData);
      updateLinks();
    } else {
      // reset links so they do not interfere with
      // other layouts. updateLinks() will be called when
      // force is done animating.
      force.links([]);
      // if present, remove them from svg 
      if (link) {
        link.data([]).exit().remove();
        link = null;
      }
    }

    // start me up!
    return force.start();
  };

  // Public function to switch between layouts
  network.toggleLayout = function(newLayout) {
    force.stop();
    setLayout(newLayout);
    return update();
  };

  // Public function to switch between filter options
  network.toggleFilter = function(newFilter) {
    force.stop();
    setFilter(newFilter);
    return update();
  };

  // Public function to switch between sort options
  network.toggleSort = function(newSort) {
    force.stop();
    setSort(newSort);
    return update();
  };

  // Public function to update highlighted nodes
  // from search
  network.updateSearch = function(searchTerm) {
    const searchRegEx = new RegExp(searchTerm.toLowerCase());
    return node.each(function(d) {
      const element = d3.select(this);
      const match = d.name.toLowerCase().search(searchRegEx);
      if ((searchTerm.length > 0) && (match >= 0)) {
        element.style("fill", "#F38630")
          .style("stroke-width", 2.0)
          .style("stroke", "#555");
        return d.searched = true;
      } else {
        d.searched = false;
        return element.style("fill", d => nodeColors(d.origin))
          .style("stroke-width", 1.0);
      }
    });
  };

  network.updateData = function(newData) {
    allData = setupData(newData);
    link.remove();
    node.remove();
    return update();
  };

  // called once to clean up raw data and switch links to
  // point to node instances
  // Returns modified data
  var setupData = function(data) {
    // initialize circle radius scale
    const countExtent = d3.extent(data.nodes, d => d.playcount);
    const circleRadius = d3.scale.sqrt().range([10, 12]).domain(countExtent);
    // was
    // d3.scale.sqrt().range([3, 12]).domain(countExtent)

    data.nodes.forEach(function(n) {
      // set initial x/y to values within the width/height
      // of the visualization
      let randomnumber;
      n.x = (randomnumber=Math.floor(Math.random()*width));
      n.y = (randomnumber=Math.floor(Math.random()*height));
      // add radius to the node so we can use it later
      return n.radius = circleRadius(n.playcount);
    });

    // id's -> node objects
    const nodesMap  = mapNodes(data.nodes);

    // switch links to point to node objects instead of id's
    data.links.forEach(function(l) {
      l.source = nodesMap.get(l.source);
      l.target = nodesMap.get(l.target);

      // linkedByIndex is used for link sorting
      return linkedByIndex[`${l.source.id},${l.target.id}`] = 1;
    });

    return data;
  };

  // Helper function to map node id's to node objects.
  // Returns d3.map of ids -> nodes
  var mapNodes = function(nodes) {
    const nodesMap = d3.map();
    nodes.forEach(n => nodesMap.set(n.id, n));
    return nodesMap;
  };

  // Helper function that returns an associative array
  // with counts of unique attr in nodes
  // attr is value stored in node, like 'artist'
  const nodeCounts = function(nodes, attr) {
    const counts = {};
    nodes.forEach(function(d) {
      if (counts[d[attr]] == null) { counts[d[attr]] = 0; }
      return counts[d[attr]] += 1;
    });
    return counts;
  };

  // Given two nodes a and b, returns true if
  // there is a link between them.
  // Uses linkedByIndex initialized in setupData
  const neighboring = (a, b) =>
    linkedByIndex[a.id + "," + b.id] ||
      linkedByIndex[b.id + "," + a.id]
  ;

  // Removes nodes from input array
  // based on current filter setting.
  // Returns array of nodes
  var filterNodes = function(allNodes) {
    let filteredNodes = allNodes;
    if ((filter === "popular") || (filter === "obscure")) {
      const playcounts = allNodes.map(d => d.playcount).sort(d3.ascending);
      const cutoff = d3.quantile(playcounts, 0.5);
      filteredNodes = allNodes.filter(function(n) {
        if (filter === "popular") {
          return n.playcount > cutoff;
        } else if (filter === "obscure") {
          return n.playcount <= cutoff;
        }
      });
    }

    return filteredNodes;
  };

  // Returns array of artists sorted based on
  // current sorting method.
  var sortedArtists = function(nodes,links) {
    let counts;
    let artists = [];
    if (sort === "links") {
      counts = {};
      links.forEach(function(l) {
        if (counts[l.source.artist] == null) { counts[l.source.artist] = 0; }
        counts[l.source.artist] += 1;
        if (counts[l.target.artist] == null) { counts[l.target.artist] = 0; }
        return counts[l.target.artist] += 1;
      });
      // add any missing artists that dont have any links
      nodes.forEach(n => counts[n.artist] != null ? counts[n.artist] : (counts[n.artist] = 0));

      // sort based on counts
      artists = d3.entries(counts).sort((a,b) => b.value - a.value);
      // get just names
      artists = artists.map(v => v.key);
    } else {
      // sort artists by song count
      counts = nodeCounts(nodes, "artist");
      artists = d3.entries(counts).sort((a,b) => b.value - a.value);
      artists = artists.map(v => v.key);
    }

    return artists;
  };

  var updateCenters = function(artists) {
    if (layout === "radial") {
      return groupCenters = RadialPlacement().center({"x":width/2, "y":(height / 2) - 100})
        .radius(300).increment(18).keys(artists);
    }
  };

  // Removes links from allLinks whose
  // source or target is not present in curNodes
  // Returns array of links
  var filterLinks = function(allLinks, curNodes) {
    curNodes = mapNodes(curNodes);
    return allLinks.filter(l => curNodes.get(l.source.id) && curNodes.get(l.target.id));
  };

  // enter/exit display for nodes
  var updateNodes = function() {
    node = nodesG.selectAll("circle.node")
      .data(curNodesData, d => d.id);

    node.enter().append("circle")
      .attr("class", "node")
      .attr("cx", d => d.x)
      .attr("cy", d => d.y)
      .attr("r", d => d.radius)
      .style("fill", d => nodeColors(d.origin))
      .style("stroke", d => strokeFor(d))
      .style("stroke-width", 0.8);

    node.on("mouseover", showDetails)
      .on("mouseout", hideDetails);

    return node.exit().remove();
  };

  // enter/exit display for links
  var updateLinks = function() {
    link = linksG.selectAll("line.link")
      .data(curLinksData, d => `${d.source.id}_${d.target.id}`);
    link.enter().append("line")
      .attr("class", "link")
      .attr("stroke", "#000")
      .attr("stroke-opacity", 0.8)
      .attr("x1", d => d.source.x)
      .attr("y1", d => d.source.y)
      .attr("x2", d => d.target.x)
      .attr("y2", d => d.target.y);

    return link.exit().remove();
  };

  // switches force to new layout parameters
  var setLayout = function(newLayout) {
    layout = newLayout;
    if (layout === "force") {
      return force.on("tick", forceTick)
        .charge(-200)
        .linkDistance(50);
    } else if (layout === "radial") {
      return force.on("tick", radialTick)
        .charge(charge);
    }
  };

  // switches filter option to new filter
  var setFilter = newFilter => filter = newFilter;

  // switches sort option to new sort
  var setSort = newSort => sort = newSort;

  // tick function for force directed layout
  var forceTick = function(e) {
    node
      .attr("cx", d => d.x)
      .attr("cy", d => d.y);

    return link
      .attr("x1", d => d.source.x)
      .attr("y1", d => d.source.y)
      .attr("x2", d => d.target.x)
      .attr("y2", d => d.target.y);
  };

  // tick function for radial layout
  var radialTick = function(e) {
    node.each(moveToRadialLayout(e.alpha));

    node
      .attr("cx", d => d.x)
      .attr("cy", d => d.y);

    if (e.alpha < 0.03) {
      force.stop();
      return updateLinks();
    }
  };

  // Adjusts x/y for each node to
  // push them towards appropriate location.
  // Uses alpha to dampen effect over time.
  var moveToRadialLayout = function(alpha) {
    const k = alpha * 0.1;
    return function(d) {
      const centerNode = groupCenters(d.origin);
      d.x += (centerNode.x - d.x) * k;
      return d.y += (centerNode.y - d.y) * k;
    };
  };


  // Helper function that returns stroke color for
  // particular node.
  var strokeFor = d => d3.rgb(nodeColors(d.origin)).darker().toString();

  // Mouseover tooltip function
  var showDetails = function(d,i) {
    let content = `<p class="main">${d.name}</span></p>`;
    content += '<hr class="tooltip-hr">';
    content += `<p class="main">origin: ${d.origin}</span></p>`;
    tooltip.showTooltip(content,d3.event);

    // higlight connected links
    if (link) {
      link.attr("stroke", function(l) {
        if ((l.source === d) || (l.target === d)) { return "#555"; } else { return "#000"; }
      })
        .attr("stroke-opacity", function(l) {
          if ((l.source === d) || (l.target === d)) { return 1.0; } else { return 0.5; }
        });
    }

      // link.each (l) ->
      //   if l.source == d or l.target == d
      //     d3.select(this).attr("stroke", "#555")

    // highlight neighboring nodes
    // watch out - don't mess with node if search is currently matching
    node.style("stroke", function(n) {
      if (n.searched || neighboring(d, n)) { return "#555"; } else { return strokeFor(n); }
  })
      .style("stroke-width", function(n) {
        if (n.searched || neighboring(d, n)) { return 2.0; } else { return 1.0; }
    });
  
    // highlight the node being moused over
    return d3.select(this).style("stroke","black")
      .style("stroke-width", 12.0);
  };

  // Mouseout function
  var hideDetails = function(d,i) {
    tooltip.hideTooltip();
    // watch out - don't mess with node if search is currently matching
    node.style("stroke", function(n) { if (!n.searched) { return strokeFor(n); } else { return "#555"; } })
      .style("stroke-width", function(n) { if (!n.searched) { return 1.0; } else { return 2.0; } });
    if (link) {
      return link.attr("stroke", "#000")
        .attr("stroke-opacity", 0.8);
    }
  };

  // Final act of Network() function is to return the inner 'network()' function.
  return network;
};

// Activate selector button
const activate = function(group, link) {
  d3.selectAll(`#${group} a`).classed("active", false);
  return d3.select(`#${group} #${link}`).classed("active", true);
};

$(function() {
  const myNetwork = Network();

  d3.selectAll("#layouts a").on("click", function(d) {
    const newLayout = d3.select(this).attr("id");
    activate("layouts", newLayout);
    return myNetwork.toggleLayout(newLayout);
  });

  d3.selectAll("#filters a").on("click", function(d) {
    const newFilter = d3.select(this).attr("id");
    activate("filters", newFilter);
    return myNetwork.toggleFilter(newFilter);
  });

  d3.selectAll("#sorts a").on("click", function(d) {
    const newSort = d3.select(this).attr("id");
    activate("sorts", newSort);
    return myNetwork.toggleSort(newSort);
  });

//  $("#modal_save_button").on "click", (e) ->
//    if $("#exampleModalLabel").val() == "Select Data File" 
//      songFile = $("#song_select").val()
//      d3.json "server/php/files/#{songFile}", (json) ->
//        myNetwork.updateData(json)

  $("#song_select").on("change", function(e) {
    const songFile = $(this).val();
    return d3.json(`server/php/files/${songFile}`, json => myNetwork.updateData(json));
  });
  
  $("#search").keyup(function() {
    const searchTerm = $(this).val();
    return myNetwork.updateSearch(searchTerm);
  });

  return d3.json("server/php/files/default_data.csv.json", json => myNetwork("#vis", json));
});
