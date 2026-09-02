<?php session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    http_response_code(403);
    die("Access Denied. Only employers can view this dashboard. <a href='index.php'>Return Home</a>");
}
$employer_id = $_SESSION['user_id'];
$status_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_job') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_STRING);
    $stmt = $pdo->prepare("SELECT company_name FROM users WHERE id = ?");
    $stmt->execute([$employer_id]);
    $company_name = $stmt->fetchColumn();
    $initials = strtoupper(substr($company_name, 0, 2));
    $logo_url = "https://placehold.co/100x100/1e293b/ffffff?text=" . urlencode($initials);
    if ($title && $description) {
        $insert_stmt = $pdo->prepare("
            INSERT INTO jobs (employer_id, title, company, description, tags, salary, logo_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        try {
            $insert_stmt->execute([$employer_id, $title, $company_name, $description, $tags, $salary, $logo_url]);
            $status_msg = "<div class='alert success'>Job posted successfully!</div>";
        } catch (PDOException $e) {
            $status_msg = "<div class='alert error'>Failed to post job. Please try again.</div>";
            error_log($e->getMessage()); // Log internal error, don't expose to UI
        }
    }
}
$jobs_stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY posted_at DESC");
$jobs_stmt->execute([$employer_id]);
$my_jobs = $jobs_stmt->fetchAll();
$apps_stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title 
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.employer_id = ?
    ORDER BY a.applied_at DESC");
$apps_stmt->execute([$employer_id]);
$applications = $apps_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard | Remotely</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --primary-hover: #1e293b; --accent: #4f46e5; --bg: #f1f5f9; --text: #334155; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); line-height: 1.6; }
        .navbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .navbar a { text-decoration: none; color: var(--accent); font-weight: 600; }
        .dashboard-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .card h2 { margin-bottom: 20px; color: var(--primary); font-size: 1.25rem; border-bottom: 2px solid var(--bg); padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); }
        .btn { background: var(--accent); color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; }
        .btn:hover { background: #4338ca; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th, .data-table td { text-align: left; padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        .data-table th { background: #f8fafc; font-weight: 600; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; }
        .alert.success { background: #d1fae5; color: #065f46; }
        .alert.error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div style="font-weight: 700; font-size: 1.2rem; color: var(--primary);">Employer Workspace</div>
        <div>
            <span><?= htmlspecialchars($_SESSION['email']) ?></span> | 
            <a href="index.php">View Portal</a> | 
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    <div class="dashboard-container">
        <div class="card">
            <h2>Post a New Role</h2>
            <?= $status_msg ?>
            <form method="POST" action="">
                <input type="hidden" name="action" value="post_job">
                <div class="form-group">
                    <label>Job Title</label>
                    <input type="text" name="title" required placeholder="e.g. Senior DevOps Engineer">
                </div>
                <div class="form-group">
                    <label>Tags (Comma separated)</label>
                    <input type="text" name="tags" placeholder="Remote, AWS, Docker">
                </div>          
                <div class="form-group">
                    <label>Salary Range</label>
                    <input type="text" name="salary" placeholder="$120k - $150k">
                </div>         
                <div class="form-group">
                    <label>Job Description</label>
                    <textarea name="description" rows="6" required placeholder="Describe the responsibilities and requirements..."></textarea>
                </div>
                <button type="submit" class="btn">Publish Job</button>
            </form>
        </div>
        <div>
            <div class="card" style="margin-bottom: 30px;">
                <h2>Applicant Pipeline</h2>
                <?php if (count($applications) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Role</th>
                                <th>Contact</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($applications as $app): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($app['applicant_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($app['job_title']) ?></td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($app['applicant_email']) ?>">Email</a>
                                        <?php if($app['portfolio_link']): ?>
                                            | <a href="<?= htmlspecialchars($app['portfolio_link']) ?>" target="_blank">Portfolio</a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d', strtotime($app['applied_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text); font-size: 0.9rem;">No applications received yet.</p>
                <?php endif; ?>
            </div>
            <div class="card">
                <h2>Your Active Postings</h2>
                <?php if (count($my_jobs) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Posted On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($my_jobs as $job): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                                    <td><?= date('M d, Y', strtotime($job['posted_at'])) ?></td>
                                    <td><a href="job.php?id=<?= $job['id'] ?>" target="_blank" style="color: var(--accent);">View Public</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text); font-size: 0.9rem;">You haven't posted any jobs yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>