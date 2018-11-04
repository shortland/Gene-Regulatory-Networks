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
    <script type="text/javascript" src="vis/dist/vis.js"></script>
    <link href="vis/dist/vis-network.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
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
<script type="text/javascript">
    var color = 'gray';
    var len = undefined;

    var nodes = [{id: 0, label: "0", group: 0},
        {id: 1, label: "1", group: 0},
        {id: 2, label: "2", group: 0},
        {id: 3, label: "3", group: 1},
        {id: 4, label: "4", group: 1},
        {id: 5, label: "5", group: 1},
        {id: 6, label: "6", group: 2},
        {id: 7, label: "7", group: 2},
        {id: 8, label: "8", group: 2},
        {id: 9, label: "9", group: 3},
        {id: 10, label: "10", group: 3},
        {id: 11, label: "11", group: 3},
        {id: 12, label: "12", group: 4},
        {id: 13, label: "13", group: 4},
        {id: 14, label: "14", group: 4},
        {id: 15, label: "15", group: 5},
        {id: 16, label: "16", group: 5},
        {id: 17, label: "17", group: 5},
        {id: 18, label: "18", group: 6},
        {id: 19, label: "19", group: 6},
        {id: 20, label: "20", group: 6},
        {id: 21, label: "21", group: 7},
        {id: 22, label: "22", group: 7},
        {id: 23, label: "23", group: 7},
        {id: 24, label: "24", group: 8},
        {id: 25, label: "25", group: 8},
        {id: 26, label: "26", group: 8},
        {id: 27, label: "27", group: 9},
        {id: 28, label: "28", group: 9},
        {id: 29, label: "29", group: 9}
    ];
    var edges = [{from: 1, to: 0},
        {from: 2, to: 0},
        {from: 4, to: 3},
        {from: 5, to: 4},
        {from: 4, to: 0},
        {from: 7, to: 6},
        {from: 8, to: 7},
        {from: 7, to: 0},
        {from: 10, to: 9},
        {from: 11, to: 10},
        {from: 10, to: 4},
        {from: 13, to: 12},
        {from: 14, to: 13},
        {from: 13, to: 0},
        {from: 16, to: 15},
        {from: 17, to: 15},
        {from: 15, to: 10},
        {from: 19, to: 18},
        {from: 20, to: 19},
        {from: 19, to: 4},
        {from: 22, to: 21},
        {from: 23, to: 22},
        {from: 22, to: 13},
        {from: 25, to: 24},
        {from: 26, to: 25},
        {from: 25, to: 7},
        {from: 28, to: 27},
        {from: 29, to: 28},
        {from: 28, to: 0}
    ]

    // create a network
    var container = document.getElementById('mynetwork');
    var data = {
        nodes: nodes,
        edges: edges
    };
    var options = {
        nodes: {
            shape: 'dot',
            // size: 30,
            font: {
                size: 24,
                color: '#000'
            },
            borderWidth: 2
        },
        edges: {
            width: 2
        },
        interaction: {
            navigationButtons: true,
            keyboard: true,
            zoomView: false
        }
    };
    network = new vis.Network(container, data, options);
</script>
                <div id="vis" style="zoom:25%;"></div>
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
                <br><br>
            </div>
            <!-- end select-data-file view -->

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
    <script>
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

    <!--
        This must be the last "script" included...
    -->
    <!-- <script type="text/javascript" src="js/vis.js"></script> -->
</body>
</html>
