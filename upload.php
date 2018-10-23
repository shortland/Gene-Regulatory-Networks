<?php
    $target_dir = "data/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "A file with that name already exists. ";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "csv") {
        echo "Sorry, only .csv files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Error uploading the file. <a href='/network/'>Back</a>";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br>\n";
            echo "Attempting to convert .csv into .json format...<br>\n";
            $out = exec("python converter.py " . $target_dir . basename( $_FILES["fileToUpload"]["name"]));
            if (strpos($out, '1023 is source') !== false) {
                echo "Successfully converted file to JSON format. <a href='/network/'>Back</a>\n";
            }
            else {
                echo "Failed to convert file to JSON format. <a href='/network/'>Back</a>\n";
            }
        } else {
            echo "Sorry, there was an error uploading your file. <a href='/network/'>Back</a>";
        }
    }
?>