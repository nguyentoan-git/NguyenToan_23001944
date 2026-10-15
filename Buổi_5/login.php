<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu đã đăng nhập sẵn thì chuyển tới danh sách sản phẩm luôn
if (isset($_SESSION['username'])) {
    header("Location: product_list.php");
    exit;
}

require_once 'common/dbConnect.php';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: product_list.php");
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        }
    }
}

include 'view/header.php';
?>

<h3>Đăng nhập hệ thống quản lý</h3>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="login.php" method="POST">
    <div>
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
    </div>
    <div>
        <label>Mật khẩu:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-edit">Đăng nhập</button>
    <a href="register.php" class="btn" style="margin-left: 10px;">Đăng ký tài khoản</a>
</form>

<?php include 'view/footer.php'; ?>
