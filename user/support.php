<?php
require("./check.php"); // Ensure user is authenticated
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'], $_POST['description'])) {
        $user_id = $_SESSION['user_id'];
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        // Insert support request into the database
        $sql = "INSERT INTO `support_requests`(`user_id`, `description`) VALUES ('$user_id', '$description')";
        $stmt = mysqli_query($conn, $sql);

        if ($stmt) {
            $_SESSION['success'] = "Ticket sent successfully!";
            header("Location: ./support.php");
        } else {
            $_SESSION['error'] = "Error while submitting.";
            header("Location: ./support.php");
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ./support.php");
    }

    mysqli_close($conn);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Page</title>
    <?php
    require("./style-files.php");
    ?>
    <style>
        /* General Page Styling */
        .whole-body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f5f7fa; /* Soft white background */
            color: #333;
        }
        /* Container Layout */
        .support-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80%;
            max-width: 600px;
            background: white; /* White background for the box */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        /* Title & Description */
        .title-h1 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #0d47a1; /* Deep blue */
        }
        .p1 {
            font-size: 1.1rem;
            line-height: 1.5;
            color: #444;
            margin-bottom: 20px;
        }
        /* Success Message Styling */
        .success {
            color: green;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        /* Input Field */
        #description {
            width: calc(100% - 10px); /* Increased width */
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #1976d2;
            background: #f0f4f8; /* Light grayish background */
            color: #333;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
            height: 120px;
            resize: none;
        }
        #description::placeholder {
            color: #666;
        }
        #description:focus {
            background: white;
            border-color: #0d47a1;
            box-shadow: 0 0 5px rgba(13, 71, 161, 0.3);
        }
        /* Button Styling */
        .s-btn {
            background: #1565c0;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .s-btn:hover {
            background: #0d47a1;
            transform: scale(1.05);
        }
        /* Responsive Design */
        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<?php require("./navbar.php"); ?>
<div class="whole-body">
    <div class="support-container">
        <h1 class="title-h1">How Can We Help?</h1>
        <p class='p1'>Please describe your issue or concern below, and our support team will get back to you as soon as possible.</p>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form id="supportForm" method="post" action="./support.php">
            <textarea id="description" name="description" placeholder="Describe your issue or query here..." required></textarea>
            <button class="s-btn" type="submit">Submit</button>
        </form>
    </div>
    </div>
    <?php require("./footer.php"); ?>
    <?php
    require("./script-files.php");
    ?>
</body>
</html>