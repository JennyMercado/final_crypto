<?php
session_start();
include("db.php");

// Establish database connection
$con = mysqli_connect("localhost", "root", "", "medical");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $patientName = isset($_POST['inputPatientName']) ? trim($_POST['inputPatientName']) : '';
    $doctorName = isset($_POST['inputDoctorName']) ? trim($_POST['inputDoctorName']) : '';
    $departmentName = isset($_POST['inputDepartmentName']) ? trim($_POST['inputDepartmentName']) : '';
    $phoneNumber = isset($_POST['inputPhone']) ? trim($_POST['inputPhone']) : '';
    $medicalRecord = isset($_POST['inputMedicalRecord']) ? trim($_POST['inputMedicalRecord']) : '';
    $birthday = isset($_POST['inputDate']) ? trim($_POST['inputDate']) : '';

    // Clean up phone number
    $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

    // Validate form inputs (optional)

    // Prepare and execute SQL query to insert data into database
    // Your SQL query and execution logic here

    // Close database connection
    mysqli_close($con);
}

// Fetch the submitted record from the database based on some identifier (e.g., record ID)
// Here, let's assume you have the record ID stored in a session variable or passed as a URL parameter
// Replace 'record_id' with the actual identifier you use to uniquely identify the record
$recordId = isset($_SESSION['record_id']) ? $_SESSION['record_id'] : ''; // Or fetch from URL parameter if using GET method

if (!empty($recordId)) {
    // Query the database to fetch the record details
    $query = "SELECT * FROM record WHERE RecordID = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $recordId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $RecordID, $PatientName, $DoctorName, $DepartmentName, $PhoneNumber, $MedicalRecord, $Birthday, $Signature, $FileData);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Display the retrieved data
    if ($RecordID) {
        echo "Record ID: " . $RecordID . "<br>";
        echo "Patient Name: " . $PatientName . "<br>";
        echo "Doctor's Name: " . $DoctorName . "<br>";
        echo "Department's Name: " . $DepartmentName . "<br>";
        echo "Phone Number: " . $PhoneNumber . "<br>";
        echo "Medical Record: " . $MedicalRecord . "<br>";
        echo "Birthday: " . $Birthday . "<br>";
        // Display more fields as needed
    } else {
        echo "Record not found.";
    }
} else {
    echo "Record ID is missing.";
}
?>



