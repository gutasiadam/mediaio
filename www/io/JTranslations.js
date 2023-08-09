var applicationTitleShort = "Arpad Media IO";

var button_Go = "Go";

var takeout_Success= "Sikeres kivétel!";
var takeout_Unavailible = "A kiválasztott tárgy nincs a raktárban!";

var retrieve_Success= "Sikeresen visszahoztad a tárgya(ka)t!";
var retrieve_AuthCode_success= "<strong>Sikeresen</strong> visszahoztad a tárgyat és felhasználtad a kódot!";
var retrieve_Error= "A tárgy a raktárban van!";
var retrieve_no_AuthCode_given= "Nem adtál meg kódot!";
var retrieve_Error_AuthCode_General= "Az általad megadott kód hibás vagy nem létezik!";

//EVERY ITEM PRESENT IN THE SYSTEM - MODIFY WITH CAUTION!
//DB Items updater
function loadFile(filePath) {
  var result = null;
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", filePath, false);
  xmlhttp.send();
  if (xmlhttp.status==200) {
    result = xmlhttp.responseText;
  }
  return result.split("\n");
  
}
 