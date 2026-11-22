<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ hỗ trợ phương thức POST.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$host = '127.0.0.1';
$db   = 'shop_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối cơ sở dữ liệu.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$productId = isset($_POST['product_id']) && preg_match('/^[1-9]\d*$/', (string)$_POST['product_id'])
    ? (int)$_POST['product_id']
    : 0;

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Mã sản phẩm (product_id) không hợp lệ.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $pdo->prepare("SELECT id, image FROM products WHERE id = :id");
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy sản phẩm tương ứng để gắn ảnh.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng chọn tệp ảnh để tải lên.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['image'];
$errorMsg = 'Vui lòng chọn định dạng ảnh .jpg, .jpeg hoặc .png (kích thước tối đa 2 MB).';

if ($file['error'] !== UPLOAD_ERR_OK) {
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $errorMsg
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải tệp lên máy chủ.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$maxSize = 2 * 1024 * 1024;
$finfo = new finfo(FILEINFO_MIME_TYPE);
$realMimeType = $finfo->file($file['tmp_name']);

$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png'
];

if ($file['size'] <= 0 || $file['size'] > $maxSize || !array_key_exists($realMimeType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $errorMsg
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$uploadDir = __DIR__ . '/../upload';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Không thể tạo thư mục upload trên máy chủ.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$extension = $allowedMimes[$realMimeType];
$uniqueFileName = 'prod_' . $productId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
$destinationPath = $uploadDir . '/' . $uniqueFileName;
$relativeDbPath = 'upload/' . $uniqueFileName;

if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Không thể lưu tệp ảnh vào thư mục upload.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $updateStmt = $pdo->prepare("UPDATE products SET image = :image WHERE id = :id");
    $updateStmt->execute([
        ':image' => $relativeDbPath,
        ':id'    => $productId
    ]);

    if (!empty($product['image']) && $product['image'] !== $relativeDbPath) {
        $oldFilePath = __DIR__ . '/../' . ltrim($product['image'], '/');
        if (file_exists($oldFilePath) && is_file($oldFilePath)) {
            @unlink($oldFilePath);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Tải ảnh sản phẩm lên thành công.',
        'data'    => [
            'product_id' => $productId,
            'image'      => $relativeDbPath
        ]
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    if (file_exists($destinationPath) && is_file($destinationPath)) {
        @unlink($destinationPath);
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật đường dẫn ảnh vào cơ sở dữ liệu.'
    ], JSON_UNESCAPED_UNICODE);
}
