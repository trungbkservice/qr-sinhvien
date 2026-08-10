<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['student_code' => 'SV001', 'name' => 'Nguyễn Văn An'],
            ['student_code' => 'SV002', 'name' => 'Trần Thị Bình'],
            ['student_code' => 'SV003', 'name' => 'Lê Hoàng Cường'],
            ['student_code' => 'SV004', 'name' => 'Phạm Minh Đức'],
            ['student_code' => 'SV005', 'name' => 'Đỗ Thu Hà'],
            ['student_code' => 'SV006', 'name' => 'Vũ Quốc Khánh'],
            ['student_code' => 'SV007', 'name' => 'Bùi Phương Linh'],
            ['student_code' => 'SV008', 'name' => 'Đặng Quang Nam'],
            ['student_code' => 'SV009', 'name' => 'Hoàng Ngọc Ánh'],
            ['student_code' => 'SV010', 'name' => 'Ngô Tiến Thành'],
            ['student_code' => '0767666', 'name' => 'Sinh Viên Test'],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['student_code' => $student['student_code']],
                $student
            );
        }
    }
}