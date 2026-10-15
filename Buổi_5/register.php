<?php
require_once 'common/dbConnect.php';

$error = '';
$success = ''; 
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Tên đăng nhập và mật khẩu không được để trống.';
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = 'Tên đăng nhập này đã tồn tại trong hệ thống.';
        } else {
            // Mã hóa mật khẩu bảo mật
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashedPassword])) {
                $success = 'Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...';
                header("refresh:2;url=login.php");
            } else {
                $error = 'Đã có lỗi xảy ra trong quá trình đăng ký.';
            }
        }
    }
}

include 'view/header.php';
?>

<h3>Đăng ký tài khoản hệ thống</h3>
<p><a href="login.php" class="btn">&lt; Quay lại Đăng nhập</a></p>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form action="register.php" method="POST">
    <div>
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
    </div>
    <div>
        <label>Mật khẩu:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-add">Đăng ký</button>
</form>

<?php include 'view/footer.php'; ?>
