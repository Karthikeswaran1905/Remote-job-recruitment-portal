<?php session_start();
require_once 'db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $role = $_POST['role'] ?? 'seeker';
        if (!in_array($role, ['seeker', 'employer'])) {
            $role = 'seeker'; 
        }
        $company = ($role === 'employer') ? htmlspecialchars($_POST['company']) : null;
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, company_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $hash, $role, $company]);          
            echo json_encode(['status' => 'success', 'message' => 'Registration complete. You can now log in.']);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error.']);
            }
        }
        exit;
    }
    if ($action === 'login') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT id, email, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);          
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            echo json_encode([
                'status' => 'success', 
                'redirect' => $user['role'] === 'employer' ? 'dashboard.php' : 'index.php'
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
        }
        exit;
    }
}
?>