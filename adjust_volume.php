<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $volume = $_POST["volume"];

    // Validate the volume input to ensure it's within the allowed range
    if ($volume < 0 || $volume > 100) {
        echo "Invalid volume level!";
        exit;
    }

    // Construct the command
    $command = "sudo amixer -c 3 set PCM {$volume}%";

    // Execute the command using shell_exec (you might need to adjust permissions)
    shell_exec($command);

    // Redirect back to index.php
    header("Location: index.php");
    exit; // Make sure to exit after sending the header
}
?>

