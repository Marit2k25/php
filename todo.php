<?php
// Database connection
$conn = new mysqli("localhost", "root", "root", "todo_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task VARCHAR(255) NOT NULL,
    status TINYINT(1) DEFAULT 0
)");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["task"])) {
        $task = $conn->real_escape_string($_POST["task"]);
        $conn->query("INSERT INTO tasks (task) VALUES ('$task')");
    } elseif (isset($_POST["delete_id"])) {
        $conn->query("DELETE FROM tasks WHERE id=" . (int)$_POST["delete_id"]);
    } elseif (isset($_POST["complete_id"])) {
        $conn->query("UPDATE tasks SET status=1 WHERE id=" . (int)$_POST["complete_id"]);
    }
}

// Fetch tasks
$tasks = $conn->query("SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background-color: #f9f9f9;
        }
        .container { 
            width: 400px; 
            margin: 50px auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        ul { 
            list-style: none; 
            padding: 0; 
            margin: 20px 0;
            border: 2px solid #ddd; 
            border-radius: 5px;
            overflow: hidden;
        }
        li { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 10px; 
            border-bottom: 1px solid #ddd;
            background: #ffffff;
        }
        li:last-child {
            border-bottom: none;
        }
        .done { 
            text-decoration: line-through; 
            color: gray;
        }
        button { 
            border: none; 
            padding: 5px 10px; 
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        .delete-btn { 
            background: #ff5c5c; 
            color: white;
        }
        .delete-btn:hover {
            background: #e04a4a;
        }
        .complete-btn { 
            background: #4caf50; 
            color: white;
        }
        .complete-btn:hover {
            background: #45a049;
        }
        input[type="text"] { 
            padding: 8px; 
            width: 70%; 
            border: 1px solid #ccc; 
            border-radius: 5px;
        }
        .add-btn {
            background: #007BFF;
            color: white;
            padding: 8px 12px;
        }
        .add-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>To-Do List</h2>
        <form method="POST">
            <input type="text" name="task" placeholder="New task" required>
            <button type="submit" class="add-btn">Add</button>
        </form>
        <ul>
            <?php while ($row = $tasks->fetch_assoc()): ?>
                <li class="<?php echo $row['status'] ? 'done' : ''; ?>">
                    <?php echo htmlspecialchars($row['task']); ?>
                    <div>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                        <?php if (!$row['status']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="complete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="complete-btn">Complete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
