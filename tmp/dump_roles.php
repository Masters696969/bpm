<?php
require_once 'c:/xamppp/htdocs/microfinance-backup/config/config.php';
$res = $conn->query("SELECT * FROM roles");
$roles = [];
while($row = $res->fetch_assoc()) $roles[] = $row;
file_put_contents('c:/xamppp/htdocs/microfinance-backup/tmp/roles_dump.json', json_encode($roles, JSON_PRETTY_PRINT));
?>
