<?php
require_once 'model/product.php';
$products = getAllProducts();

include 'view/header.php';
?>

<h3>Danh sách sản phẩm</h3>

<div style="margin-bottom: 20px;">
    <a href="product_add.php" class="btn btn-add" style="margin-right: 10px;">+ Thêm sản phẩm mới</a>
    <a href="product_delete.php" class="btn btn-delete">- Xóa sản phẩm</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Giá (VNĐ)</th>
            <th>Số lượng</th>
            <th>Sửa thông tin</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr>
                <td colspan="5" style="text-align: center;">Chưa có sản phẩm nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($products as $pro): ?>
                <tr>
                    <td><?= $pro['id'] ?></td>
                    <td><?= htmlspecialchars($pro['name']) ?></td>
                    <td><?= number_format($pro['price'], 2, ',', '.') ?></td>
                    <td><?= $pro['quantity'] ?></td>
                    <td>
                        <a href="product_edit.php?id=<?= $pro['id'] ?>" class="btn btn-edit">Sửa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'view/footer.php'; ?>
