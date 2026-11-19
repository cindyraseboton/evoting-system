<!DOCTYPE html>
<html>
<head>
    <title>School E-Voting System</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; text-align: center; padding-top: 80px; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px #ccc; max-width: 500px; margin: auto; }
        h1 { color: #1e40af; }
        .btn { display: inline-block; padding: 12px 30px; margin: 10px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 16px; }
        .btn:hover { background: #1d4ed8; }
        .btn.green { background: #16a34a; }
        .btn.green:hover { background: #15803d; }
        .admin-link { margin-top: 30px; font-size: 13px; color: #888; }
        .admin-link a { color: #888; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗳️ SCHOOL E-VOTING SYSTEM</h1>
        <p>Welcome! Please choose what to do:</p>
        
        <a href="login.php" class="btn">🗳️ Student — Cast Your Vote</a>
        <a href="results.php" class="btn green">📊 View Election Results</a>
        
        <div class="admin-link">
            <a href="admin/index.php">🔧 Admin Login — Add Students & Candidates</a>
        </div>
    </div>
</body>
</html>
