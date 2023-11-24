<?php
require_once __DIR__.'/../../ItemManager.php';
require_once __DIR__.'/../../Core.php';
use Mediaio\ItemDataManager;
use Mediaio\takeOutManager;
use Mediaio\Core;
//Usage: TODO

$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
  {
    case 'GET':
      // Take out items
      //items field is comma separated
      if(!empty($_GET["apikey"]&&!empty($_GET["items"])))
      {
        //Check API key
        $c=new Core();
        $loginResponse=$c->loginWithApikey($_GET["apikey"]);
        if($loginResponse['code']!=200){
          header("HTTP/1.0 ".$loginResponse['code']);
          header('Content-Type: application/json');
          echo json_encode(array('type'=>'error', 'text' => 'Invalid api key'));
          exit();
        }
        //Convert items to array. Items are comma separated
        $items=explode(",",$_GET["items"]);
        $takeOutManager=new takeOutManager();
        $result=$takeOutManager->REST_retrieve($items,$loginResponse['userData']);
        header("HTTP/1.0 ".$loginResponse['code']);
        header('Content-Type: application/json');
        echo json_encode(array('result'=>$result,'response'=>$loginResponse['code']));
      }
      else
      {
        header("HTTP/1.0 500 Internal Server Error");
      }
      break;
    default:
      // Invalid Request Method
      header("HTTP/1.0 405 Method Not Allowed");
      break;
  }


?>