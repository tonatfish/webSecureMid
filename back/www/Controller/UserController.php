<?php

namespace Src\Controller;

include_once 'TableGateways/UserGateway.php';

use Src\TableGateways\UserGateway;

class UserController
{

  private $db;
  private $requestMethod;
  private $userParam;

  private $userGateway;

  public function __construct($db, $requestMethod, $userParam)
  {
    $this->db = $db;
    $this->requestMethod = $requestMethod;
    $this->userParam = $userParam;

    $this->userGateway = new UserGateway($db);
  }

  public function processRequest()
  {
    // api list:
    // POST    user/login
    // POST    user/logout
    // POST    user/
    // POST    user/get
    // request entities(login): username, password
    // request entities(get, logout): userid, token
    // request entities(POST): username, password, weburl / sticker(file)
    switch ($this->requestMethod) {
      case 'POST':
        if ($this->userParam == "login") {
          $response = $this->loginUser();
        } else if ($this->userParam == "logout") {
          $response = $this->logoutUser();
        } else if ($this->userParam == "get") {
          $response = $this->getUser();
        } else {
          $response = $this->createUser();
        }
        break;
      default:
        $response = $this->notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    if ($response['body']) {
      echo $response['body'];
    }
  }

  private function createUser()
  {
    $input = $_POST;
    if (!$input['username']) {
      $input = (array) json_decode(file_get_contents('php://input'), true);
    }

    if (!$this->validateCreate($input)) {
      return $this->unprocessableEntityResponse();
    }

    if ($_FILES['sticker']) {
      $image = $_FILES["sticker"];
      $info = getimagesize($image["tmp_name"]);
      if (!$info) {
        return $this->unprocessableEntityResponse();
      }
      $input['sticker'] = file_get_contents($image["tmp_name"]);
    } else if ($input['weburl']) {
      $info = getimagesize($input["weburl"]);
      if (!$info) {
        return $this->unprocessableEntityResponse();
      }
      $input['sticker'] = file_get_contents($input["weburl"]);
    }

    // deal with blob
    $this->userGateway->insert($input);

    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = null;
    return $response;
  }

  private function getUser()
  {
    // $input = $_POST;
    $input = (array) json_decode(file_get_contents('php://input'), true);
    $result = $this->userGateway->find($input['userid'])[0];
    if (!$result) {
      return $this->notFoundResponse();
    }

    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }

    $result['sticker'] = base64_encode($result['sticker']);
    $result['password'] = "";
    $result['manager'] = "";

    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function loginUser()
  {
    // $input = $_POST;
    $input = (array) json_decode(file_get_contents('php://input'), true);
    $result = $this->userGateway->findByLogin($input['username'], $input['password'])[0];
    if (!$result) {
      return $this->notFoundResponse();
    }
    $result['sticker'] = base64_encode($result['sticker']);

    $token = rand(0, 2147483640);
    $input['token'] = $token;
    $result['token'] = $token;
    $this->userGateway->update($result['id'], $input);
    $result['password'] = "";
    $result['manager'] = "";
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function logoutUser()
  {
    // $input = $_POST;
    $input = (array) json_decode(file_get_contents('php://input'), true);
    $result = $this->userGateway->find($input['userid'])[0];

    if (!$result) {
      return $this->notFoundResponse();
    }

    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }
    $tokenObj = [
      'token' => ''
    ];
    $this->userGateway->update($result['id'], $tokenObj);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = 'Success!';
    return $response;
  }

  private function validateCreate($input)
  {
    $match = $this->userGateway->findByName($input['username'])[0];
    if ($match) {
      return false;
    }
    if (!isset($_FILES['sticker']) and !$input['weburl']) {
      return false;
    }
    return true;
  }

  private function validatePerson($input)
  {
    $match = $this->userGateway->find($input['userid'])[0];
    if (!isset($match['token']) or $match['token'] != $input['token']) {
      return false;
    }
    return true;
  }

  private function unprocessableEntityResponse()
  {
    $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
    $response['body'] = json_encode([
      'error' => 'Invalid input'
    ]);
    return $response;
  }

  private function notFoundResponse()
  {
    $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
    $response['body'] = null;
    return $response;
  }
}
