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

function calculateAverageScore($students) {
    $totalScore = 0;
    foreach ($students as $student) {
        $totalScore += $student['score'];
    }
    return count($students) > 0 ? ($totalScore / count($students)) : 0;
}

function getRank($score) {
    if ($score >= 8) {
        return "Giỏi";
    } elseif ($score >= 6.5) {
        return "Khá";
    } elseif ($score >= 5) {
        return "Trung bình";
    } else {
        return "Yếu";
    }
}

function displayStudent($student) {
    $rank = getRank($student['score']); 
    echo "Họ tên: " . $student['name'] . " | Tuổi: " . $student['age'] . " | Điểm: " . $student['score'] . " | Xếp loại: <b>" . $rank . "</b><br>";
}

echo "<h4>Bài 2: Tách hàm xử lý sinh viên</h4>";

foreach ($students as $student) {
    displayStudent($student);
}
echo "<hr>";
$avg = calculateAverageScore($students);
echo "<b>Điểm trung bình :</b> " . round($avg, 2);
