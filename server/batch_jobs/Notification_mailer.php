<?php 
/*Email küldése a következőkről:
* Több, mint egy hete nem megerősített esemémy
* Több, mint egy hónapja kivett tárgy, ami nem lett visszahozva.
*/
//Mai nap:
$today = new DateTime(date("Y-m-d"));

//PÉLDA két dátum közti különbségre.
$earlier = new DateTime("2010-07-06");
$diff = $today->diff($earlier)->format("%a");
echo $diff;

//Példa a dátumok különbségére:
//SELECT *, DATE("2021/01/25")-DATE(Date) FROM takelog WHERE (DATE("2021/01/25")-DATE(Date))>0 ORDER BY Date DESC
function BATCH_notify_Unconfirmed_Events(){
    //Lekérdezés: események, amik a prepben vannak több, mint egy hete.
};
?>