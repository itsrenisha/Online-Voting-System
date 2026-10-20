<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';


// Make results visible or hidden
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $election_id = $_POST['election_id'];
    $result_visible = $_POST['result_visible'];

    $stmt = $conn->prepare(
        "UPDATE elections SET result_visible = ? WHERE id = ?"
    );

    $stmt->bind_param("ii", $result_visible, $election_id);
    $stmt->execute();

    header("Location: elections.php");
    exit();
}


// Get all elections
$result = $conn->query("
    SELECT id, title, start_date, end_date, result_visible,
    CASE
        WHEN CURDATE() < start_date THEN 'upcoming'
        WHEN CURDATE() > end_date THEN 'completed'
        ELSE 'active'
    END AS status
    FROM elections
    ORDER BY id DESC
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Elections - Vote</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../css/style.css">

</head>


<body
data-page="elections"
data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>"
>

<div id="layout-root"></div>


<main class="main">

<div class="main-inner">


    <div class="page-head">

        <div>

            <h1 class="page-title">
                Manage Elections
            </h1>

            <p class="page-subtitle">
                Create and manage voting polls
            </p>

        </div>


        <a href="add_election.php">

            <button class="btn btn-primary">
                + Create Election
            </button>

        </a>

    </div>


    <div class="card table-card">

        <table class="data-table">

            <thead>

                <tr>

                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Results</th>
                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>


            <?php if ($result->num_rows > 0): ?>


                <?php while ($row = $result->fetch_assoc()): ?>


                    <tr>


                        <td>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </td>


                        <td>
                            <?php echo htmlspecialchars($row['start_date']); ?>
                        </td>


                        <td>
                            <?php echo htmlspecialchars($row['end_date']); ?>
                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php if ($row['status'] == 'active'): ?>

                                <span class="badge badge-green">
                                    <span class="badge-dot"></span>
                                    Active
                                </span>

                            <?php elseif ($row['status'] == 'upcoming'): ?>

                                <span class="badge badge-amber">
                                    <span class="badge-dot"></span>
                                    Upcoming
                                </span>

                            <?php else: ?>

                                <span class="badge badge-blue">
                                    <span class="badge-dot"></span>
                                    Completed
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- RESULTS -->

                        <td>

                            <?php if ($row['status'] == 'completed'): ?>


                                <?php if ($row['result_visible'] == 0): ?>

                                    <form method="POST">

                                        <input
                                            type="hidden"
                                            name="election_id"
                                            value="<?php echo $row['id']; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="result_visible"
                                            value="1"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            Make Results Visible
                                        </button>

                                    </form>


                                <?php else: ?>

                                    <form method="POST">

                                        <input
                                            type="hidden"
                                            name="election_id"
                                            value="<?php echo $row['id']; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="result_visible"
                                            value="0"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-outline"
                                        >
                                            Hide Results
                                        </button>

                                    </form>

                                <?php endif; ?>


                            <?php else: ?>

                                <span style="font-size:12px;color:var(--text-faint);">
                                    After election ends
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- ACTIONS -->

                        <td>

                            <div class="table-actions">

                                <a
                                    class="action-edit"
                                    href="edit_election.php?id=<?php echo $row['id']; ?>"
                                >
                                    Edit
                                </a>


                                <a
                                    class="action-delete"
                                    href="delete_election.php?id=<?php echo $row['id']; ?>"
                                    onclick="return confirm('Delete this election? This also removes its candidates.');"
                                >
                                    Delete
                                </a>

                            </div>

                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr class="empty-row">

                    <td colspan="6">
                        No elections created yet.
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