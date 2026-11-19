<?php
session_start();
include "config.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // I-check kung nakaboto na
    $check = mysqli_query($conn, "SELECT has_voted FROM students WHERE id = $student_id");
    $row = mysqli_fetch_assoc($check);
    
    if ($row['has_voted'] == 1) {
        $error = "⚠️ You have already voted!";
    } else {
        // I-save ang mga boto
        foreach ($_POST['vote'] as $position_id => $candidate_id) {
            $candidate_id = intval($candidate_id);
            if ($candidate_id > 0) {
                mysqli_query($conn, "INSERT INTO votes (student_id, candidate_id) VALUES ($student_id, $candidate_id)");
                mysqli_query($conn, "UPDATE candidates SET votes = votes + 1 WHERE id = $candidate_id");
            }
        }
        
        // Markahan na nakaboto na
        mysqli_query($conn, "UPDATE students SET has_voted = 1 WHERE id = $student_id");
        
        $success = "✅ Thank you! Your vote has been recorded.";
        session_unset();
        session_destroy();
    }
}

// Kunin ang mga posisyon at kandidato
$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cast Your Vote</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px #ccc; }
        h2 { text-align: center; color: #1e40af; margin-top: 0; }
        h3 { color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px; margin-top: 30px; }
        .welcome { text-align: center; color: #4b5563; margin-bottom: 20px; }
        .candidate-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin: 15px 0; }
        .candidate-card { border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: 0.2s; }
        .candidate-card:hover { border-color: #2563eb; background: #eff6ff; }
        input:checked + label > .candidate-card { border-color: #2563eb; background: #dbeafe; }
        .candidate-photo { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; }
        .candidate-name { font-weight: bold; font-size: 15px; }
        input[type="radio"] { display: none; }
        button { background: #16a34a; color: white; border: none; padding: 14px 30px; font-size: 17px; border-radius: 6px; cursor: pointer; width: 100%; margin-top: 30px; }
        button:hover { background: #15803d; }
        .error { color: #dc2626; text-align: center; margin-top: 15px; font-size: 18px; }
        .success { color: #16a34a; text-align: center; margin-top: 15px; font-size: 18px; font-weight: bold; }
        .logout { text-align: right; margin-bottom: 10px; }
        .logout a { color: #6b7280; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">🚪 Logout</a>
        </div>
        
        <h2>🗳️ Cast Your Vote</h2>
        <p class="welcome">Welcome, <strong><?= htmlspecialchars($student_name) ?></strong>! Please select your candidates below.</p>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
            <div style="text-align:center; margin-top:30px;">
                <a href="index.php" style="background:#2563eb; color:white; padding:10px 20px; border-radius:5px; text-decoration:none;">Go to Home</a>
            </div>
        <?php elseif (isset($success)): ?>
            <div class="success"><?= $success ?></div>
            <div style="text-align:center; margin-top:30px;">
                <a href="index.php" style="background:#2563eb; color:white; padding:10px 20px; border-radius:5px; text-decoration:none;">Go to Home</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <?php while ($pos = mysqli_fetch_assoc($positions)): ?>
                    <h3><?= htmlspecialchars($pos['position_name']) ?></h3>
                    <div class="candidate-grid">
                        <?php
                        $pos_id = $pos['id'];
                        $candidates = mysqli_query($conn, "SELECT * FROM candidates WHERE position_id = $pos_id ORDER BY full_name");
                        $has_candidates = false;
                        while ($cand = mysqli_fetch_assoc($candidates)):
                            $has_candidates = true;
                            $photo_path = !empty($cand['photo']) && file_exists("photos/" . $cand['photo']) 
                                ? "photos/" . $cand['photo'] 
                                : "photos/default.jpg";
                        ?>
                            <input type="radio" name="vote[<?= $pos_id ?>]" id="cand-<?= $cand['id'] ?>" value="<?= $cand['id'] ?>" required>
                            <label for="cand-<?= $cand['id'] ?>">
                                <div class="candidate-card">
                                    <img src="<?= $photo_path ?>" alt="<?= htmlspecialchars($cand['full_name']) ?>" class="candidate-photo">
                                    <div class="candidate-name"><?= htmlspecialchars($cand['full_name']) ?></div>
                                </div>
                            </label>
                        <?php endwhile; ?>
                        <?php if (!$has_candidates): ?>
                            <p style="color:#888;">No candidates added yet for this position.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                
                <button type="submit">✅ Submit My Vote</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
