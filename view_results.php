<?php

include 'db.php';


$result = $conn->query("SELECT * FROM results ORDER BY quiz_date DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Quiz Results</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Correct Answers</th>
                <th>Wrong Answers</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo $row['correct_answers']; ?></td>
                    <td><?php echo $row['wrong_answers']; ?></td>
                    <td><?php echo $row['quiz_date']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
