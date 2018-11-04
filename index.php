<!doctype html>
<html>
<head>
    <title>Gene Regulatory Network State Space</title>
    <style>
      #mynetwork {
          width: 100%;
          height: calc(100vh - 50px);
          background-color: #fff;
      }
    </style>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- CSS concatenated and minified via ant build script-->
    <link rel="stylesheet" href="css/reset.css">
    <!-- Bootstrap styles -->
    <link rel="stylesheet" href="css/modified_bootstrap.css">
    <!-- Generic page styles -->
    <link rel="stylesheet" href="css/style.css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="css/jquery.fileupload.css">
    <!-- end CSS-->
    <script src="js/libs/modernizr-2.0.6.min.js"></script>
    <script type="text/javascript" src="js/visjs.loadutil.js"></script>
    <script type="text/javascript" src="vis/dist/vis.js"></script>
    <link href="vis/dist/vis-network.min.css" rel="stylesheet" type="text/css"/>
</head>
<body onLoad="load_new_network('attractor.csv.json')">
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-fixed-top .navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><b>Gene Regulatory Network State Space</b></a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="#upload" id="controls_toggle_upload" data-toggle="modal" data-target="#controlsModal">Upload</a></li>
                    <li><a href="#select" id="controls_toggle_select" data-toggle="modal" data-target="#controlsModal">Select Data File</a></li>
                    <li><a href="#options" id="controls_toggle_api" data-toggle="modal" data-target="#controlsModal">API Stream</a></li>
                    <li><a href="#options" id="controls_toggle_options" data-toggle="modal" data-target="#controlsModal">Display Options</a></li>
                    <li><a target="_blank" href="https://github.com/rAntonioh/D3_python_attractor_networks">Source Code</a></li>

                </ul>
<!--                 <ul class="nav navbar-nav">
                    <li>Current: demo_data.json</li>
                </ul> -->
            </div>
        </div>
    </div>
    <div id="container" class="container">
        <!-- 
        <header>
          <h1>&nbsp;Attractor Network</h1>
        </header> 
        -->
        <!--
          Nodes are sized based on "playcounts"
          Nodes are colored by "artist"
        -->

        <div id="controls">

    <!--
          <div id="filters" class="control">
            <h3>Filter</h3>
            <a id="all" class="active">All</a>
            <a id="popular">Popular</a>
            <a id="obscure">Obscure</a>
          </div>
          <div id="sorts" class="control">
            <h3>Sort</h3>
            <a id="songs" class="active">Songs</a>
            <a id="links">Links</a>
          </div>
    -->
    <!--
          <div id="search_section" class="control">
            <form id="search_form" action=""  method="post">
              <p class="search_title">Search <input type="text" class="text-input" id="search" value="" /></p>
            </form>
          </div>
    -->
            <div id="main" role="main">
                <div id="mynetwork"></div>
<!--<script id="dynamic_data" type="application/json" src="server/php/files/attractor (4).csv.json"></script>-->
<script type="text/javascript">
    var color = 'gray';
    var len = undefined;
    var network;
    var nodes = new vis.DataSet();
    nodes.on('*', function () {
        console.log("node event listener triggered");
    });
    var edges = new vis.DataSet();

    // create a network
    var container = document.getElementById('mynetwork');
    var data = {
        nodes: nodes,
        edges: edges
    };

    var options = {
        autoResize: true,
        nodes: {
            shape: 'dot',
            // size: 30,
            font: {
                size: 24,
                color: '#000'
            },
            borderWidth: 1
        },
        interaction: {
            navigationButtons: true,
            keyboard: true,
            zoomView: false
        },
        layout: {
          improvedLayout: true
        },
        physics: {
          stabilization: false,
          minVelocity: 1,
          maxVelocity: 20,
          timestep: 1
        },
        edges: {
          smooth: false,
          width: 4
        }
    };
    //network = new vis.Network(container, data, options);
</script>
                
            </div>
        </div> 
        <!-- end controls -->
    </div> 
    <!-- end container -->

    <!-- start modal -->
    <div class="modal fade" id="controlsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <center>
            <!-- start upload view -->
            <div id="view_upload_file" class="modal_items">
                <h3>Upload</h3>
                <p>Only .csv files are allowed</p>
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <!-- The file input field used as target for the file upload widget -->
                    <input id="fileupload" type="file" accept=".csv" name="files[]" multiple>
                </span>
                <br><br>
                <div id="files" class="files"></div>
            </div>
            <!-- end upload view -->

            <!-- start select-data-file view -->
            <div id="view_data_selection" class="modal_items">
                <h3>Select Data File</h3>
                <select class="form-control" id="song_select">
                    <!-- jquery ajax gets this, toolbar.js -->
                </select>
                <br>
                <button type="button" class="btn btn-success active" id="load_new_network">Load</button>
                <br>
            </div>
            <!-- end select-data-file view -->

            <!-- start data stream [view_stream_selection]-->
            <div id="view_stream_selection" class="modal_items">
                <h3><input type='text' id='api_stream_id' value='' disabled/></h3>
            </div>
            <!-- end data sream -->

            <!-- start view options view -->
            <div id="view_display_selection" class="modal_items">
                <h3>Display Options</h3>
                <button type="button" class="btn btn-success active" id="force">Loose</button>
                &nbsp;&nbsp;
                <button type="button" class="btn btn-warning" id="radial">Compact</button>
            </div>
            <!-- end view options view -->
            </center>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <!--
            <button id="modal_save_button" type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
            -->
          </div>
        </div>
      </div>
    </div>
    <!-- end modal -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="js/toolbar.js"></script>

    <!-- https://flowingdata.com/2012/08/02/how-to-make-an-interactive-network-visualization/ -->
    <script defer src="js/plugins.js"></script>
    <script defer src="js/script.js"></script>
    <!-- <script src="js/libs/coffee-script.js"></script> -->    
    <!-- <script src="js/libs/d3.v2.js"></script> -->
    <script src="js/Tooltip.js"></script>

    <!-- https://github.com/blueimp/jQuery-File-Upload -->
    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="js/vendor/jquery.ui.widget.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="js/jquery.iframe-transport.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="js/jquery.fileupload.js"></script>
    <!-- The File Upload processing plugin -->
    <script src="js/jquery.fileupload-process.js"></script>
    <!-- The File Upload image preview & resize plugin -->
    <script src="js/jquery.fileupload-image.js"></script>
    <!-- The File Upload audio preview plugin -->
    <script src="js/jquery.fileupload-audio.js"></script>
    <!-- The File Upload video preview plugin -->
    <script src="js/jquery.fileupload-video.js"></script>
    <!-- The File Upload validation plugin -->
    <script src="js/jquery.fileupload-validate.js"></script>
    <!-- base64 -->
    <script src="js/base64.js"></script>
    <!-- modify vis network functionality -->
    <script src="js/visjs.modify-network.js"></script>
    <script>
    var updateTimestamp = Math.floor(Date.now() / 1000);
    $("#load_new_network").click(function() {
      load_new_network($("#song_select").val());
    });

    function load_new_network(fileName) {
      nodes.clear();
      edges.clear();
      updateTimestamp = Math.floor(Date.now() / 1000);
      $.getJSON( "server/php/files/" + fileName, function( data ) {
        var network_id = Base64.encode(encodeURI(fileName));
        $("#api_stream_id").val(network_id);
        nodes.add(data['nodes']);
        edges.add(data['edges']);
        network.redraw();
        network.fit();
      });
      setTimeout(function(){network.fit()}, 15000);
      setTimeout(function(){network.fit()}, 20000);
      //setTimeout(function(){network.fit()}, 30000);
    }

    /**
    *   Part of jQuery-File-Upload,
    *   Below script must be after the inclusion of the necessary .js files for this lib
    */
    /*jslint unparam: true, regexp: true */
    /*global window, $ */
    $(function () {
        'use strict';
        // Change this to the location of your server-side upload handler:
        var url = window.location.hostname === '138.197.50.244' ?
                    'server/php/' : 'server/php/',
            uploadButton = $('<button/>')
                .addClass('btn btn-primary')
                .prop('disabled', true)
                .text('Processing...')
                .on('click', function () {
                    var $this = $(this),
                        data = $this.data();
                    $this
                        .off('click')
                        .text('Abort')
                        .on('click', function () {
                            $this.remove();
                            data.abort();
                        });
                    data.submit().always(function () {
                        $this.remove();
                    });
                });
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 999000,
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            previewMaxWidth: 100,
            previewMaxHeight: 100,
            previewCrop: true
        }).on('fileuploadadd', function (e, data) {
            data.context = $('<div/>').appendTo('#files');
            $.each(data.files, function (index, file) {
                var node = $('<p/>')
                        .append($('<span/>').text(file.name));
                if (!index) {
                    node
                        .append('<br>')
                        .append(uploadButton.clone(true).data(data));
                }
                node.appendTo(data.context);
            });
        }).on('fileuploadprocessalways', function (e, data) {
            var index = data.index,
                file = data.files[index],
                node = $(data.context.children()[index]);
            if (file.preview) {
                node
                    .prepend('<br>')
                    .prepend(file.preview);
            }
            if (file.error) {
                node
                    .append('<br>')
                    .append($('<span class="text-danger"/>').text(file.error));
            }
            if (index + 1 === data.files.length) {
                data.context.find('button')
                    .text('Upload')
                    .prop('disabled', !!data.files.error);
            }
        }).on('fileuploadprogressall', function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }).on('fileuploaddone', function (e, data) {
            $.each(data.result.files, function (index, file) {
                if (file.url) {
                    var link = $('<a>')
                        .attr('target', '_blank')
                        .prop('href', file.url);
                    $(data.context.children()[index])
                        .wrap(link);
                } else if (file.error) {
                    var error = $('<span class="text-danger"/>').text(file.error);
                    $(data.context.children()[index])
                        .append('<br>')
                        .append(error);
                }
            });
        }).on('fileuploadfail', function (e, data) {
            $.each(data.files, function (index) {
                var error = $('<span class="text-danger"/>').text('File upload failed.');
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            });
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });
    </script>
    <!-- polling, insert after load_new_network -->
    <script src="js/api.polling.js"></script>
    <!--
        This must be the last "script" included...
    -->
    <!-- <script type="text/javascript" src="js/vis.js"></script> -->
    <script>
        network = new vis.Network(container, data, options);
    </script>
</body>
</html>
