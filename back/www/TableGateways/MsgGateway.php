<?php

namespace Src\TableGateways;

class MsgGateway
{

  private $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function findAll()
  {
    $statement = "
            SELECT 
                id, username, content, filename
            FROM
                msgs;
        ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }

  public function find($id)
  {
    $statement = "
            SELECT 
                *
            FROM
                msgs
            WHERE id = ?;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($id));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }

  public function insert(array $input)
  {
    $statement = "
            INSERT INTO msgs 
                (userid, username, content, filename, filetype, file)
            VALUES
                (:userid, :username, :content, :filename, :filetype, :file);
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'userid' => $input['userid'],
        'username'  => $input['username'],
        'content'  => $input['content'] ?? null,
        'filename'  => $input['filename'] ?? null,
        'filetype' => $input['filetype'] ?? null,
        'file'  => $input['file'] ?? null,
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }

  public function update($id, array $input)
  {
    $statement = "
            UPDATE msgs
            SET 
              content = :content,
            WHERE id = :id;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'id' => (int) $id,
        'content' => $input['content'] ?? null,
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }

  public function delete($id)
  {
    $statement = "
            DELETE FROM msgs
            WHERE id = :id;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array('id' => $id));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }
}
