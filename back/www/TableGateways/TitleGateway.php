<?php

namespace Src\TableGateways;

class TitleGateway
{

  private $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function get()
  {
    $statement = "
            SELECT 
                *
            FROM
            title;
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

  public function update($oldtitle, array $input)
  {
    $statement = "
            UPDATE title
            SET 
              webtitle = :title
            WHERE webtitle = :oldtitle;
        ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'title' => $input['newtitle'] ?? $oldtitle,
        'oldtitle' => $oldtitle,
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      exit($e->getMessage());
    }
  }
}
