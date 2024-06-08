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

    // Validate form inputs
    if (empty($patientName) || empty($doctorName) || empty($departmentName) || empty($phoneNumber) || empty($birthday) || empty($medicalRecord)) {
        die("All fields are required.");
    }

    // Validate phone number length
    if (strlen($phoneNumber) != 11) {
        die("Invalid phone number format.");
    }

    // Handle file upload
    $uploadDirectory = "uploads/";
    $uploadedFilePath = "upload"; // Initialize the variable
    if (isset($_FILES['inputFile']) && $_FILES['inputFile']['error'] == UPLOAD_ERR_OK) {
        $allowedFileTypes = ['application/pdf', 'image/jpeg', 'image/png']; // Allowed file types
        if (in_array($_FILES['inputFile']['type'], $allowedFileTypes)) {
            $uploadedFileName = basename($_FILES['inputFile']['name']);
            $uploadedFilePath = $uploadDirectory . $uploadedFileName; // Correct path assignment
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
            if (move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploadedFilePath)) {
                // File successfully uploaded
                echo "File uploaded successfully.";
            } else {
                echo "File upload failed, please try again.";
            }
        } else {
            echo "Invalid file type. Only PDF, JPEG, and PNG are allowed.";
        }
    } else {
        echo "No file was uploaded.";
    }

    // Prepare file data to be stored in the database
    $fileData = file_get_contents($uploadedFilePath);
    $fileData = mysqli_real_escape_string($con, $fileData); // Escape special characters

    // Generate digital signature
    $dataToSign = $patientName . $doctorName . $departmentName . $phoneNumber . $birthday . $medicalRecord;
    $signature = hash('sha256', $dataToSign);

    // Insert data into database along with the signature
    $que = "INSERT INTO `record` (`PatientName`, `DoctorName`, `DepartmentName`, `PhoneNumber`, `MedicalRecord`, `Birthday`, `Signature`, `FileData`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $que);
    mysqli_stmt_bind_param($stmt, "ssssssss", $patientName, $doctorName, $departmentName, $phoneNumber, $medicalRecord, $birthday, $signature, $fileData);
    mysqli_stmt_execute($stmt);

    // Redirect to main page after submission
    header('Location: about.html');
    exit; 
}
?>
