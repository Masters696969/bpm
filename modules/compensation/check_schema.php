<?php
require_once '../../config/config.php';
$res = $conn->query("DESCRIBE simulation_drafts");
$cols = [];
while($row = $res->fetch_assoc()) $cols[] = $row['Field'];
echo json_encode($cols);
?>
