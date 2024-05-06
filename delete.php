<?php
$servername = "db";
$username = "php_docker";
$password = "password";
$dbname = "Cloud";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Delete student's enrollment details from enrollment table
$studentID = $_POST['studentID'];
$sql_enrollment = "DELETE FROM enrollment WHERE student_id = $studentID";
if (mysqli_query($conn, $sql_enrollment)) {
    echo "Student's enrollment details deleted successfully from enrollment table";
} else {
    echo "Error deleting student's enrollment details from enrollment table: " . mysqli_error($conn);
}

// Delete student from students table
$sql_students = "DELETE FROM students WHERE student_id = $studentID";
if (mysqli_query($conn, $sql_students)) {
    echo "Student deleted successfully from students table";
} else {
    echo "Error deleting student from students table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
