<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Website</title>
    <link rel="stylesheet" href="c.css">
    <style>
        /* Highlighted row styles */
        .highlight {
            background-color: #ffc107; /* Yellow background color */
        }

        /* Responsive design */
        @media screen and (max-width: 600px) {
            table {
                width: 100%;
            }
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
        }
        
        /* Adjust width of the operation column */
        #courseTable th:nth-child(12),
        #courseTable td:nth-child(12) {
            width: 120px; /* Adjust width as needed */
        }

        /* Style for the insert, update, and delete buttons */
        .operationButton {
            width: 80px;
            margin-right: 5px;
            background-color: #007bff; /* Blue background color */
            color: #fff; /* White text color */
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Style for the buttons on hover */
        .operationButton:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        /* Message display style */
        #message {
            display: none;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Course Information</h1>
        <input type="text" id="searchInput" placeholder="Search by Student ID, Name">
        <button id="insertButton" class="operationButton">Insert</button>
        <table id="courseTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Hours Assigned</th>
                    <th>GPA</th>
                    <th>Department Name</th>
                    <th>Department Code</th>
                    <th>Course 1</th>
                    <th>Course 2</th>
                    <th>Course 3</th>
                    <th>Course 4</th>
                    <th>Course 5</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Establish a connection to the MySQL database
                $servername = "172.22.0.2"; // IP address of the database container
                $username = "php_docker";
                $password = "password";
                $dbname = "Cloud";
                    
                    // Create connection
                    $conn =mysqli_connect($servername, $username, $password, $dbname);
                    
                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    
                    // Execute the SQL query
                    $sql = "SELECT 
                                S.student_id,
                                CONCAT(S.first_name, ' ', S.last_name) AS student_name,
                                S.hours_assigned,
                                S.c_gpa,
                                D.department_name,
                                D.department_code,
                                MAX(CASE WHEN C.rn = 1 THEN C.course_name END) AS course_1,
                                MAX(CASE WHEN C.rn = 2 THEN C.course_name END) AS course_2,
                                MAX(CASE WHEN C.rn = 3 THEN C.course_name END) AS course_3,
                                MAX(CASE WHEN C.rn = 4 THEN C.course_name END) AS course_4,
                                MAX(CASE WHEN C.rn = 5 THEN C.course_name END) AS course_5
                            FROM 
                                students S
                            JOIN 
                                departments D ON S.department_code = D.department_code
                            LEFT JOIN (
                                SELECT 
                                    e.student_id,
                                    c.course_name,
                                    ROW_NUMBER() OVER (PARTITION BY e.student_id ORDER BY c.course_name) AS rn
                                FROM 
                                    enrollment e
                                JOIN 
                                    courses c ON e.course_code = c.course_code
                            ) AS C ON S.student_id = C.student_id
                            GROUP BY 
                                S.student_id, S.first_name, S.last_name, S.hours_assigned, S.c_gpa, D.department_name, D.department_code";
                    $result = $conn->query($sql);
                    
                    // Check if there are any results
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["student_id"] . "</td>";
                            echo "<td>" . $row["student_name"] . "</td>";
                            echo "<td>" . $row["hours_assigned"] . "</td>";
                            echo "<td>" . $row["c_gpa"] . "</td>";
                            echo "<td>" . $row["department_name"] . "</td>";
                            echo "<td>" . $row["department_code"] . "</td>";
                            // Display "none" for empty course names
                            echo "<td>" . ($row["course_1"] ? $row["course_1"] : "none") . "</td>";
                            echo "<td>" . ($row["course_2"] ? $row["course_2"] : "none") . "</td>";
                            echo "<td>" . ($row["course_3"] ? $row["course_3"] : "none") . "</td>";
                            echo "<td>" . ($row["course_4"] ? $row["course_4"] : "none") . "</td>";
                            echo "<td>" . ($row["course_5"] ? $row["course_5"] : "none") . "</td>";
                            echo "<td><button class='deleteBtn'>Delete</button></td>"; // New column for delete button only
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>0 results</td></tr>";
                    }
                    
                    // Close the connection
                    $conn->close();
                ?>
            </tbody>
        </table>
        <p id="noResults" style="display: none;">No results found</p>
        <p id="message"></p> <!-- Message display element -->
    </div>

    <script>

    function filterTable() {
            // Get the search input value
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            // Get all table rows
            const rows = document.querySelectorAll('#courseTable tbody tr');
            // Loop through each row and hide those that don't match the search input
            rows.forEach(row => {
                // Get student ID and name from the row
                const studentID = row.cells[0].textContent.toLowerCase();
                const studentName = row.cells[1].textContent.toLowerCase();
                // Check if student ID or name contains the search input
                if (studentID.includes(searchInput) || studentName.includes(searchInput)) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        }

    // Event listener for search input
    document.getElementById('searchInput').addEventListener('input', filterTable);
    // Function to open new PHP page for operations in the same window
    function openOperationPageinsert(operation) 
    {
        window.location.href = operation + '.php';
    }

    // Function to delete data from the database
    function deleteData(studentID) {
        // Send an asynchronous request to delete.php with the studentID parameter
        fetch('delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'studentID=' + encodeURIComponent(studentID)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            // Handle the response from the server
            console.log(data); // Log the response
            // Display a message to the user
            const messageElement = document.getElementById('message');
            messageElement.textContent = "Delete successful"; // Display the message
            messageElement.style.display = "block"; // Show the message element
            // You can update the UI or display a message to the user
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            // Handle errors or display error messages
        });
    }

    // Event listener to highlight rows when clicked
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', () => {
            row.classList.toggle('highlight'); // Toggle highlight on click
        });
    });

    // Event listener for delete buttons
    const deleteButtons = document.querySelectorAll('.deleteBtn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent row click event from triggering
            console.log("Delete button clicked"); // Debugging statement
            // Get the student ID associated with the row
            const studentID = button.parentElement.parentElement.querySelector('td:first-child').textContent;
            console.log("Student ID:", studentID); // Debugging statement
            // Call the deleteData function with the student ID
            deleteData(studentID);
        });
    });

    // Event listener for insert button
    const insertButton = document.getElementById('insertButton');
    insertButton.addEventListener('click', () => {
        openOperationPageinsert('insert');
    });

</script>

</body>
</html>