<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    http_response_code(403);
    die("Access Denied. Only job seekers can view this dashboard. <a href='login.php'>Log In</a>");
}
$user_email = $_SESSION['email'];
$stmt = $pdo->prepare("
    SELECT a.applied_at, a.portfolio_link, j.title, j.company, j.logo_url, j.id as job_id 
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.applicant_email = ?
    ORDER BY a.applied_at DESC
");
$stmt->execute([$user_email]);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | Remotely</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text-main: #0f172a; --text-muted: #64748b; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text-main); line-height: 1.6; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: white; border-bottom: 1px solid #e2e8f0; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: var(--text-main); font-weight: 600; transition: color 0.2s; }
        .nav-links a:hover { color: var(--primary); }
        .container { max-width: 1000px; margin: 50px auto; padding: 0 20px; }
        .page-header { margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; }
        .page-header h1 { font-size: 2rem; color: var(--text-main); }
        .page-header p { color: var(--text-muted); }
        .app-list { display: flex; flex-direction: column; gap: 20px; }
        .app-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; }
        .app-info { display: flex; align-items: center; gap: 20px; }
        .app-info img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
        .job-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 5px; }
        .job-company { color: var(--text-muted); font-size: 0.95rem; }
        .status-badge { background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; display: inline-block; }
        .meta-info { text-align: right; }
        .date-applied { font-size: 0.9rem; color: var(--text-muted); margin-top: 10px; }
        .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1; }
        .empty-state h3 { margin-bottom: 10px; }
        .btn-primary { display: inline-block; margin-top: 15px; background: var(--primary); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <nav class="top-nav">
        <div style="font-weight: 700; font-size: 1.25rem; color: var(--primary);">Remotely</div>
        <div class="nav-links">
            <span style="color: #64748b; margin-right: 15px; font-size: 0.9rem;">
                <?= htmlspecialchars($_SESSION['email']) ?>
            </span>
            <a href="index.php">Find Jobs</a>
            <a href="logout.php">Log Out</a>
        </div>
    </nav>
    <main class="container">
        <div class="page-header">
            <h1>My Applications</h1>
            <p>Track the status of the remote roles you have applied for.</p>
        </div>
        <div class="app-list">
            <?php if (count($applications) > 0): ?>
                <?php foreach($applications as $app): ?>
                    <div class="app-card">
                        <div class="app-info">
                            <img src="<?= htmlspecialchars($app['logo_url']) ?>" alt="Logo">
                            <div>
                                <a href="job.php?id=<?= $app['job_id'] ?>" style="text-decoration: none; color: inherit;">
                                    <div class="job-title"><?= htmlspecialchars($app['title']) ?></div>
                                </a>
                                <div class="job-company"><?= htmlspecialchars($app['company']) ?></div>
                            </div>
                        </div>
                        <div class="meta-info">
                            <div class="status-badge">Application Under Review</div>
                            <div class="date-applied">Applied on <?= date('M d, Y', strtotime($app['applied_at'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>You haven't applied to any jobs yet!</h3>
                    <p style="color: var(--text-muted);">Your dream remote job is waiting for you.</p>
                    <a href="index.php" class="btn-primary">Browse Open Roles</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>