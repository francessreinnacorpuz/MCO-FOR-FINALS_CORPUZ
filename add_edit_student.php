<?php
include 'db.php';

$id = $_GET['id'] ?? null;
$name = $email = $course = "";
$edit_mode = false;

if ($id) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $name = $student['name'];
        $email = $student['email'];
        $course = $student['course'];
    } else {
        echo "Student not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $course = $_POST['course'] ?? '';

    if ($edit_mode) {
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, course=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $course, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, email, course) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $course);
        $stmt->execute();
    }
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $edit_mode ? 'Edit' : 'Add' ?> Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container mt-4">
    <h2><?= $edit_mode ? 'Edit' : 'Add' ?> Student</h2>
    <form method="post" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($name) ?>" />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($email) ?>" />
        </div>
        <div class="mb-3">
            <label for="course" class="form-label">Course</label>
            <input type="text" class="form-control" id="course" name="course" required value="<?= htmlspecialchars($course) ?>" />
        </div>
        <button type="submit" class="btn btn-success"><?= $edit_mode ? 'Update' : 'Add' ?> Student</button>
        <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>
