<?php 
namespace Mediaio;
require "./Mediaio_autoload.php";


class takeOutManager{

}

class retrieveManager{
    
}

class itemDataManager{
    static function getNumberOfTotalItems(){}
    static function getNumberOfTakenItems(){}
    static function getItemData($itemTypes){
        $displayed="";
        if ($itemTypes['toDisplay1']!=1 & $itemTypes['toDisplay2']!=2 & $itemTypes['toDisplay3']!=3 ){
            return NULL;
        }
        $sql= 'SELECT * FROM leltar WHERE';
        //Kölcsönözhető
        if ($itemTypes['toDisplay1']==1){
          $sql = $sql.' TakeRestrict=""';
          $displayed=$displayed." Kölcsönözhető";
        }
        //Stúdiós
        if ($itemTypes['toDisplay2']==2){
          if (isset($_GET['toDisplay1'])){
            $sql = $sql.' OR TakeRestrict="s"';
            $displayed=$displayed.", Stúdiós";
          }else{
            $sql = $sql.' TakeRestrict="s"';
            $displayed=$displayed." Stúdiós";
          }
          
        }
        //Nem kölcsönözhető
        if ($itemTypes['toDisplay3']==3){
          if (isset($_GET['toDisplay1']) || isset($_GET['toDisplay2'])){
            $sql = $sql.' OR TakeRestrict="*"';
            $displayed=$displayed.", Nem kölcsönözhető";
          }else{
            $sql = $sql.' TakeRestrict="*"';
            $displayed=$displayed."Nem kölcsönözhető";
          }
        }
        $sql= $sql." ORDER BY Nev ASC";
        return Database::runQuery($sql);
    }
}

class itemHistoryManager{

}

?>