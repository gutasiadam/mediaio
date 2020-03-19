<?php 

require("../header.php");
require("../translation.php");
if($_SESSION['GCodeState']==""){
//QEVHSR2OMFIRLQFI
require_once("GoogleAuthenticator.php");
$servername = "localhost";
    $username = "root";
    $password = $application_DATABASE_PASS;
    $dbname = "loginsystem";
    $conn = new mysqli($servername, $username, $password, $dbname);
    $uName = $_SESSION['UserUserName'];

$ga = new PHPGangsta_GoogleAuthenticator();

$secret = $ga->createSecret();
//echo $secret.'<br \>';

$qr = $ga->getQrCodeGoogleUrl('ArpadMediaIO', $secret);
//echo '<img src="'.$qr.'"/>';

$myCode = $ga->getCode($secret);
$result = $ga->verifyCode($secret,$myCode,3);

//echo $result;
$sql = "UPDATE users SET GAUTH_SECRET='$secret' WHERE usernameUsers = '$uName'";

$result = $conn->query($sql);
if($result) // will return true if succefull else it will return false
{
echo 1;
}else{echo 0;}

echo '<table align=center>
  <tr><td id="key_msg"></td></tr>
  <tr>
    <td>A titkos kulcsod:</td>
  </tr>
  <tr>
    <th>'.$secret.'</th>
  </tr>
  <tr>
    <td><img src="'.$qr.'"/></td>
  </tr>
  <tr>
    <td><button class="btn btn-danger" onClick="window.location.reload();">Nem tudtam elmenteni, újat generálok!</button></td>
    <form id="checkCode">
    <tr><td><input id="GCode" class="form-control" type=text autocomplete="off" placeholder="Írd be a jelenlegi 6-jegyű kódot"></input></td></tr>
    
    <td><button type="submit" class="btn btn-success" href="gTest.php">Ellenőrzés</button></td></form>
  </tr>
</table>';}
else{
    echo '<table align=center>
    <tr><td><h1 class="text text-danger">Már van Google Authenticator kódód!</h1></td></tr>
  </table>';
}
?>
<script>
$( document ).ready(function() {
    $("#key_msg").fadeOut();
});

$( "#checkCode" ).submit(function( event ) {
  var GCode = document.getElementById("GCode").value;
  event.preventDefault();
  $.ajax({url: "GAUTH_login.php",
    type:"POST",
    data:{GCode:GCode}, 
    success: function(result){
    if(result==1){
        $("#key_msg").html("<b>A kulcsodat sikeresen aktiváltad! Kérlek ne töltsd újra az oldalt!</b>");
        $("#key_msg").fadeIn();
        setTimeout(function(){
        window.location.replace("../profile/index.php");
},3000); 
    }
  }});
});
</script>