<?php 
/*Email küldése a következőkről:
* Több, mint egy hete nem megerősített esemémy
* Több, mint egy hónapja kivett tárgy, ami nem lett visszahozva.
*/
//Mai nap:
$today = new DateTime(date("Y-m-d"));

//PÉLDA:
$earlier = new DateTime("2010-07-06");
$diff = $today->diff($earlier)->format("%a");
echo $diff;
function BATCH_notify_Unconfirmed_Events(){
    //Lekérdezés: események, amik a prepben vannak több, mint egy hete.
};
?>