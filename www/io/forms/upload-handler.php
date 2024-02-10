<?php

class backGroundManager
{
  static function uploadBackground($formId, $file)
  {
    // Check if file was posted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
      $target_dir = "./backgrounds/";
      $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

      // Rename the file to a unique name based on the current timestamp
      $newFileName = $formId . "-" . $_FILES["fileToUpload"]["name"];
      $target_file = $target_dir . $newFileName;

      // Check if image file is a actual image or fake image
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if ($check !== false) {
        // Try to move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          echo $newFileName;
        } else {
          echo "Sorry, there was an error uploading your file.";
        }
      } else {
        echo "File is not an image.";
      }
    } else {
      echo "No file was posted.";
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


if (isset($_POST['mode'])) {
  if ($_POST['mode'] == "uploadBackground") {
    echo backGroundManager::uploadBackground($_POST['formId'], $_POST['fileToUpload']);
    exit();
  }
  if ($_POST['mode'] == "deleteBackground") {
    echo backGroundManager::deleteBackground($_POST['formId'], $_POST['fileToUpload']);
    exit();
  }
}


?>