<?php
// Configuration file path
$configFilePath = '/etc/ntune/output.conf';

// Read the current config value (if the file exists)
$currentConfigValue = '';
if (file_exists($configFilePath)) {
    $currentConfigValue = trim(file_get_contents($configFilePath));
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["set_output"])) {
    // Get the selected sound output option from the form
    $selectedOutput = $_POST["sound_output"];

    // Validate and sanitize the input (you should enhance this part)
    // ...

    // Write the selected option to the config file
    file_put_contents($configFilePath, $selectedOutput);

    // Update the current config value
    $currentConfigValue = $selectedOutput;
}

// Execute the command to get available sound output options
$outputOptions = shell_exec("sudo aplay -l | grep 'card' | awk '{ print $3 }'");
$outputOptionsArray = explode("\n", trim($outputOptions));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Output Configuration</title>
</head>
<body>
    <h1>Sound Output Configuration</h1>
    <form method="POST" action="">
        <?php
        foreach ($outputOptionsArray as $option) {
            $option = trim($option);  // Remove any leading/trailing whitespace
            if (!empty($option)) {
                echo '<label>';
                echo '<input type="radio" name="sound_output" value="' . htmlspecialchars($option) . '"';
                if ($option === $currentConfigValue) {
                    echo ' checked';
                }
                echo '>';
                echo htmlspecialchars($option);
                echo '</label><br>';
            }
        }
        ?>
        <br>
        <input type="submit" name="set_output" value="Set Output">
    </form>
    <h2>Current Sound Output: <?php echo htmlspecialchars($currentConfigValue); ?></h2>
</body>
</html>

