
<?php
include '../includes/auth_check.php';
include '../config/db.php';

$user_id = (int)$_SESSION['user_id'];

// Get elections
$elections = $conn->query("
    SELECT id, title
    FROM elections
    ORDER BY id DESC
");


// Get only elections whose results are:
// 1. Completed
// 2. Made visible by admin

$visible_results = $conn->query("
    SELECT id, title
    FROM elections
    WHERE end_date < CURDATE()
    AND result_visible = 1
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - Vote</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">


<style>

.page-head {
    margin-bottom: 22px;
}

.welcome-card {
    padding: 26px 30px;
    margin-bottom: 22px;
}

.welcome-card h2 {
    font-size: 17px;
    margin-bottom: 4px;
}

.welcome-card p {
    font-size: 13px;
    color: var(--text-muted);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}

.stat-card {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon.blue {
    background: var(--blue-light);
    color: var(--blue);
}

.stat-icon.green {
    background: var(--green-light);
    color: var(--green);
}

.stat-label {
    color: var(--text-muted);
    font-size: 12px;
}

.stat-value {
    font-size: 15px;
    font-weight: 600;
}

.results {
    margin-bottom: 22px;
}

.result-card {
    padding: 20px;
    margin-bottom: 16px;
}

.result-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 15px;
}

.candidate {
    padding: 12px 0;
    border-top: 1px solid var(--border);
}

.candidate-name {
    font-size: 13px;
    font-weight: 600;
}

.candidate-votes {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 3px;
}

.bar {
    height: 7px;
    background: var(--bg);
    border-radius: 10px;
    margin-top: 7px;
}

.bar-fill {
    height: 100%;
    background: var(--blue);
    border-radius: 10px;
}

.total {
    text-align: right;
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 10px;
}

.section-card {
    padding: 22px;
}

.section-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 16px;
}

.announce-item {
    display: flex;
    gap: 12px;
}

.announce-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--blue);
    margin-top: 5px;
}

.announce-text {
    font-size: 13px;
}

.announce-time {
    font-size: 11px;
    color: var(--text-faint);
}


@media (max-width: 640px) {

    .stats-row {
        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body
    data-page="dashboard"
    data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>"
>


<div id="layout-root"></div>


<main class="main">

<div class="main-inner">


    <!-- PAGE HEADER -->

    <div class="page-head">

        <h1 class="page-title">
            Dashboard
        </h1>

        <p class="page-subtitle">
            Overview of your account
        </p>

    </div>


    <!-- WELCOME -->

    <div class="card welcome-card">

        <h2>

            Welcome back,
            <?php echo htmlspecialchars($_SESSION['full_name']); ?>

        </h2>


        <p>

            You're logged in to Vote as
            <?php echo htmlspecialchars($_SESSION['email']); ?>

        </p>


        <a
            href="elections.php"
            style="display:inline-block;margin-top:14px;"
        >

            <button class="btn btn-primary">
                View Elections
            </button>

        </a>

    </div>


    <!-- STATISTICS -->

    <div class="stats-row">


        <!-- ACCOUNT STATUS -->

        <div class="card stat-card">

            <div class="stat-icon green">
                ✓
            </div>


            <div>

                <div class="stat-label">
                    Account Status
                </div>

                <div class="stat-value">
                    Active
                </div>

            </div>

        </div>


        <!-- AVAILABLE ELECTIONS -->

        <div class="card stat-card">

            <div class="stat-icon blue">
                ⚑
            </div>


            <div>

                <div class="stat-label">
                    Available Elections
                </div>

                <div class="stat-value">

                    <?php
                    echo $elections->num_rows;
                    ?>

                </div>

            </div>

        </div>


    </div>


    <!-- ELECTION RESULTS -->

    <div class="results">


        <h2 class="section-title">
            Election Results
        </h2>


        <?php if ($visible_results->num_rows > 0): ?>


            <?php while ($election = $visible_results->fetch_assoc()): ?>


                <?php

                $id = (int)$election['id'];


                // Get total votes

                $total_result = $conn->query("
                    SELECT COUNT(*) AS total
                    FROM votes
                    WHERE election_id = $id
                ");


                $total = (int)$total_result->fetch_assoc()['total'];


                // Get candidate results

                $candidates = $conn->query("
                    SELECT
                        c.full_name,
                        c.symbol,
                        COUNT(v.id) AS votes
                    FROM candidates c

                    LEFT JOIN votes v
                    ON c.id = v.candidate_id

                    WHERE c.election_id = $id

                    GROUP BY c.id

                    ORDER BY votes DESC
                ");

                ?>


                <!-- RESULT CARD -->

                <div class="card result-card">


                    <div class="result-title">

                        <?php
                        echo htmlspecialchars(
                            $election['title']
                        );
                        ?>

                    </div>


                    <?php while (
                        $candidate =
                        $candidates->fetch_assoc()
                    ): ?>


                        <?php

                        $votes =
                            (int)$candidate['votes'];


                        if ($total > 0) {

                            $percent =
                                ($votes / $total) * 100;

                        } else {

                            $percent = 0;

                        }

                        ?>


                        <div class="candidate">


                            <div class="candidate-name">

                                <?php
                                echo htmlspecialchars(
                                    $candidate['full_name']
                                );
                                ?>

                            </div>


                            <div class="candidate-votes">

                                <?php
                                echo htmlspecialchars(
                                    $candidate['symbol']
                                );
                                ?>

                                &nbsp; — &nbsp;

                                <?php
                                echo $votes;
                                ?>

                                votes

                            </div>


                            <div class="bar">

                                <div
                                    class="bar-fill"
                                    style="width:<?php echo $percent; ?>%;"
                                >
                                </div>

                            </div>


                        </div>


                    <?php endwhile; ?>


                    <div class="total">

                        Total Votes:

                        <strong>
                            <?php echo $total; ?>
                        </strong>

                    </div>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <!-- NO RESULTS AVAILABLE -->

            <div class="card result-card">

                <p style="
                    font-size:13px;
                    color:var(--text-muted);
                ">

                    No election results have been published yet.

                </p>

            </div>


        <?php endif; ?>


    </div>


    <!-- ANNOUNCEMENTS -->

    <div class="card section-card">


        <div class="section-title">
            Announcements
        </div>


        <div class="announce-item">


            <div class="announce-dot"></div>


            <div>


                <div class="announce-text">

                    Welcome to Vote — your account has been
                    created successfully.

                </div>


                <div class="announce-time">
                    Just now
                </div>


            </div>


        </div>


    </div>


</div>

</main>


<script src="../js/layout.js"></script>


</body>

</html>


<?php

$conn->close();

?>