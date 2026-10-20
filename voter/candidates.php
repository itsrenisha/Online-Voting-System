<?php
include '../includes/auth_check.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];
$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;

if ($election_id <= 0) {
    header("Location: elections.php");
    exit();
}

// Get election
$stmt = $conn->prepare("
    SELECT id, title,
    CASE
        WHEN CURDATE() < start_date THEN 'upcoming'
        WHEN CURDATE() > end_date THEN 'completed'
        ELSE 'active'
    END AS status
    FROM elections
    WHERE id = ?
");

$stmt->bind_param("i", $election_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: elections.php");
    exit();
}

$election = $result->fetch_assoc();
$stmt->close();

if ($election['status'] != 'active') {
    header("Location: elections.php");
    exit();
}

// Check if the user has already voted
$stmt = $conn->prepare("
    SELECT candidate_id
    FROM votes
    WHERE user_id = ? AND election_id = ?
");

$stmt->bind_param("ii", $user_id, $election_id);
$stmt->execute();

$vote_result = $stmt->get_result();

$already_voted = $vote_result->num_rows > 0;
$voted_for = null;

if ($already_voted) {
    $vote = $vote_result->fetch_assoc();
    $voted_for = $vote['candidate_id'];
}

$stmt->close();

// Get candidates
$stmt = $conn->prepare("
    SELECT id, full_name, symbol, manifesto, photo, achievements
    FROM candidates
    WHERE election_id = ?
    ORDER BY id ASC
");

$stmt->bind_param("i", $election_id);
$stmt->execute();

$candidates = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Candidates - Vote</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">

<style>

.candidate-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 20px;
}

.candidate-card {
    padding: 20px;
}

.cand-photo,
.cand-photo-placeholder {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin-bottom: 12px;
}

.cand-photo {
    object-fit: cover;
    background: var(--blue-light);
}

.cand-photo-placeholder {
    background: var(--blue-light);
    color: var(--blue);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 600;
}

.cand-name {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 3px;
}

.cand-symbol {
    display: inline-block;
    font-size: 11.5px;
    color: var(--text-muted);
    background: var(--bg);
    padding: 4px 10px;
    border-radius: 30px;
    margin-bottom: 10px;
}

.cand-manifesto {
    font-size: 12.5px;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 14px;
}

.cand-achievements {
    font-size: 12px;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 14px;
    padding-top: 8px;
    border-top: 1px solid var(--border);
}

.cand-achievements strong {
    display: block;
    color: var(--text);
    margin-bottom: 3px;
}

.voted-tag {
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--green-light);
    color: var(--green);
    font-size: 12px;
    font-weight: 600;
    padding: 8px;
    border-radius: 8px;
    width: 100%;
}

.empty-candidates {
    grid-column: 1 / -1;
    text-align: center;
    padding: 35px;
}

@media (max-width: 900px) {
    .candidate-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .candidate-grid {
        grid-template-columns: 1fr;
    }
}

</style>

</head>

<body
data-page="elections"
data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">

<div id="layout-root"></div>

<main class="main">

<div class="main-inner">

    <h1 class="page-title">
        <?php echo htmlspecialchars($election['title']); ?>
    </h1>

    <p class="page-subtitle">

        <?php
        if ($already_voted) {
            echo "You've already cast your vote in this election.";
        } else {
            echo "Choose a candidate below to cast your vote.";
        }
        ?>

    </p>


    <?php if ($already_voted): ?>

        <div class="message success">

            You voted already — thank you for participating.

            <a href="results.php?election_id=<?php echo $election_id; ?>">
                View results
            </a>

        </div>

    <?php endif; ?>


    <div class="candidate-grid">

        <?php if ($candidates->num_rows == 0): ?>

            <div class="card empty-candidates">

                <p style="color: var(--text-faint); font-size: 13px;">
                    No candidates have been added to this election yet.
                </p>

            </div>

        <?php else: ?>

            <?php while ($candidate = $candidates->fetch_assoc()): ?>

                <div class="card candidate-card">

                    <?php if (!empty($candidate['photo'])): ?>

                        <img
                            class="cand-photo"
                            src="../uploads/candidates/<?php echo htmlspecialchars($candidate['photo']); ?>"
                            alt="Candidate Photo"
                        >

                    <?php else: ?>

                        <div class="cand-photo-placeholder">

                            <?php
                            echo htmlspecialchars(
                                strtoupper(substr($candidate['full_name'], 0, 1))
                            );
                            ?>

                        </div>

                    <?php endif; ?>


                    <div class="cand-name">

                        <?php echo htmlspecialchars($candidate['full_name']); ?>

                    </div>


                    <div class="cand-symbol">

                        <?php echo htmlspecialchars($candidate['symbol']); ?>

                    </div>


                    <p class="cand-manifesto">

                        <?php

                        if (!empty($candidate['manifesto'])) {
                            echo htmlspecialchars($candidate['manifesto']);
                        } else {
                            echo "No manifesto provided.";
                        }

                        ?>

                    </p>


                    <?php if (!empty($candidate['achievements'])): ?>

                        <div class="cand-achievements">

                            <strong>Achievements</strong>

                            <?php
                            echo nl2br(
                                htmlspecialchars($candidate['achievements'])
                            );
                            ?>

                        </div>

                    <?php endif; ?>


                    <?php if ($already_voted): ?>

                        <?php if ($voted_for == $candidate['id']): ?>

                            <div class="voted-tag">
                                ✓ Your Vote
                            </div>

                        <?php else: ?>

                            <button
                                class="btn btn-outline btn-block"
                                disabled
                            >
                                Voting Closed
                            </button>

                        <?php endif; ?>

                    <?php else: ?>

                        <a href="vote.php?election_id=<?php echo $election_id; ?>&candidate_id=<?php echo $candidate['id']; ?>">

                            <button
                                type="button"
                                class="btn btn-primary btn-block"
                            >
                                Vote
                            </button>

                        </a>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        <?php endif; ?>

    </div>

</div>

</main>

<script src="../js/layout.js"></script>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>