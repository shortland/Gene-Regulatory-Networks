<?php
    error_reporting(E_ALL); 
    ini_set('display_errors', 1);

    $dir = "server/php/files/";
    $list = scandir($dir, 1);

    $newlist = [];
    foreach ($list as $name) {
        if (strpos($name, '.csv.json') !== false) {
            $newlist[] = $dir . $name;
        }
    }

    usort($newlist, function($a, $b) {
        return filemtime($a) < filemtime($b);
    });

    var_dump($newlist);

    $i = 1;
    foreach ($newlist as $name) {
        $modified = date ("F d, H:i", filemtime($name));
        $realPath = $name;
        $name = preg_replace('/\.csv.json$/i', '.json', $name);
        $nameClean = preg_replace('/server\/php\/files\//i', '', $name);
        $realPath = preg_replace('/server\/php\/files\//i', '', $realPath);
        $extra = "";
        if ($i == sizeof($newlist)) {
            $extra = ' selected="selected"';
        }
        echo '<option value="' . $realPath . '"' . $extra . '>' . $nameClean . ' [' . $modified . ']</option>';
        $i++;
    }
?>