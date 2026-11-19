<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['full_name']);
    $year = trim($_POST['year_level']);
    $block = trim($_POST['block']);

    if (empty($name) || empty($year) || empty($block)) {
        $error = "❌ Please fill in all fields.";
    } else {
        // I-check kung nakaboto na sila
        $stmt = mysqli_prepare($conn, "SELECT id, has_voted FROM students WHERE full_name = ? AND year_level = ? AND block = ?");
        mysqli_stmt_bind_param($stmt, "sss", $name, $year, $block);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['has_voted'] == 1) {
                $error = "⚠️ You have already voted. Thank you!";
            } else {
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $name;
                $_SESSION['student_year'] = $year;
                $_SESSION['student_block'] = $block;
                header("Location: vote.php");
                exit;
            }
        } else {
            // Automatic na ilalagay sa listahan kung bago
            $stmt = mysqli_prepare($conn, "INSERT INTO students (full_name, year_level, block, has_voted) VALUES (?, ?, ?, 0)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $year, $block);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['student_id'] = mysqli_insert_id($conn);
                $_SESSION['student_name'] = $name;
                $_SESSION['student_year'] = $year;
                $_SESSION['student_block'] = $block;
                header("Location: vote.php");
                exit;
            } else {
                $error = "❌ Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; display: flex; justify-content: center; padding-top: 80px; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px #ccc; width: 360px; }
        h2 { text-align: center; color: #1e40af; margin-top: 0; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; font-size: 16px; margin-top: 25px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .error { color: red; text-align: center; margin-top: 15px; }
        a { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>👋 Welcome!</h2>
        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="full_name" placeholder="Enter your full name" required>

            <label>Year Level:</label>
            <select name="year_level" required>
                <option value="">-- Select Year --</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select>

            <label>Section / Block:</label>
            <select name="block" required>
                <option value="">-- Select Section --</option>
                <option value="A">Section A</option>
                <option value="B">Section B</option>
                <option value="C">Section C</option>
                <option value="D">Section D</option>
            </select>

            <button type="submit">View Candidates & Vote →</button>
            <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        </form>
        <a href="index.php">&larr; Back to Home</a>
    </div>
</body>
</html>
