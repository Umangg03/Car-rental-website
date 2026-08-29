<?php
include 'db.php';

$result = mysqli_query($conn, "SELECT * FROM cars ORDER BY created_at DESC");
$cars = [];

while ($row = mysqli_fetch_assoc($result)) {
    $cars[] = $row;
}

echo json_encode($cars);
?>
