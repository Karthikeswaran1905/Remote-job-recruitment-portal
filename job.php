<?php require_once 'db.php';
$job_id = $_GET['id'] ?? null;
if (!$job_id) {
    die("Invalid Job ID. <a href='index.php'>Go back</a>");
}
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();
if (!$job) {
    die("Job not found. <a href='index.php'>Go back</a>");
}
$application_status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $portfolio = $_POST['portfolio'] ?? '';
    $cover_letter = $_POST['cover_letter'] ?? '';
    if (!empty($name) && !empty($email)) {
        try {
            $insert_stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_name, applicant_email, portfolio_link, cover_letter) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->execute([$job_id, $name, $email, $portfolio, $cover_letter]);          
            $application_status = 'success';
        } catch (PDOException $e) {
            $application_status = 'error';
            $error_msg = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> at <?= htmlspecialchars($job['company']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }
        * { 
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body { 
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
        }
        .nav {
            padding: 20px;
            background: white;
            border-bottom: 1px solid #e2e8f0; }
        .nav a { 
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            transition: color 0.2s;
        }
        .nav a:hover {
             color: var(--primary);
        }
        .job-detail-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.5s ease-out;
        }
        .header-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .header-row img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
        }
        .job-title {
            font-size: 2rem;
            font-weight: 700;
        }
        .company-name {
            font-size: 1.1rem;
            color: var(--text-muted);
        }
        .meta-tags {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta-tag {
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .content {
            margin-bottom: 40px;
            font-size: 1.05rem;
            color: #334155;
        } 
        .btn-large {
            display: block;
            width: 100%;
            padding: 15px;
            background: var(--primary); 
            color: white; border: none;
            border-radius: 8px;
            font-size: 1.1rem; 
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-align: center;
        }
        .btn-large:hover {
            background: var(--primary-hover);
        }
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7);
            display: none; 
            justify-content: center;
            align-items: center; z-index: 1000;
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            background: white; width: 100%; max-width: 500px;
            border-radius: 12px; padding: 30px; position: relative;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        .close-btn {
            position: absolute;
            top: 15px; right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-muted);
        }
        .modal-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .form-group input, .form-group textarea {
            width: 100%; padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
        }
        .form-group input:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
        @keyframes fadeIn { from {
            opacity: 0;
            transform: translateY(10px);
        } to {
            opacity: 1;
            transform: translateY(0);
        } }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="index.php">&larr; Back to all jobs</a>
    </nav>
    <main class="job-detail-container">
        <?php if ($application_status === 'success'): ?>
            <div class="alert alert-success">
                🎉 Your application has been submitted successfully! The team will review it shortly.
            </div>
        <?php elseif ($application_status === 'error'): ?>
            <div class="alert alert-error">
                Something went wrong: <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>
        <div class="header-row">
            <img src="<?= htmlspecialchars($job['logo_url']) ?>" alt="Logo">
            <div>
                <h1 class="job-title"><?= htmlspecialchars($job['title']) ?></h1>
                <div class="company-name"><?= htmlspecialchars($job['company']) ?></div>
            </div>
        </div>
        <div class="meta-tags">
            <div class="meta-tag">💰 <?= htmlspecialchars($job['salary']) ?></div>
            <div class="meta-tag">🌍 <?= htmlspecialchars($job['tags']) ?></div>
            <div class="meta-tag">📅 Posted: <?= date('F j, Y', strtotime($job['posted_at'])) ?></div>
        </div>
        <div class="content">
            <h3>About the Role</h3>
            <br>
            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
            <br>
            <p><strong>Requirements:</strong> Experience in scalable systems, self-driven work ethic, and excellent communication skills for remote collaboration.</p>
        </div>
        <button class="btn-large" id="openModalBtn">Apply for this Role</button>
    </main>
    <div class="modal-overlay" id="applyModal">
        <div class="modal-content">
            <span class="close-btn" id="closeModalBtn">&times;</span>
            <h2 class="modal-title">Submit Application</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required placeholder="John Doe">
                </div> 
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="john@example.com">
                </div>  
                <div class="form-group">
                    <label for="portfolio">Portfolio / GitHub URL</label>
                    <input type="url" id="portfolio" name="portfolio" placeholder="https://github.com/johndoe">
                </div>  
                <div class="form-group">
                    <label for="cover_letter">Cover Letter</label>
                    <textarea id="cover_letter" name="cover_letter" rows="4" placeholder="Tell us why you're a great fit..."></textarea>
                </div>
                <button type="submit" class="btn-large">Submit Application</button>
            </form>
        </div>
    </div>
    <script>
        const modal = document.getElementById('applyModal');
        const openBtn = document.getElementById('openModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');
        openBtn.addEventListener('click', () => {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>