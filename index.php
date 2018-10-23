<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Attractor Network</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- CSS concatenated and minified via ant build script-->
    <link rel="stylesheet" href="css/reset.css">
    <!-- Bootstrap styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Generic page styles -->
    <link rel="stylesheet" href="css/style1.css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="css/jquery.fileupload.css">
    <!-- end CSS-->
    <script src="js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body>

  <div id="container" class="container">
    <header>
      <h1>&nbsp;Attractor Network</h1>
    </header>

    <!--
      Nodes are sized based on "playcounts"
      Nodes are colored by "artist"
    -->

    <div id="song_selection" class="control">
      <h3>Upload</h3>
      <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload" name="submit">
      </form>

      <h3>Select Data File</h3>
      <select id="song_select">
        <?php

        $dir = "data/";
        $list = scandir($dir, 1);
        foreach ($list as $name) {
          if (strpos($name, '.csv.json') !== false) {
            $name = preg_replace('/\.csv.json$/i', '.json', $name);
            echo '<option value="' . $name . '">' . $name . '</option>';
          }
        }

        ?>
      </select>

    </div>

    <div id="controls">
      <div id="layouts" class="control">
        <h3>Display Options:</h3>
        <a id="force" class="active">Loose</a> <!-- Source to Target -->
        <a id="radial">Compact</a> <!-- Group Radially By "Artist" -->
      </div>
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
        <div id="vis" style="zoom:25%"></div>
      </div>
    </div> <!-- controls -->

  </div> <!-- container -->

  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js"><\/script>')</script>

  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>
  <script src="js/libs/coffee-script.js"></script>
  <script src="js/libs/d3.v2.js"></script>
  <script src="js/Tooltip.js"></script>
  <script type="text/coffeescript" src="coffee/vis.coffee"></script>
  
</body>
</html>
