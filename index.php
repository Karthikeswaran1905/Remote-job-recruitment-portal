<?php session_start(); 
require_once 'db.php';
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY posted_at DESC");
$jobs = $stmt->fetchAll();
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remotely - Find Your Next Remote Role</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f8fafc;
            --card-bg: #ffffff;
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
        .hero {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
            animation: fadeInDown 1s ease-out;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .hero p {
            color: #94a3b8;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .search-bar {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
        }
        .search-bar input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .search-bar input:focus {
            outline: none;
            transform: scale(1.02);
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .job-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        .job-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            opacity: 0;
            animation: slideUp 0.6s ease-out forwards;
        }
        .job-card:nth-child(1) { animation-delay: 0.1s; }
        .job-card:nth-child(2) { animation-delay: 0.2s; }
        .job-card:nth-child(3) { animation-delay: 0.3s; }
        .job-card:nth-child(4) { animation-delay: 0.4s; }
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .card-header img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }
        .job-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .company-name {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .job-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .tags {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tag {
            background: #e0e7ff;
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }
        .salary {
            font-weight: 600;
            color: #10b981;
        }
        .apply-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }
        .apply-btn:hover {
            background: var(--primary-hover);
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }
        .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 600;
            transition: color 0.2s;
        }
        .nav-links a:hover {
            color: var(--primary);
        }
        .nav-links a.btn-primary {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .nav-links a.btn-primary:hover {
            background: var(--primary-hover);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <div style="font-weight: 700; font-size: 1.25rem; color: var(--primary);">Remotely</div>
        <div class="nav-links">
            <?php if (!$is_logged_in): ?>
                <a href="login.php">Log In</a>
                <a href="login.php" class="btn-primary">Post a Job</a>
            <?php else: ?>
                <span style="color: #64748b; margin-right: 15px; font-size: 0.9rem;">
                    <?= htmlspecialchars($_SESSION['email']) ?>
                </span>          
                <?php if ($user_role === 'employer'): ?>
                    <a href="dashboard.php" class="btn-primary">Employer Dashboard</a>
                <?php elseif ($user_role === 'seeker'): ?>
                    <a href="profile.php">My Applications</a>
                <?php endif; ?>
                <a href="logout.php">Log Out</a>
            <?php endif; ?>
        </div>
    </nav>
    <header class="hero">
        <h1>Find Your Dream Remote Job</h1>
        <p>Work from anywhere. Explore roles from top global tech companies.</p>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by job title or keyword...">
        </div>
    </header>
    <main class="container">
        <div class="job-grid" id="jobGrid">
            <?php if (count($jobs) > 0): ?>
                <?php foreach($jobs as $job): ?>
                    <article class="job-card">
                        <div class="card-header">
                            <img src="<?= htmlspecialchars($job['logo_url']) ?>" alt="<?= htmlspecialchars($job['company']) ?> logo">
                            <div>
                                <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
                                <div class="company-name"><?= htmlspecialchars($job['company']) ?> &bull; <?= date('M d, Y', strtotime($job['posted_at'])) ?></div>
                            </div>
                        </div>
                        <p class="job-desc"><?= htmlspecialchars($job['description']) ?></p>                       
                        <div class="tags">
                            <?php 
                            $tags = explode(',', $job['tags']);
                            foreach($tags as $tag): 
                            ?>
                                <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer">
                            <span class="salary"><?= htmlspecialchars($job['salary']) ?></span>
                            <a href="job.php?id=<?= $job['id'] ?>" class="apply-btn" style="text-decoration: none; text-align: center;">View & Apply</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No jobs found. Check back later!</p>
            <?php endif; ?>
        </div>
    </main>
    <script>
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    const searchInput = document.getElementById('searchInput');
    const jobGrid = document.getElementById('jobGrid');
    const performSearch = async (query) => {
        try {
            const response = await fetch(`api/search.php?q=${encodeURIComponent(query)}`);
            const result = await response.json();
            if (result.status === 'success') {
                jobGrid.innerHTML = '';
                if (result.data.length === 0) {
                    jobGrid.innerHTML = '<p>No jobs found matching your criteria.</p>';
                    return;
                }
                result.data.forEach((job, index) => {
                    const tagsHtml = job.tags.split(',').map(tag => 
                        `<span class="tag">${tag.trim()}</span>`
                    ).join('');
                    const delay = (index % 4 + 1) * 0.1;
                    const cardHtml = `
                        <article class="job-card" style="animation-delay: ${delay}s">
                            <div class="card-header">
                                <img src="${job.logo_url}" alt="${job.company} logo">
                                <div>
                                    <div class="job-title">${job.title}</div>
                                    <div class="company-name">${job.company}</div>
                                </div>
                            </div>
                            <p class="job-desc">${job.description.substring(0, 100)}...</p>
                            <div class="tags">${tagsHtml}</div>
                            <div class="card-footer">
                                <span class="salary">${job.salary}</span>
                                <a href="job.php?id=${job.id}" class="apply-btn" style="text-decoration: none; text-align: center;">View & Apply</a>
                            </div>
                        </article>
                    `;
                    jobGrid.insertAdjacentHTML('beforeend', cardHtml);
                });
            }
        } catch (error) {
            console.error('Search failed:', error);
        }
    };
    searchInput.addEventListener('keyup', debounce((e) => {
        performSearch(e.target.value);
    }, 300));
    </script>
</body>
</html>