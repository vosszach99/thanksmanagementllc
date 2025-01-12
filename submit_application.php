<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form fields
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $position = htmlspecialchars($_POST['position']);

    // File upload
    $resume = $_FILES['resume'];

    // Check for file upload errors
    if ($resume['error'] !== UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    // Read the uploaded file
    $fileContent = file_get_contents($resume['tmp_name']);
    $fileName = $resume['name'];

    // Email details
    $to = "hr@example.com"; // Replace with your HR email address
    $subject = "New Job Application: $position";
    $message = "You have received a new job application:\n\n";
    $message .= "Name: $name\n";
    $message .= "Email: $email\n";
    $message .= "Position: $position\n\n";

    // Boundary for the email
    $boundary = md5(time());

    // Headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Message body
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n";
    $body .= "\r\n";
    $body .= $message . "\r\n";

    // Attachment
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: " . $resume['type'] . "; name=\"" . $fileName . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . $fileName . "\"\r\n";
    $body .= "\r\n";
    $body .= chunk_split(base64_encode($fileContent)) . "\r\n";
    $body .= "--$boundary--";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        echo "Application submitted successfully.";
    } else {
        echo "Failed to send the application. Please try again.";
    }
}
?>