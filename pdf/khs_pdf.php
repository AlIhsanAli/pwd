<?php
require('fpdf.php');
include '../config.php';
include '../includes/functions.php';
requireStudent();


$student = getStudentByUserId($conn, $_SESSION['user_id']);
$enrollments = getEnrollmentsByStudent($conn, $student['id']);
$gpa = calculateGPA($enrollments);

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'KARTU HASIL STUDI (KHS)',0,1,'C');
$pdf->Ln(3);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,"Nama : {$student['nama']}",0,1);
$pdf->Cell(0,6,"NIM  : {$student['nim']}",0,1);
$pdf->Cell(0,6,"Jurusan : {$student['jurusan']}",0,1);
$pdf->Cell(0,6,"Angkatan : {$student['angkatan']}",0,1);
$pdf->Cell(0,6,"IPK : ".number_format($gpa,2),0,1);
$pdf->Ln(5);

$pdf->SetFont('Arial','B',9);
$pdf->Cell(25,8,'Kode',1);
$pdf->Cell(55,8,'Mata Kuliah',1);
$pdf->Cell(10,8,'SKS',1);
$pdf->Cell(35,8,'Dosen',1);
$pdf->Cell(20,8,'Semester',1);
$pdf->Cell(15,8,'Nilai',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
foreach ($enrollments as $e) {
    $pdf->Cell(25,8,$e['kode'],1);
    $pdf->Cell(55,8,$e['course_name'],1);
    $pdf->Cell(10,8,$e['sks'],1);
    $pdf->Cell(35,8,$e['lecturer_name'] ?? 'N/A',1);
    $pdf->Cell(20,8,$e['semester'],1);
    $pdf->Cell(15,8,$e['nilai'] ?? '-',1);
    $pdf->Ln();
}

$pdf->Output('I','KHS_'.$student['nim'].'.pdf');
