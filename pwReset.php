<?php 
require("header.php");
?>
<table class="logintable">
<th><h1>Elfelejtett jelszó helyreállítása</h1><br><h3>Elfelejtetted a jelszavad? Semmi gond! Állítsuk helyre!</h3></th>
<form name="frmForgot" class="form-inline my-2 my-lg-0" id="frmForgot" method="post" onSubmit="return validate_forgot();">
	<?php if(!empty($success_message)) { ?>
	<div class="success_message"><?php echo $success_message; ?></div>
	<?php } ?>

	<div id="validation-message">
		<?php if(!empty($error_message)) { ?>
	<?php echo $error_message; ?>
	<?php } ?>
	</div>

	<tr><td><div class="field-group">
		<div><label for="username">Felhasználónév</label></div>
		<div><input type="text" name="user-login-name" id="user-login-name" class="form-control mb-2 mr-sm-2"> Vagy</div>
	</div></tr></td>
	
	<tr><td><div class="field-group">
		<div><label for="email">Email</label></div>
		<div><input type="text" name="user-email" id="user-email" class="form-control mb-2 mr-sm-2"></div>
	</div></tr></td>
	
	<tr><td><div class="field-group">
		<div><input type="submit" name="forgot-password" id="forgot-password" value="Küldés" class="btn btn-dark"></div>
	</div></tr></td>
	<tr><td>
	<div class="mailStatus">
	<div class="spinner-border text-dark" role="status">
            <span class="sr-only">Loading...</span></div>
             Az új, ideiglenes jelszavadat éppen az e-mail címedre küldjük, légy türelemmel!</tr></td></div>
</form>
</table>
<?php
	if(!empty($_POST["forgot-password"])){
		$conn = mysqli_connect("localhost", "root", "umvHVAZ%", "loginsystem");
		
		$condition = "";
		if(!empty($_POST["user-login-name"])) 
			$condition = " usernameUsers = '" . $_POST["user-login-name"] . "'";
		if(!empty($_POST["user-email"])) {
			if(!empty($condition)) {
				$condition = " and ";
			}
			$condition = " emailUsers = '" . $_POST["user-email"] . "'";
		}
		
		if(!empty($condition)) {
			$condition = " where " . $condition;
		}

		$sql = "Select * from users " . $condition;
		$result = mysqli_query($conn,$sql);
		$user = mysqli_fetch_array($result);
		
		if(!empty($user)) {
            function generateRandomString($length = 10) {
                return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
            }
			$password = generateRandomString();
			$hashedpwd = password_hash($password, PASSWORD_BCRYPT);
			$uName=$_POST["user-login-name"];
			$eName=$_POST["user-email"];
			$sql="UPDATE `users` SET `pwdUsers` = '$hashedpwd' WHERE `usernameUsers` = '$uName' OR `emailUsers`='$eName'";
			$result = $conn->query($sql);
if (!$result) {
    trigger_error('Invalid query: ' . $conn->error);
}
	else {//MINDEN OK, Megy E-mailben az ideiglenes kód
            //echo $password;
            $sql= "SELECT `emailUsers` FROM `users` WHERE `usernameUsers`='$uName'";
            $result = $conn->query($sql);
$rs = $result->fetch_assoc();
            //echo $rs['emailUsers'];

$to = $rs['emailUsers'];

// Subject
$subject = 'MediaIO - Jelszó-helyreállítás';

// Message
$message = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$uName.'!</h3><p>
 A jelszavad helyreállítását igényelted. Az ideiglenes jelszavad</p>
<h2>'.$password.'</h2>
  <h5>Kérünk, hogy változtasd meg jelszavadat a legelső belépésednél.<br>Üdvözlettel: <br> Arpad Media Admin</h5>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';

/* Additional headers
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Birthday Reminder <birthday@example.com>';
$headers[] = 'Cc: birthdayarchive@example.com';*/
mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, implode("\r\n", $headers));
header("Location: ./index.php?logout=pwChange");
		}
	}}
	
?>
<style>
.logintable{
  width: 30%;
  text-align: center;
  margin: 0 auto; 
}
</style>
<script>
$("#forgot-password").click(function(){
  $(".mailStatus").show();
});

$( document ).ready(function() {
  $(".mailStatus").hide();
});
</script>