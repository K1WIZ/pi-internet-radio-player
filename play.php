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

// Function to play selected stream and kill existing mplayer processes
function playStream($url) {
    //shell_exec('sudo /usr/bin/killall mplayer');   // changed from mplayer to vlc to support https streams
    shell_exec('sudo /usr/bin/killall vlc');
    //$command = "sudo /usr/bin/screen -m -d mplayer -ao alsa:device=hw=3.0 $url";  // changed from mplayer to vlc to support https streams
    $command = "sudo runuser -l orangepi -c 'cvlc --alsa-audio-device default:CARD=USB $url >/dev/null 2>&1 &'";
    shell_exec($command);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedRecord = $_POST["recordNumber"];
    if (isset($stations[$selectedRecord])) {
        $selectedUrl = $stations[$selectedRecord];
        playStream($selectedUrl);
    } else {
        $errorMessage = "Invalid record number.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stream Player</title>
</head>
<body>
    
    <?php if (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <!--   removed the table as it's already on the index
    <h2>Saved Station Presets:</h2>
    <table border="1">
        <tr>
            <th>Preset Number</th>
            <th>Stream URL</th>
        </tr> 
        <?php foreach ($stations as $recordNumber => $url): ?>
            <tr>
                <td><?php echo $recordNumber; ?></td>
                <td><?php echo $url; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    -->
    <form method="post" action="">
        <label for="recordNumber">Select a station preset to play:</label>
        <select name="recordNumber" id="recordNumber">
            <?php foreach ($stations as $recordNumber => $url): ?>
                <option value="<?php echo $recordNumber; ?>"><?php echo $recordNumber; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">TUNE</button>
    </form>
</body>
</html>

