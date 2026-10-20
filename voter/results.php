
<?php
include '../includes/auth_check.php';
include '../config/db.php';

$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;


// Get election information
$stmt = $conn->prepare("
    SELECT 
        title,
        CASE
            WHEN CURDATE() < start_date THEN 'upcoming'
            WHEN CURDATE() > end_date THEN 'completed'
            ELSE 'active'
        END AS status,
        result_visible
    FROM elections
    WHERE id = ?
");

$stmt->bind_param("i", $election_id);
$stmt->execute();

$election_result = $stmt->get_result();


// If election does not exist
if ($election_result->num_rows === 0) {
    header("Location: elections.php");
    exit();
}

$election = $election_result->fetch_assoc();

$stmt->close();


// Results can be viewed only when:
// 1. Election is completed
// 2. Admin has made results visible

$can_view = (
    $election['status'] === 'completed' &&
    $election['result_visible'] == 1
);


$results = [];
$total_votes = 0;


// Get results only when they are allowed to be viewed
if ($can_view) {

    $stmt = $conn->prepare("
        SELECT 
            c.full_name,
            c.symbol,
            COUNT(v.id) AS vote_count
        FROM candidates c
        LEFT JOIN votes v 
            ON v.candidate_id = c.id
        WHERE c.election_id = ?
        GROUP BY c.id
        ORDER BY vote_count DESC
    ");

    $stmt->bind_param("i", $election_id);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();

    $total_votes = array_sum(
        array_column($results, 'vote_count')
    );
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Election Results</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">

<style>

.result-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
}

.result-row:first-child {
    padding-top: 0;
}

.result-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.result-name {
    font-weight: 600;
    font-size: 13.5px;
}

.result-symbol {
    font-size: 12px;
    color: var(--text-muted);
}

.result-bar-track {
    background: var(--bg);
    border-radius: 30px;
    height: 8px;
    margin-top: 6px;
    overflow: hidden;
}

.result-bar-fill {
    background: var(--blue);
    height: 100%;
    border-radius: 30px;
}

.result-count {
    font-weight: 600;
    font-size: 14px;
    min-width: 60px;
    text-align: right;
}

.locked-card {
    padding: 40px 30px;
    text-align: center;
    max-width: 450px;
    margin: 20px auto 0;
}

</style>

</head>


<body
    data-page="elections"
    data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>"
>


<div id="layout-root"></div>


<main class="main">

    <div class="main-inner">


        <!-- Election Title -->

        <h1 class="page-title">
            <?php echo htmlspecialchars($election['title']); ?>
        </h1>


        <!-- Page Subtitle -->

        <p class="page-subtitle">
            Election Results
        </p>


        <?php if (!$can_view): ?>


            <!-- Results Hidden -->

            <div class="card locked-card">

                <p style="
                    margin-bottom: 0;
                    color: var(--text-muted);
                    font-size: 13px;
                    line-height: 1.6;
                ">

                    Results are not available yet.
                    The administrator will publish the results
                    after the election is completed.

                </p>

            </div>


        <?php else: ?>


            <!-- Results Visible -->

            <div class="card section-card">


                <?php if (empty($results)): ?>

                    <p style="
                        color: var(--text-muted);
                        text-align: center;
                    ">

                        No votes have been recorded.

                    </p>


                <?php else: ?>


                    <?php foreach ($results as $r):

                        $pct = $total_votes > 0
                            ? round(($r['vote_count'] / $total_votes) * 100)
                            : 0;

                    ?>


                        <div class="result-row">


                            <div style="flex: 1;">

                                <div class="result-name">

                                    <?php
                                    echo htmlspecialchars(
                                        $r['full_name']
                                    );
                                    ?>

                                </div>


                                <div class="result-symbol">

                                    <?php
                                    echo htmlspecialchars(
                                        $r['symbol']
                                    );
                                    ?>

                                </div>


                                <div class="result-bar-track">

                                    <div
                                        class="result-bar-fill"
                                        style="width: <?php echo $pct; ?>%;"
                                    ></div>

                                </div>

                            </div>


                            <div class="result-count">

                                <?php
                                echo (int)$r['vote_count'];
                                ?>

                                votes

                            </div>


                        </div>


                    <?php endforeach; ?>


                <?php endif; ?>


            </div>


        <?php endif; ?>


    </div>

</main>


<script src="../js/layout.js"></script>

</body>

</html>
