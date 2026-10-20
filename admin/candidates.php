<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

$result = $conn->query("
    SELECT c.id, c.full_name, c.symbol, c.manifesto, c.photo,
           c.achievements, e.title AS election_title
    FROM candidates c
    JOIN elections e ON c.election_id = e.id
    ORDER BY c.id DESC
");

if (!$result) {
    die("Database error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Candidates - Vote</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">

<style>

.page-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 22px;
    gap: 16px;
    flex-wrap: wrap;
}

.table-photo,
.table-photo-placeholder {
    width: 36px;
    height: 36px;
    border-radius: 50%;
}

.table-photo {
    object-fit: cover;
}

.table-photo-placeholder {
    background: var(--blue-light);
    color: var(--blue);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

</style>

</head>

<body
data-page="candidates"
data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">

<div class="main-inner">

<div class="page-head">

<div>

<h1 class="page-title">
Manage Candidates
</h1>

<p class="page-subtitle" style="margin-bottom:0;">
Add, edit, or remove candidates for any election
</p>

</div>

<a href="add_candidate.php">

<button class="btn btn-primary">
+ Add Candidate
</button>

</a>

</div>


<?php if (isset($_GET['error']) && $_GET['error'] == 'has_votes'): ?>

<div class="message error">

This candidate can't be deleted because they already have votes.

</div>

<?php endif; ?>


<div class="card table-card">

<table class="data-table">

<thead>

<tr>

<th>Photo</th>
<th>Name</th>
<th>Election</th>
<th>Symbol</th>
<th>Manifesto</th>
<th>Achievements</th>
<th>Actions</th>

</tr>

</thead>


<tbody>

<?php if ($result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td>

<?php if (!empty($row['photo'])): ?>

<img
class="table-photo"
src="../uploads/candidates/<?php echo htmlspecialchars($row['photo']); ?>"
alt="Candidate photo">

<?php else: ?>

<div class="table-photo-placeholder">

<?php
echo htmlspecialchars(
    strtoupper(substr($row['full_name'], 0, 1))
);
?>

</div>

<?php endif; ?>

</td>


<td>
<?php echo htmlspecialchars($row['full_name']); ?>
</td>


<td>
<?php echo htmlspecialchars($row['election_title']); ?>
</td>


<td>
<?php echo htmlspecialchars($row['symbol']); ?>
</td>


<td>

<?php

echo htmlspecialchars(
    mb_strimwidth(
        $row['manifesto'] ?? '',
        0,
        50,
        '...'
    )
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    mb_strimwidth(
        $row['achievements'] ?? '',
        0,
        50,
        '...'
    )
);

?>

</td>


<td>

<div class="table-actions">

<a
class="action-edit"
href="edit_candidate.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a
class="action-delete"
href="delete_candidate.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this candidate?');">
Delete
</a>

</div>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr class="empty-row">

<td colspan="7">
No candidates added yet.
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</main>

<script src="../js/admin-layout.js"></script>

</body>

</html>

<?php
$conn->close();
?>