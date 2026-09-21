<?php
$students = [
    [
        "name" => "Nguyen Van An",
        "age" => 20,
        "score" => 8.5
    ],
    [
        "name" => "Tran Thi Binh",
        "age" => 21,
        "score" => 6.5
    ],
    [
        "name" => "Pham Thi Dung",
        "age" => 20,
        "score" => 7.5
    ]
];

$total_score = 0;
$total_students = count($students); 

echo "<h4>DANH SÁCH LỚP HỌC</h4>";

foreach ($students as $student) {
    $total_score += $student['score'];

    if ($student['score'] >= 8.0) {
        $classification = "Giỏi";
    } elseif ($student['score'] >= 6.5) {
        $classification = "Khá";
    } else {
        $classification = "Trung bình";
    }
    echo "- Tên: <b>" . $student['name'] . "</b> | ";
    echo "Tuổi: " . $student['age'] . " | ";
    echo "Điểm: " . $student['score'] . " -> Xếp loại: <b>" . $classification . "</b><br>";
}

echo "<hr>"; 
if ($total_students > 0) {
    $average_score = $total_score / $total_students;
    echo "<b>Điểm trung bình của lớp:</b> " . round($average_score, 2);
}

?>


