<?php

class backGroundManager
{
  static function uploadBackground($formId, $file)
  {
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_dir = "./backgrounds/";
    $target_file = $target_dir . $formId . "-background." . $imageFileType;
    $uploadOk = 1;
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
      $uploadOk = 1;
    } else {
      return 400;
    }

    // Check file size
    if ($file["size"] > 5*1024*1024) { // 5MB
      return 500;
    }
    // Allow certain file formats
    if (
      $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif"
    ) {
      return 400;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      return 500;
      // if everything is ok, try to upload file
    } else {
      if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $formId . "-background." . $imageFileType;
      } else {
        return 500;
      }
    }
  }

  static function deleteBackground($formId, $file)
  {
    $target_dir = "./backgrounds/";
    $target_file = $target_dir . $file;
    if (file_exists($target_file)) {
      unlink($target_file);
      echo "File deleted.";
    } else {
      echo "File not found.";
    }
  }
}


if (isset ($_POST['mode'])) {
  if ($_POST['mode'] == "uploadBackground") {
    echo backGroundManager::uploadBackground($_POST['formId'], $_FILES['fileToUpload']);
    exit();
  }
  if ($_POST['mode'] == "deleteBackground") {
    echo backGroundManager::deleteBackground($_POST['formId'], $_POST['fileToUpload']);
    exit();
  }
}


?>