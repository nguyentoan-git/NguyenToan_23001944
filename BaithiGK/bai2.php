
<?php
//Bai 2
$prices = array(10000,20000,15000,45000,12000);

function calculateTotal($arr) {
    $total = 0;
    foreach ($arr as $price) {
        $total += $price;
    }
    return $total; 
}

function findMaxPrice($arr) {
    if (empty($arr)) {
        return 0;
    }
    
    $max = $arr[0];
    foreach ($arr as $price) {
        if ($price > $max) {
            $max = $price;
        }
    }
    return $max; 
}
$totalPrice = calculateTotal($prices);
$maxPrice = findMaxPrice($prices);

echo "Danh sách giá sản phẩm: " . implode(", ", $prices) . " (đơn vị: VND)<br>";
echo "Tổng giá trị tất cả sản phẩm: " . number_format($totalPrice) . " VND<br>";
echo "Giá sản phẩm cao nhất: " . number_format($maxPrice) . " VND<br>";

?>
