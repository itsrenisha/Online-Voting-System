<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        // Foreign key constraint blocks this if the candidate already has votes
        $stmt->close();
        $conn->close();
        header("Location: candidates.php?error=has_votes");
        exit();
    }
    $stmt->close();
}

$conn->close();
header("Location: candidates.php");
exit();
?>
