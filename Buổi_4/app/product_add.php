<?php
require_once 'model/product.php';

$error = '';
$name = '';
$price = '';
$quantity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Kiểm tra dữ liệu đầu vào (Validation)
    if (empty($name)) {
        $error = 'Tên sản phẩm không được để trống.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Giá sản phẩm phải là số và lớn hơn 0.';
    } elseif (!is_numeric($quantity) || $quantity < 0 || floor($quantity) != $quantity) {
        $error = 'Số lượng sản phẩm phải là số nguyên và lớn hơn hoặc bằng 0.';
    } else {
        if (addProduct($name, $price, $quantity)) {
            header("Location: product_list.php");
            exit;
        } else {
            $error = 'Đã có lỗi xảy ra khi thêm sản phẩm.';
        }
    }
}

include 'view/header.php';
?>

<h3>Thêm sản phẩm mới</h3>
<a href="product_list.php" class="btn">&lt; Quay lại danh sách</a>

<?php if (!empty($error)): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<form action="product_add.php" method="POST">
    <div>
        <label>Tên sản phẩm:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
    </div>
    <div>
        <label>Giá:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>">
    </div>
    <div>
        <label>Số lượng:</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($quantity) ?>">
    </div>
    <button type="submit" class="btn btn-add">Lưu lại</button>
</form>

<?php include 'view/footer.php'; ?>
