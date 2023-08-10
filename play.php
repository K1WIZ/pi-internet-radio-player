<?php
// Path to the CSV file
$csvFilePath = '/opt/stations.csv';

// Read CSV file and store data in an associative array
$stations = [];
if (($handle = fopen($csvFilePath, 'r')) !== false) {
    while (($data = fgetcsv($handle)) !== false) {
        $stations[$data[0]] = $data[1];
    }
    fclose($handle);
}

// Function to kill stream if stop requested
function stopStream() {
    shell_exec('sudo /usr/bin/killall vlc');
}

// Function to play selected stream and kill existing mplayer processes
function playStream($url) {
    stopStream();
    $command = "sudo runuser -l orangepi -c 'cvlc --alsa-audio-device default:CARD=USB $url >/dev/null 2>&1 &'";
    shell_exec($command);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["tune"])) {
        // "TUNE" button clicked
        $selectedRecord = $_POST["recordNumber"];
        if (isset($stations[$selectedRecord])) {
            $selectedUrl = $stations[$selectedRecord];
            playStream($selectedUrl);
        } else {
            $errorMessage = "Invalid record number.";
        }
    } elseif (isset($_POST["stopPlayback"])) {
        // "STOP PLAYBACK" button clicked
        stopStream();
        $playbackStopped = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <?php if (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <?php if (isset($playbackStopped)): ?>
        <p style="color: green;">Playback stopped.</p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="recordNumber">Select a station preset to play:</label>
        <select name="recordNumber" id="recordNumber">
            <?php foreach ($stations as $recordNumber => $url): ?>
                <option value="<?php echo $recordNumber; ?>"><?php echo $recordNumber; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="tune">TUNE</button>
        <button type="submit" name="stopPlayback">STOP PLAYBACK</button>
    </form>
</body>
</html>
