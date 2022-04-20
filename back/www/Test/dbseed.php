<?php
require 'bootstrap.php';

$statement = <<<EOS
    SHOW TABLES;
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    error_log($e->getMessage());
    exit($e->getMessage());
}

?>