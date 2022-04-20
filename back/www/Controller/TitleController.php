<?php

namespace Src\Controller;

include_once 'TableGateways/Dependencies.php';

use Src\TableGateways\TitleGateway;
use Src\TableGateways\UserGateway;

class TitleController
{

  private $db;
  private $requestMethod;

  private $titleGateway;
  private $userGateway;

  public function __construct($db, $requestMethod)
  {
    $this->db = $db;
    $this->requestMethod = $requestMethod;

    $this->titleGateway = new TitleGateway($db);
    $this->userGateway = new UserGateway($db);
  }

  public function processRequest()
  {
    // api list:
    // GET     title/
    // PATCH   title/
    // request entities(GET): (empty)
    // request entities(PATCH): userid, token, newTitle
    switch ($this->requestMethod) {
      case 'GET':
        $response = $this->getTitle();
        break;
      case 'PATCH':
        $response = $this->updateTitle();
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

  private function getTitle()
  {
    $result = $this->titleGateway->get();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result[0]);
    return $response;
  }

  private function updateTitle()
  {
    $result = $this->titleGateway->get();
    if (!$result) {
      return $this->notFoundResponse();
    }
    
    $input = (array) json_decode(file_get_contents('php://input'), true);
    if (!$this->validatePerson($input)) {
      return $this->unprocessableEntityResponse();
    }
    $this->titleGateway->update($result[0]['webtitle'], $input);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = null;
    return $response;
  }

  private function validatePerson($input)
  {
    $match = $this->userGateway->find($input['userid'])[0];
    if (!$match['token'] or $match['token'] != $input['token']) {
      return false;
    }
    if (!$match['manager']) {
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
