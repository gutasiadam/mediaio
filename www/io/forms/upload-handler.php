<?php
$form_id = $_POST['formId'];
// Check if file was posted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
  $target_dir = "./backgrounds/";
  $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

  // Rename the file to a unique name based on the current timestamp
  $newFileName = $form_id . "-" . $_FILES["fileToUpload"]["name"];
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
?>