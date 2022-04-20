<?php
include_once "../Controller/Dependencies.php";
require "../bootstrap.php";

use Src\Controller\MsgController;
use Src\Controller\TitleController;
use Src\Controller\UserController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json, multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PATCH,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-methods,access-control-allow-origin,access-control-max-age");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$requestMethod = $_SERVER["REQUEST_METHOD"];

// router
if ($requestMethod == 'OPTIONS') { // may not be safe? idk
  header('HTTP/1.1 200 OK');
} else {
  if ($uri[1] == 'msg') {
    // api list:
    // POST    msg/get/
    // POST    msg/get/:id
    // POST    msg/
    // DELETE  msg/:id
    // request entities(GET, DELETE): userid, token
    // request entities(POST): userid, token, content, file?(blob)
    $msgId = null;
    $msgParam = null;
    if (isset($uri[2])) {
      if ($uri[2] == 'get') {
        $msgParam = $uri[2];
      } else {
        $msgId = $uri[2];
      }
    }
    if (isset($uri[3])) {
      $msgParam = $uri[2];
      $msgId = $uri[3];
    }
    $controller = new MsgController($dbConnection, $requestMethod, $msgId, $msgParam);
  } else if ($uri[1] == 'title') {
    // api list:
    // GET     title/
    // PATCH   title/
    // request entities(GET): (empty)
    // request entities(PATCH): userid, token, newTitle
    $controller = new TitleController($dbConnection, $requestMethod);
  } else if ($uri[1] == 'user') {
    // api list:
    // POST    user/login
    // POST    user/logout
    // POST    user/
    // POST    user/get
    // request entities(login): username, password
    // request entities(get, logout): userid, token
    // request entities(POST): username, password, weburl / sticker(file)
    $userParam = null;
    if (isset($uri[2])) {
      $userParam = $uri[2];
    }
    $controller = new UserController($dbConnection, $requestMethod, $userParam);
  } else {
    // everything else results in a 404 Not Found
    header("HTTP/1.1 404 Not Found");
    exit();
  }



  // pass the request method and user ID to the PersonController and process the HTTP request:

  $controller->processRequest();
}
