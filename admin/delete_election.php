<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $conn->query("DELETE FROM votes WHERE election_id = $id");
    $conn->query("DELETE FROM candidates WHERE election_id = $id");

    $stmt = $conn->prepare("DELETE FROM elections WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: elections.php");
exit();
?>
