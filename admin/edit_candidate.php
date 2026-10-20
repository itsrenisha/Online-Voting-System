<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

$error = "";
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $election_id = (int)$_POST['election_id'];
    $full_name = trim($_POST['full_name']);
    $symbol = trim($_POST['symbol']);
    $manifesto = trim($_POST['manifesto']);
    $achievements = trim($_POST['achievements']);
    $photo = $_POST['existing_photo'];

    if (empty($election_id) || empty($full_name) || empty($symbol)) {
        $error = "Election, name, and symbol are required.";
    } else {

        // Upload new photo if selected
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $photo = time() . '_' . rand(1000, 9999) . '.' . $ext;
                $upload_path = '../uploads/candidates/' . $photo;

                move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path);
            } else {
                $error = "Photo must be a jpg, jpeg, png, or gif file.";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("
                UPDATE candidates 
                SET election_id=?, full_name=?, symbol=?, manifesto=?, photo=?, achievements=?
                WHERE id=?
            ");

            $stmt->bind_param(
                "isssssi",
                $election_id,
                $full_name,
                $symbol,
                $manifesto,
                $photo,
                $achievements,
                $id
            );

            if ($stmt->execute()) {
                header("Location: candidates.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }
    }
}

$stmt = $conn->prepare("
    SELECT election_id, full_name, symbol, manifesto, photo, achievements
    FROM candidates
    WHERE id=?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: candidates.php");
    exit();
}

$candidate = $result->fetch_assoc();
$stmt->close();

$elections = $conn->query("SELECT id, title FROM elections ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Candidate - Vote</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">
</head>

<body data-page="candidates"
data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">
<div class="main-inner">

<h1 class="page-title">Edit Candidate</h1>

<p class="page-subtitle">
Update the candidate's details below
</p>

<div class="card form-page-card">

<?php if ($error): ?>

<div class="message error">
<?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>


<?php if (!empty($candidate['photo'])): ?>

<img
src="../uploads/candidates/<?php echo htmlspecialchars($candidate['photo']); ?>"
style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:16px;"
>

<?php endif; ?>


<form method="POST" action="edit_candidate.php" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $id; ?>">

<input
type="hidden"
name="existing_photo"
value="<?php echo htmlspecialchars($candidate['photo']); ?>"
>


<div class="form-group">

<label>Election</label>

<select name="election_id" required
style="width:100%;padding:11px 13px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:13.5px;">

<?php while ($e = $elections->fetch_assoc()): ?>

<option
value="<?php echo $e['id']; ?>"
<?php echo ($e['id'] == $candidate['election_id']) ? 'selected' : ''; ?>
>

<?php echo htmlspecialchars($e['title']); ?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-group">

<label>Full Name</label>

<input
type="text"
name="full_name"
value="<?php echo htmlspecialchars($candidate['full_name']); ?>"
required
>

</div>


<div class="form-group">

<label>Symbol</label>

<input
type="text"
name="symbol"
value="<?php echo htmlspecialchars($candidate['symbol']); ?>"
required
>

</div>


<div class="form-group">

<label>Photo</label>

<input
type="file"
name="photo"
accept="image/*"
>

<small>Leave empty to keep the current photo.</small>

</div>


<div class="form-group">

<label>Manifesto</label>

<input
type="text"
name="manifesto"
value="<?php echo htmlspecialchars($candidate['manifesto']); ?>"
>

</div>


<div class="form-group">

<label>Achievements</label>

<textarea
name="achievements"
rows="3"
placeholder="e.g. Won Best Speaker Award 2025"
style="width:100%;padding:11px 13px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:13.5px;resize:vertical;"
><?php echo htmlspecialchars($candidate['achievements']); ?></textarea>

</div>


<div class="form-actions">

<a href="candidates.php" style="flex:1;">

<button
type="button"
class="btn btn-outline btn-block"
>
Cancel
</button>

</a>

<button
type="submit"
class="btn btn-primary"
>
Update Candidate
</button>

</div>

</form>

</div>

</div>
</main>

<script src="../js/admin-layout.js"></script>

</body>
</html>

<?php
$conn->close();
?>