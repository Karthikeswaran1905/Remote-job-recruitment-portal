<?php session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication | Remotely</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #0f172a; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .auth-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .tabs { display: flex; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; }
        .tab { flex: 1; text-align: center; padding: 10px; cursor: pointer; font-weight: 600; color: #64748b; }
        .tab.active { color: var(--primary); border-bottom: 2px solid var(--primary); margin-bottom: -2px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .btn { width: 100%; background: var(--primary); color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .hidden { display: none; }
        #msgBox { padding: 10px; margin-bottom: 15px; border-radius: 6px; display: none; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="tabs">
        <div class="tab active" onclick="switchTab('login')">Login</div>
        <div class="tab" onclick="switchTab('register')">Register</div>
    </div>
    <div id="msgBox"></div>
    <form id="loginForm" onsubmit="handleAuth(event, 'login')">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Log In</button>
    </form>
    <form id="registerForm" class="hidden" onsubmit="handleAuth(event, 'register')">
        <div class="form-group">
            <label>I am a...</label>
            <select name="role" id="roleSelect" onchange="toggleCompanyField()">
                <option value="seeker">Job Seeker</option>
                <option value="employer">Employer / Hiring Manager</option>
            </select>
        </div>
        <div class="form-group hidden" id="companyGroup">
            <label>Company Name</label>
            <input type="text" name="company" id="companyInput">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required minlength="6">
        </div>
        <button type="submit" class="btn">Create Account</button>
    </form>
</div>
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById('loginForm').classList.toggle('hidden', tab !== 'login');
        document.getElementById('registerForm').classList.toggle('hidden', tab !== 'register');
        document.getElementById('msgBox').style.display = 'none';
    }
    function toggleCompanyField() {
        const isEmployer = document.getElementById('roleSelect').value === 'employer';
        document.getElementById('companyGroup').classList.toggle('hidden', !isEmployer);
        document.getElementById('companyInput').required = isEmployer;
    }
    async function handleAuth(e, action) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', action);
        const msgBox = document.getElementById('msgBox');
        msgBox.style.display = 'block';
        msgBox.style.background = '#e2e8f0';
        msgBox.style.color = '#333';
        msgBox.innerText = 'Processing...';
        try {
            const response = await fetch('auth.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.status === 'success') {
                msgBox.style.background = '#d1fae5';
                msgBox.style.color = '#065f46';
                msgBox.innerText = result.message || 'Success! Redirecting...';
                
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else if (action === 'register') {
                    setTimeout(() => switchTab('login'), 1500);
                }
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            msgBox.style.background = '#fee2e2';
            msgBox.style.color = '#991b1b';
            msgBox.innerText = error.message || 'An error occurred.';
        }
    }
</script>
</body>
</html>