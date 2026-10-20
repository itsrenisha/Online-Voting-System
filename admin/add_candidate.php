<?php

include '../includes/admin_auth_check.php';
include '../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $election_id = $_POST['election_id'];
    $full_name = trim($_POST['full_name']);
    $symbol = trim($_POST['symbol']);
    $manifesto = trim($_POST['manifesto']);
    $achievements = trim($_POST['achievements']);

    $photo = "";

    // Basic validation
    if (empty($election_id) || empty($full_name) || empty($symbol)) {

        $error = "Election, name, and symbol are required.";

    } else {

        // Upload photo
        if (!empty($_FILES['photo']['name'])) {

            $file_name = $_FILES['photo']['name'];
            $file_tmp = $_FILES['photo']['tmp_name'];

            $file_extension = strtolower(
                pathinfo($file_name, PATHINFO_EXTENSION)
            );

            $allowed_types = ["jpg", "jpeg", "png", "gif"];

            if (in_array($file_extension, $allowed_types)) {

                $photo = time() . "_" . $file_name;

                $upload_path = "../uploads/candidates/" . $photo;

                move_uploaded_file($file_tmp, $upload_path);

            } else {

                $error = "Only JPG, JPEG, PNG, and GIF images are allowed.";
            }
        }

        // Save candidate
        if (empty($error)) {

            $sql = "INSERT INTO candidates
                    (election_id, full_name, symbol, manifesto, photo, achievements)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "isssss",
                $election_id,
                $full_name,
                $symbol,
                $manifesto,
                $photo,
                $achievements
            );

            if ($stmt->execute()) {

                header("Location: candidates.php");
                exit();

            } else {

                $error = "Could not add candidate.";
            }

            $stmt->close();
        }
    }
}

// Get elections
$elections = $conn->query(
    "SELECT id, title FROM elections ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Candidate - Vote</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">

</head>

<body
data-page="candidates"
data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">

<div class="main-inner">

<h1 class="page-title">Add Candidate</h1>

<p class="page-subtitle">
Enter the candidate's details below
</p>

<div class="card form-page-card">

<?php if ($error): ?>

<div class="message error">
    <?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>


<?php if ($elections->num_rows == 0): ?>

<div class="message error">
    You need to create an election first before adding candidates.
</div>

<a href="add_election.php">
    <button class="btn btn-primary btn-block">
        + Create Election
    </button>
</a>

<?php else: ?>

<form
method="POST"
action="add_candidate.php"
enctype="multipart/form-data">

<div class="form-group">

<label>Election</label>

<select
name="election_id"
required
style="width:100%; padding:11px 13px; border:1px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13.5px;"
>

<option value="">
-- Select an election --
</option>

<?php while ($election = $elections->fetch_assoc()): ?>

<option value="<?php echo $election['id']; ?>">

<?php echo htmlspecialchars($election['title']); ?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-group">

<label>Full Name</label>

<input
type="text"
name="full_name"
placeholder="Enter candidate name"
required
>

</div>


<div class="form-group">

<label>Symbol</label>

<input
type="text"
name="symbol"
placeholder="e.g. Tree, Sun, Flower"
required
>

</div>


<div class="form-group">

<label>Candidate Photo</label>

<input
type="file"
name="photo"
accept="image/*"
>

</div>


<div class="form-group">

<label>Manifesto</label>

<textarea
name="manifesto"
rows="3"
placeholder="Enter candidate manifesto"
style="width:100%; padding:11px 13px; border:1px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13.5px; resize:vertical;"
></textarea>

</div>


<div class="form-group">

<label>Achievements</label>

<textarea
name="achievements"
rows="4"
placeholder="e.g. Won Best Speaker Award 2025&#10;Organized college cleanliness program"
style="width:100%; padding:11px 13px; border:1px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13.5px; resize:vertical;"
></textarea>

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
Save Candidate
</button>

</div>

</form>

<?php endif; ?>

</div>

</div>

</main>

<script src="../js/admin-layout.js"></script>

</body>

</html>