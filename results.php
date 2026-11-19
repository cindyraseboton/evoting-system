<?php
include "config.php";

// Kunin ang lahat ng posisyon
$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Election Results</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        h1 { text-align: center; color: #16a34a; margin-bottom: 30px; }
        .position-title { background: #2563eb; color: white; padding: 12px; border-radius: 6px; font-size: 18px; font-weight: bold; margin-top: 30px; }
        .candidate-row { display: flex; align-items: center; background: white; padding: 15px; border-bottom: 1px solid #e5e7eb; }
        .candidate-photo { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 20px; }
        .candidate-name { flex: 1; font-size: 16px; font-weight: 500; }
        .vote-bar-container { flex: 2; height: 24px; background: #e5e7eb; border-radius: 12px; margin: 0 15px; overflow: hidden; }
        .vote-bar { height: 100%; background: #16a34a; border-radius: 12px; transition: width 0.3s; }
        .vote-count { width: 80px; text-align: right; font-weight: bold; color: #374151; }
        .back-link { text-align: center; margin-top: 40px; }
        .back-link a { background: #2563eb; color: white; padding: 10px 25px; border-radius: 5px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Election Results</h1>
        
        <?php while ($pos = mysqli_fetch_assoc($positions)): ?>
            <div class="position-title"><?= htmlspecialchars($pos['position_name']) ?></div>
            
            <?php
            $pos_id = $pos['id'];
            $candidates = mysqli_query($conn, "SELECT * FROM candidates WHERE position_id = $pos_id ORDER BY votes DESC");
            $total_votes = 0;
            $cand_list = [];
            while ($c = mysqli_fetch_assoc($candidates)) {
                $cand_list[] = $c;
                $total_votes += $c['votes'];
            }
            ?>
            
            <?php if (empty($cand_list)): ?>
                <div style="background:white; padding:20px; color:#888; text-align:center;">No candidates yet.</div>
            <?php else: ?>
                <?php foreach ($cand_list as $c): 
                    $percent = $total_votes > 0 ? round(($c['votes'] / $total_votes) * 100, 1) : 0;
                    $photo = !empty($c['photo']) && file_exists("photos/" . $c['photo']) ? "photos/" . $c['photo'] : "photos/default.jpg";
                ?>
                    <div class="candidate-row">
                        <img src="<?= $photo ?>" class="candidate-photo" alt="">
                        <div class="candidate-name"><?= htmlspecialchars($c['full_name']) ?></div>
                        <div class="vote-bar-container">
                            <div class="vote-bar" style="width: <?= $percent ?>%"></div>
                        </div>
                        <div class="vote-count"><?= $c['votes'] ?> votes (<?= $percent ?>%)</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endwhile; ?>
        
        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
