<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "blog_db";

$blogTitle = $_POST["blogtitle"];
$blogDate = $_POST["blogdate"];
$blogPara = $_POST["blogpara"];

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
}

$filename = "NONE";

if ($blogDate) {
    $dateTime = DateTime::createFromFormat('n/j/Y, g:i A', $blogDate);
    if ($dateTime) {
        $blogDate = $dateTime->format('Y-m-d'); // Convert to MySQL DATE format
    } else {
        die("Invalid date format. Please use 'MM/DD/YYYY, HH:MM AM/PM'.");
    }
}


if (isset($_FILES['uploadimage']) && $_FILES['uploadimage']['error'] == UPLOAD_ERR_OK) {
    $filename = basename($_FILES['uploadimage']['name']);
    $tempname = $_FILES['uploadimage']['tmp_name'];

    $uploadDir = "images/";
    $uploadFilePath = $uploadDir . $filename;

    if (move_uploaded_file($tempname, $uploadFilePath)) {
        echo "";
    } else {
        echo "Error uploading image.";
    }
}

$stmt = $conn->prepare("INSERT INTO blog_table (topic_title, topic_date, image_filename, topic_para) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $blogTitle, $blogDate, $filename, $blogPara);

if ($stmt->execute()) {
    echo "";
} else {
    echo "Error Saving Post: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Saved</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<div class="container" style="width: 50%; margin: auto; text-align: justify; font-family: Roboto, sans-serif;">
    <h1 style="margin-bottom: 10px; text-align: center;">Post Saved</h1>
    <center><a style="color: dodgerblue;" href="index.php">Go to Home Page</a></center>
    <br><br>

    <?php echo "<span style='font-weight: bold;' id='showTitle'>" . htmlspecialchars($blogTitle) . "</span>" ?>
    <br>
    <span id="showDate"><?php echo htmlspecialchars($blogDate) ?></span><br><br>

    <?php if ($filename != "NONE"): ?>
        <center><img src="images/<?php echo htmlspecialchars($filename); ?>" id="showImage" style="width: 50%; height: auto;"></center>
    <?php endif; ?>

    <br>
    <?php echo "<span id='showPara'>" . nl2br(htmlspecialchars($blogPara)) . "</span>" ?>
    <br><br>
</div>
</body>
</html>
