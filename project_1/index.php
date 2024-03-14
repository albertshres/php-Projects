<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'todoList';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Function to retrieve tasks from the database
function getTasks($conn)
{
  $result = $conn->query("SELECT * FROM tasks");
  $tasks = [];

  while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
  }

  return $tasks;
}

// Display tasks
$tasks = getTasks($conn);

// Add task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $taskName = $_POST['task'];

  $sql = "INSERT INTO tasks (task_name) VALUES ('$taskName')";

  if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Edit task
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
  $taskId = $_GET['edit'];

  // Fetch the task from the database
  $result = $conn->query("SELECT * FROM tasks WHERE id = $taskId");

  if ($result->num_rows > 0) {
    $task = $result->fetch_assoc();
  } else {
    echo "Task not found";
    exit;
  }
}

// Update task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $taskId = $_POST['id'];
  $taskName = $_POST['task'];

  $sql = "UPDATE tasks SET task_name = '$taskName' WHERE id = $taskId";

  if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Delete task
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
  $taskId = $_GET['delete'];

  $sql = "DELETE FROM tasks WHERE id = $taskId";

  if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Todo List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      /* Updated to align content at the top-left corner */
      padding-left: 20px;
      /* Add left padding for better readability */
    }

    h1 {
      color: #333;
    }

    form {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      /* Updated to align form items at the top-left corner */
    }

    label {
      margin-bottom: 10px;
      font-size: 16px;
    }

    input {
      padding: 8px;
      margin-bottom: 10px;
    }

    button {
      padding: 10px;
      background-color: #4caf50;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #45a049;
    }

    ul {
      list-style-type: none;
      padding: 0;
    }

    li {
      background-color: #fff;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    a {
      text-decoration: none;
      color: #333;
      margin-left: 10px;
      padding: 5px;
      border: 1px solid #333;
      border-radius: 3px;
    }

    a:hover {
      background-color: #333;
      color: #fff;
    }

    form.edit-form {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      /* Updated to align form items at the top-left corner */
    }
  </style>
</head>

<body>
  <h1>Todo List</h1>

  <form action="index.php" method="post">
    <label for="task">New Task:</label>
    <input type="text" name="task" required>
    <button type="submit">Add Task</button>
  </form>

  <ul>
    <?php foreach ($tasks as $task): ?>
      <li>
        <?php echo $task['task_name']; ?>
        <a href="index.php?edit=<?php echo $task['id']; ?>">Edit</a>
        <a href="index.php?delete=<?php echo $task['id']; ?>">Delete</a>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if (isset($task)): ?>
    <h2>Edit Task</h2>
    <form action="index.php" method="post">
      <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
      <label for="task">Task:</label>
      <input type="text" name="task" value="<?php echo $task['task_name']; ?>" required>
      <button type="submit" name="update">Update Task</button>
    </form>
  <?php endif; ?>
</body>

</html>