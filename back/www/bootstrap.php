<?php
include_once 'System/Dependencies.php';

use Src\System\DatabaseConnector;

$dbConnection = (new DatabaseConnector())->getConnection();