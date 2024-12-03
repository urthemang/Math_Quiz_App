<?php

header('Content-Type: application/json');


include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $data = json_decode(file_get_contents('php://input'), true);

    $username = $data['username'];
    $correct_answers = (int) $data['correct_answers'];
    $wrong_answers = (int) $data['wrong_answers'];

  
    if (!empty($username) && $correct_answers >= 0 && $wrong_answers >= 0) {
     
        $stmt = $conn->prepare("INSERT INTO results (username, correct_answers, wrong_answers) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $username, $correct_answers, $wrong_answers);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Results saved successfully!']);
        } else {
            echo json_encode(['message' => 'Failed to save results.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid data provided.']);
    }
} else {
    echo json_encode(['message' => 'Invalid request method.']);
}

$conn->close();
?>
