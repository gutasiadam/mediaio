<?php
//  This script determines the SERVER machine's IP address, 
// allowing to change development and production URL's easily.

    //echo $_SERVER['REMOTE_ADDR']; 
$ip_address=file_get_contents('http://checkip.dyndns.com/');
/*echo '<br><br>';
echo $ip_address;
echo '<br><br>';*/
$ip_address = strip_tags($ip_address);
$ip_address = str_replace("Current IP CheckCurrent IP Address: ","",$ip_address);
substr_replace($ip_address ,"", -1);
$ip_address=str_replace("\r\n","",$ip_address);

?>
