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
        "name" => "Le Van Cuong",
        "age" => 19,
        "score" => 4.5
    ],
    [
        "name" => "Pham Thi Dung",
        "age" => 20,
        "score" => 7.5
    ]
];

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

function findBestStudent($students) {
    if (empty($students)) return null;
    $best = $students[0]; 
    foreach ($students as $student) {
        if ($student['score'] > $best['score']) {
            $best = $student;
        }
    }
    return $best;
}

function findWorstStudent($students) {
    if (empty($students)) return null;
    $worst = $students[0]; 
    foreach ($students as $student) {
        if ($student['score'] < $worst['score']) {
            $worst = $student;
        }
    }
    return $worst;
}

function countPassedStudents($students) {
    $count = 0;
    foreach ($students as $student) {
        if ($student['score'] >= 5) {
            $count++;
        }
    }
    return $count;
}

function findStudentByName($students, $name) {
    foreach ($students as $student) {
        if (strcasecmp($student['name'], $name) == 0) {
            return $student;
        }
    }
    return null;
}

echo "<h4>Bài 3: Xử lý danh sách sinh viên nâng cao</h4>";

$best = findBestStudent($students);
$worst = findWorstStudent($students);
echo "Sinh viên có điểm cao nhất nhất: <b>" . $best['name'] . "</b> (" . $best['score'] . " điểm)<br>";
echo "Sinh viên có điểm thấp nhất: <b>" . $worst['name'] . "</b> (" . $worst['score'] . " điểm)<br>";

$passedCount = countPassedStudents($students);
echo "Số lượng sinh viên đạt trên 5 điểm : <b>" . $passedCount . "/" . count($students) . "</b><br>";

echo "<hr>";

$searchName = "Tran Thi Binh";
$searchResult = findStudentByName($students, $searchName);

if ($searchResult) {
    echo "Thông tin sinh viên '$searchName': <br>";
    displayStudent($searchResult); 
} else {
    echo "Không tìm thấy sinh viên có tên: " . $searchName;
}
?>
