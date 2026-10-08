<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'model/product.php';

$error = '';
$product = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_check'])) {
    $id = intval($_POST['input_id']);
    
    if ($id <= 0) {
        $error = "Vui lòng nhập một mã ID hợp lệ (số nguyên lớn hơn 0)!";
    } else {
        $product = getProductById($id);
        if (!$product) {
            $error = "Sản phẩm có ID = $id không tồn tại trong hệ thống sản phẩm!";
        } else {
            $_SESSION['delete_product'] = $product;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_confirm_delete'])) {
    if (isset($_SESSION['delete_product'])) {
        $id_to_delete = intval($_SESSION['delete_product']['id']);
        deleteProduct($id_to_delete);
        unset($_SESSION['delete_product']);
        
        header("Location: product_list.php");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' || isset($_POST['action_cancel'])) {
    unset($_SESSION['delete_product']);
}

if (isset($_SESSION['delete_product'])) {
    $product = $_SESSION['delete_product'];
}

include 'view/header.php';
?>

<h3>Chức năng: Xóa sản phẩm </h3>
<?php if (!$product): ?>
    <div style="border: 1px solid #ccc; padding: 20px; max-width: 450px; background-color: #fafafa;">
        <h4 style="margin-top: 0;">Nhập mã sản phẩm cần xóa</h4>
        
        <?php if (!empty($error)): ?>
            <p class="error" style="color: red; background: #ffebee; padding: 8px; border-left: 4px solid #f44336; font-weight: bold;"><?= $error ?></p>
        <?php endif; ?>

        <form action="product_delete.php" method="POST" style="width: auto;">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 5px;">Mã ID sản phẩm:</label>
                <input type="number" name="input_id" placeholder="Ví dụ: 1, 2, 3..." required min="1" style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            <button type="submit" name="action_check" class="btn btn-edit">Kiểm tra sản phẩm</button>
            <a href="product_list.php" class="btn" style="display: inline-block; margin-left: 10px;">Quay lại danh sách</a>
        </form>
    </div>

<?php else: ?>
    <div style="border: 1px solid #f44336; padding: 20px; background-color: #ffebee; max-width: 450px;">
        <h4 style="margin-top: 0; color: #d32f2f;">⚠️ XÁC NHẬN</h4>
        <p>Bạn có chắc chắn muốn xóa vĩnh viễn sản phẩm không?</p>
        
        <table style="width: 100%; background: #fff; margin-bottom: 15px; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd;">ID:</td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?= $product['id'] ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd;">Tên sản phẩm:</td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($product['name']) ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd;">Giá niêm yết:</td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?= number_format($product['price'], 2, ',', '.') ?> VNĐ</td>
            </tr>
        </table>
        
        <form action="product_delete.php" method="POST" style="width: auto;">
            <!-- Nút đồng ý lệnh xóa -->
            <button type="submit" name="action_confirm_delete" class="btn btn-delete" style="font-weight: bold;">Xóa</button>
            
            <!-- Nút từ chối, hủy bỏ quay về trang chủ (product_list.php) -->
            <button type="submit" name="action_cancel" class="btn" style="background: #fff; margin-left: 10px;">Hủy</button>
        </form>
    </div>
<?php endif; ?>

<?php include 'view/footer.php'; ?>
