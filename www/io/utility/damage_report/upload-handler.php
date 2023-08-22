<?php
session_start();


//set the folder name to session id and date
$folderName = $_SESSION['UserUserName']."_".date("Y-m-d");

//get dir of curretn file
$target_dir = dirname(__FILE__).'/../../uploads/images/'.$folderName;
//if folder does not exist, create one
if (!file_exists($target_dir)) {
  mkdir($target_dir, 0777, true);
}


//if uploadComplete is set
if(isset($_POST['uploadComplete'])){
    $files = scandir($target_dir);

    //zip files
    $zip = new ZipArchive();
    $zip->open($target_dir.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $zip->addFile($target_dir.'/'.$file, $file);
        }
    }
    $zip->close();

    //Delete folder
    array_map('unlink', glob("$target_dir/*.*"));
    rmdir($target_dir);
    echo $folderName;
    exit();
}


//rename file to current timestamp
$_FILES["upFile"]["name"] = time() . "_" .basename($_FILES["upFile"]["name"]);
$target_file = $target_dir . '/' . basename($_FILES["upFile"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset(($_FILES["upFile"]))) {
  $check = getimagesize($_FILES["upFile"]["tmp_name"]);
  if($check !== false) {
    //echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;

    // Check if file already exists
    if (file_exists($target_file)) {
      echo "Sorry, file already exists.";
      $uploadOk = 0;
    }else{
        //Write file to target file
        if (move_uploaded_file($_FILES["upFile"]["tmp_name"], $target_file)) {
          echo $folderName.'/'.basename( $_FILES["upFile"]["name"]);
        } else {
          echo "Sorry, there was an error uploading your file.";
        }
    }
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}
?>