<?php

namespace Src\TableGateways;

class UserGateway
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
                *
            FROM
                users;
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
                users
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

  public function findByName($name)
  {
    $statement = "
            SELECT 
                *
            FROM
                users
            WHERE name = :name;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'name' => $name
      ));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }

  public function findByLogin($name, $password)
  {
    $statement = "
            SELECT 
                *
            FROM
                users
            WHERE name = :name AND password = :password;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'name' => $name,
        'password' => $password
      ));
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
            INSERT INTO users 
                (id, name, password, token, manager, sticker)
            VALUES
                (:id, :name, :password, :token, :manager, :sticker);
        ";

    try {
      $charid = md5(uniqid(rand(), true));
      $uuid = substr($charid, 0, 8).chr(45)
        . substr($charid, 8, 4).chr(45)
        . substr($charid, 12, 4).chr(45)
        . substr($charid, 16, 4).chr(45)
        . substr($charid, 20, 12);
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'id' => $uuid,
        'name' => $input['username'],
        'password'  => $input['password'],
        'token'  => '',
        'manager'  => 0,
        'sticker'  => $input['sticker'] ?? null,
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
            UPDATE users
            SET
              token = :token
            WHERE id = :id;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'id' => (string)$id,
        'token' => $input['token'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }

  // not used but keep

  // public function delete($id)
  // {
  //   $statement = "
  //           DELETE FROM msgs
  //           WHERE id = :id;
  //       ";

  //   try {
  //     $statement = $this->db->prepare($statement);
  //     $statement->execute(array('id' => $id));
  //     return $statement->rowCount();
  //   } catch (\PDOException $e) {
  //     exit($e->getMessage());
  //   }
  // }
}
