<?php
require_once __DIR__.'/../../Core.php';
use Mediaio\Core;
//Usage: /api/auth/?username=%22a%22&password=%22b%22

//Returns 200 with a token if the credentials are correct
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
  {
    case 'GET':
      // Retrive Products
      if(!empty($_GET["useremail"])&&!empty($_GET["pwd"]&&$_GET["method"]=="createApiKey"))
      {
        $_GET["login-submit"]=true;
        
        $c=new Core();
        $status=$c->loginUser($_GET,true);
        
		//TODO: implement api key generation

		header("HTTP/1.0 ".$status['code']);
		//set the content type to json
		header('Content-Type: application/json');
		//echo the json string
    if($status['code']==200){
      echo json_encode(array('key'=>$status['token'], 'code' => $status['code']));
      exit();
    }

      }
    else if(!empty($_GET["apikey"])&&$_GET["method"]=="login"){
        //TODO: obtain session values using api key
        $c=new Core();
        $response=$c->loginWithApikey($_GET["apikey"]);
        header("HTTP/1.0 ".$response['code']);
        header('Content-Type: application/json');
        if ($response['code']==200){
          echo json_encode(array('code'=>$response['code'], 'response' => $response['userData']));
          exit();
        }else{
          echo json_encode(array('code'=>$response['code'], 'response' => 'Invalid api key'));
          exit();
        }
  
    }
	  else if(!empty($_GET["apikey"])&&$_GET["method"]=="logout"){
      $c=new Core();
      $response=$c->destroyApiKey($_GET["apikey"]);
      
      header("HTTP/1.0 ".$response['code']);
      header('Content-Type: application/json');
      echo json_encode(array('code' => $response['code']));

		//TODO: destroy api key, and stop session
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