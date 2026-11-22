<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
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
        'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function validateProductInput($input) {
    if (!is_array($input)) {
        return ['valid' => false, 'message' => 'Dữ liệu gửi lên phải ở định dạng JSON hợp lệ.'];
    }
    if (!isset($input['name']) || !is_string($input['name'])) {
        return ['valid' => false, 'message' => 'Tên sản phẩm không được để trống.'];
    }
    $name = trim($input['name']);
    $nameLen = mb_strlen($name, 'UTF-8');
    if ($nameLen < 1 || $nameLen > 100) {
        return ['valid' => false, 'message' => 'Tên sản phẩm phải có độ dài từ 1 đến 100 ký tự.'];
    }
    if (!isset($input['price']) || filter_var($input['price'], FILTER_VALIDATE_INT) === false) {
        return ['valid' => false, 'message' => 'Đơn giá phải là một số nguyên.'];
    }
    $price = (int)$input['price'];
    if ($price <= 0) {
        return ['valid' => false, 'message' => 'Đơn giá phải là số nguyên lớn hơn 0.'];
    }
    if (!isset($input['stock']) || filter_var($input['stock'], FILTER_VALIDATE_INT) === false) {
        return ['valid' => false, 'message' => 'Số lượng tồn phải là một số nguyên.'];
    }
    $stock = (int)$input['stock'];
    if ($stock < 0) {
        return ['valid' => false, 'message' => 'Số lượng tồn phải là số nguyên không âm (>= 0).'];
    }

    return [
        'valid' => true,
        'data' => [
            'name'  => $name,
            'price' => $price,
            'stock' => $stock
        ]
    ];
}

function fetchPaginatedProducts(PDO $pdo, int $page, int $limit = 5): array {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM products");
    $total = (int)$countStmt->fetchColumn();

    $totalPages = max(1, (int)ceil($total / $limit));
    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $limit;
    $stmt = $pdo->prepare("SELECT id, name, price, stock, image FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

    return [
        'data' => $products,
        'pagination' => [
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'total_pages' => $totalPages
        ]
    ];
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $page = 1;
        if (isset($_GET['page']) && $_GET['page'] !== '') {
            if (!preg_match('/^[1-9]\d*$/', (string)$_GET['page'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tham số page phải là số nguyên dương (>= 1).'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $page = (int)$_GET['page'];
        }

        $limit = 5;
        if (isset($_GET['limit']) && $_GET['limit'] !== '') {
            if (!preg_match('/^\d+$/', (string)$_GET['limit']) || (int)$_GET['limit'] !== 5) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tham số limit không hợp lệ (chỉ chấp nhận limit = 5).'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $limit = 5;
        }

        try {
            $result = fetchPaginatedProducts($pdo, $page, $limit);
            echo json_encode([
                'success'    => true,
                'data'       => $result['data'],
                'pagination' => $result['pagination']
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Không thể lấy danh sách sản phẩm.'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'POST':
        $rawBody = file_get_contents('php://input');
        $input = json_decode($rawBody, true);
        $validation = validateProductInput($input);

        if (!$validation['valid']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $validation['message']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $data = $validation['data'];
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, image) VALUES (:name, :price, :stock, NULL)");
            $stmt->execute([
                ':name'  => $data['name'],
                ':price' => $data['price'],
                ':stock' => $data['stock']
            ]);
            $newId = (int)$pdo->lastInsertId();

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Thêm sản phẩm thành công.',
                'data'    => [
                    'id'    => $newId,
                    'name'  => $data['name'],
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'image' => null
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'PUT':
        $id = isset($_GET['id']) && preg_match('/^[1-9]\d*$/', (string)$_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Mã sản phẩm (id) trên URL không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $checkStmt = $pdo->prepare("SELECT id, image FROM products WHERE id = :id");
        $checkStmt->execute([':id' => $id]);
        $existingProduct = $checkStmt->fetch();
        if (!$existingProduct) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại để cập nhật.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $rawBody = file_get_contents('php://input');
        $input = json_decode($rawBody, true);
        $validation = validateProductInput($input);

        if (!$validation['valid']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $validation['message']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $data = $validation['data'];
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id");
            $stmt->execute([
                ':name'  => $data['name'],
                ':price' => $data['price'],
                ':stock' => $data['stock'],
                ':id'    => $id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công.',
                'data'    => [
                    'id'    => $id,
                    'name'  => $data['name'],
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'image' => $existingProduct['image']
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi cập nhật sản phẩm.'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'DELETE':
        $id = isset($_GET['id']) && preg_match('/^[1-9]\d*$/', (string)$_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Mã sản phẩm (id) trên URL không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $targetPage = 1;
        if (isset($_GET['page']) && preg_match('/^[1-9]\d*$/', (string)$_GET['page'])) {
            $targetPage = (int)$_GET['page'];
        }

        try {
            $imgStmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
            $imgStmt->execute([':id' => $id]);
            $productToDelete = $imgStmt->fetch();

            if (!$productToDelete) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại hoặc đã bị xóa trước đó.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if (!empty($productToDelete['image'])) {
                $filePath = __DIR__ . '/../' . ltrim($productToDelete['image'], '/');
                if (file_exists($filePath) && is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            $paginated = fetchPaginatedProducts($pdo, $targetPage, 5);

            echo json_encode([
                'success'    => true,
                'message'    => 'Xóa sản phẩm thành công.',
                'pagination' => $paginated['pagination']
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi xóa sản phẩm.'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức HTTP không được hỗ trợ.'
        ], JSON_UNESCAPED_UNICODE);
        break;
}