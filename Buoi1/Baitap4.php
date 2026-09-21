<?php
class Student {
    public $name;
    public $age;
    public $score;

    public function __construct($name, $age, $score) {
        $this->name = $name;
        $this->age = $age;
        $this->score = $score;
    }

    public function getRank() {
        if ($this->score >= 8) return "Giỏi";
        if ($this->score >= 6.5) return "Khá";
        if ($this->score >= 5) return "Trung bình";
        return "Yếu";
    }

    public function isPassed() {
        return $this->score >= 5;
    }

    public function display() {
        echo "Họ tên: " . $this->name . " | Tuổi: " . $this->age . " | Điểm: " . $this->score . " | Xếp loại: <b>" . $this->getRank() . "</b><br>";
    }
}

function getBestStudentOOP($studentList) {
    if (empty($studentList)) return null;
    $best = $studentList[0];
    foreach ($studentList as $student) {
        if ($student->score > $best->score) { 
            $best = $student;
        }
    }
    return $best;
}

function calculateAverageScoreOOP($studentList) {
    $total = 0;
    foreach ($studentList as $student) {
        $total += $student->score;
    }
    return count($studentList) > 0 ? ($total / count($studentList)) : 0;
}

echo "<h4>Bài 4: Quản lý sinh viên bằng OOP</h4>";


$student1 = new Student("Nguyen Van An", 20, 8.5);
$student2 = new Student("Tran Thi Binh", 21, 6.5);
$student3 = new Student("Le Van Cuong", 19, 4.5);
$student4 = new Student("Pham Thi Dung", 20, 7.5);

$studentList = [$student1, $student2, $student3, $student4];

echo "<b>Danh sách sinh viên:</b><br>";
$passedCount = 0;
foreach ($studentList as $student) {
    $student->display(); 
    if ($student->isPassed()) {
        $passedCount++;
    }
}

echo "<hr>";
$bestStudent = getBestStudentOOP($studentList);
echo "Sinh viên cao điểm nhất là <b>" . $bestStudent->name . "</b> có số điểm là " . $bestStudent->score . " điểm.<br>";

echo "Số sinh viên đạt điều kiện tốt nghiệp: <b>" . $passedCount . "</b> sinh viên.<br>";

$avgOOP = calculateAverageScoreOOP($studentList);
echo "Điểm trung bình của lớp : <b>" . round($avgOOP, 2) . "</b>";
