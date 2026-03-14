<?php
require_once __DIR__ . '/../../../config/config.php';

// SQL to create position_competencies table
$sql = "CREATE TABLE IF NOT EXISTS position_competencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_id INT NOT NULL,
    competency_id INT NOT NULL,
    level_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (position_id) REFERENCES positions(PositionID),
    FOREIGN KEY (competency_id) REFERENCES competencies(id),
    FOREIGN KEY (level_id) REFERENCES competency_levels(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table position_competencies created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?>
