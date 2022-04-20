<?php

namespace Src\Controller;

include_once 'TableGateways/Dependencies.php';

use Src\TableGateways\MsgGateway;
use Src\TableGateways\UserGateway;

class MsgController
{

  private $db;
  private $requestMethod;
  private $msgId;
  private $msgParam;

  private $msgGateway;
  private $userGateway;

  public function __construct($db, $requestMethod, $msgId, $msgParam)
  {
    $this->db = $db;
    $this->requestMethod = $requestMethod;
    $this->msgId = $msgId;
    $this->msgParam = $msgParam;

    $this->msgGateway = new MsgGateway($db);
    $this->userGateway = new UserGateway($db);
  }

  public function processRequest()
  {
    // api list:
    // POST    msg/get/
    // POST    msg/get/:id
    // POST    msg/
    // DELETE  msg/:id
    // request entities(GET, DELETE): userid, token
    // request entities(POST): userid, token, content, file?(blob)
    switch ($this->requestMethod) {
      case 'POST':
        if ($this->msgParam == 'get') {
          if ($this->msgId) {
            $response = $this->getMsg($this->msgId);
          } else {
            $response = $this->getAllMsgs();
          };
        } else {
          $response = $this->createMsg();
        }
        break;
      case 'DELETE':
        $response = $this->deleteMsg($this->msgId);
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

  private function getAllMsgs()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }

    $result = $this->msgGateway->findAll();

    for ($i = 0; $i < count($result); $i++) {
      $result[$i]['userid'] = "";
      $result[$i]['file'] = "";
      $result[$i]['filetype'] = "";
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getMsg($id)
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }

    $result = $this->msgGateway->find($id)[0];
    if (!$result) {
      return $this->notFoundResponse();
    }
    $result['userid'] = "";
    $result['file'] = base64_encode($result['file']);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createMsg()
  {
    $input = $_POST;

    if (!$input['userid']) {
      $input = (array) json_decode(file_get_contents('php://input'), true);
    }

    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }
    $user = $this->userGateway->find($input['userid'])[0];
    if ($_FILES['file']) {
      $fileData = $_FILES['file'];
      $input['filename'] = $fileData['name'];
      $input['filetype'] = mime_content_type($fileData["tmp_name"]);
      $input['file'] = file_get_contents($fileData["tmp_name"]);
    }
    $input['username'] = $user['name'];
    $this->msgGateway->insert($input);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = null;
    return $response;
  }

  private function deleteMsg($id)
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }

    $result = $this->msgGateway->find($id)[0];
    if (!$this->validateOwner($result, $input)) {
      return $this->unprocessableEntityResponse();
    }
    if (!$result) {
      return $this->notFoundResponse();
    }
    $this->msgGateway->delete($id);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = 'Delete success!';
    return $response;
  }

  private function validatePerson($input)
  {
    $match = $this->userGateway->find($input['userid'])[0];
    if (!isset($match['token']) or $match['token'] != $input['token']) {
      return false;
    }
    return true;
  }

  private function validateOwner($msgData, $input)
  {
    if ($msgData['userid'] != $input['userid']) {
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
