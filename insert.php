<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Student, Department, and Course Information</title>
    <style>
        body {
            background-image: url('college.jpg'); /* Path to your background image */
            background-size: cover;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
            border-radius: 10px;
            max-width: 800px;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
        }
        input[type="text"] {
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Insert Student, Department, and Course Information</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div style="display: flex; justify-content: space-between;">
                <div style="flex: 1;">
                    <table>
                        <tr>
                            <th colspan="2">Student Information</th>
                        </tr>
                        <tr>
                            <td><label for="studentID">Student ID:</label></td>
                            <td><input type="text" id="studentID" name="studentID"></td>
                        </tr>
                        <tr>
                            <td><label for="firstName">First Name:</label></td>
                            <td><input type="text" id="firstName" name="firstName"></td>
                        </tr>
                        <tr>
                            <td><label for="lastName">Last Name:</label></td>
                            <td><input type="text" id="lastName" name="lastName"></td>
                        </tr>
                        <tr>
                            <td><label for="hoursAssigned">Hours Assigned:</label></td>
                            <td><input type="text" id="hoursAssigned" name="hoursAssigned"></td>
                        </tr>
                        <tr>
                            <td><label for="GPA">GPA:</label></td>
                            <td><input type="text" id="GPA" name="GPA"></td>
                        </tr>
                        <tr>
                            <td><label for="departmentCode">Department Code:</label></td>
                            <td><input type="text" id="departmentCode" name="departmentCode"></td>
                        </tr>
                    </table>
                </div>
                <div style="flex: 1;">
                    <table>
                        <tr>
                            <th colspan="2">Course Information</th>
                        </tr>
                        <tr>
                            <td><label for="course1">Course 1 ID:</label></td>
                            <td><input type="text" id="course1" name="courses[]"></td>
                        </tr>
                        <tr>
                            <td><label for="course2">Course 2 ID:</label></td>
                            <td><input type="text" id="course2" name="courses[]"></td>
                        </tr>
                        <tr>
                            <td><label for="course3">Course 3 ID:</label></td>
                            <td><input type="text" id="course3" name="courses[]"></td>
                        </tr>
                        <tr>
                            <td><label for="course4">Course 4 ID:</label></td>
                            <td><input type="text" id="course4" name="courses[]"></td>
                        </tr>
                        <tr>
                            <td><label for="course5">Course 5 ID:</label></td>
                            <td><input type="text" id="course5" name="courses[]"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>

    <?php
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Your database connection code here
        $servername = "db";
        $username = "php_docker";
        $password = "password";
        $dbname = "Cloud";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Student Information
        $studentID = $_POST['studentID'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $hoursAssigned = $_POST['hoursAssigned'];
        $GPA = $_POST['GPA'];
        $departmentCode = $_POST['departmentCode'];

        // Course Information
        $courses = $_POST['courses']; // Array of course IDs

        // Insert Student Information
        $stmt_student = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, hours_assigned, c_gpa, department_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_student->bind_param("isssss", $studentID, $firstName, $lastName, $hoursAssigned, $GPA, $departmentCode);
        $stmt_student->execute();
        $stmt_student->close();

        // Insert Enrollment Information
        foreach ($courses as $courseCode) {
            if (!empty($courseCode)) {
                $stmt_enrollment = $conn->prepare("INSERT INTO enrollment (student_id, course_code) VALUES (?, ?)");
                $stmt_enrollment->bind_param("ii", $studentID, $courseCode);
                $stmt_enrollment->execute();
                $stmt_enrollment->close();
            }
        }

        echo "<div class='container'>";
        echo "Data inserted successfully.";
        echo "</div>";

        // Close database connection
        $conn->close();
    }
    ?>
</body>
</html>
