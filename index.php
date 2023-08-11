<!DOCTYPE html>
<html>
<head>
    <title>Net Radio Tuner</title>
    <meta name="viewport" content="width=device-width, initial-scale=0.5">
    <link rel="stylesheet" href="../dark.css">
</head>
<body><center>
    <h1>Net Radio Tuner</h1>
    <?php include './play.php'; ?>
    <?php include './volume.html'; ?>
    <?php

    $filename = '/opt/stations.csv';

    function readCsvFile($filename) {
        $records = array();
        if (file_exists($filename)) {
            $file = fopen($filename, 'r');
            while (($record = fgetcsv($file)) !== false) {
                $records[$record[0]] = array('url' => $record[1], 'label' => $record[2]); // Use record number as key
            }
            fclose($file);
        }
        return $records;
    }

    function saveCsvFile($filename, $records) {
        $file = fopen($filename, 'w');
        foreach ($records as $recordNumber => $recordData) {
            fputcsv($file, [$recordNumber, $recordData['url'], $recordData['label']]);
        }
        fclose($file);
    }

    function displayRecords($records) {
        echo '<h2>Station Presets</h2>';
        echo '<table border="0">';
        echo '<tr><th>Preset Number</th><th>URL</th><th>Label</th><th>Edit</th><th>Delete</th></tr>';
        foreach ($records as $recordNumber => $recordData) {
            $url = htmlspecialchars($recordData['url']);
            $label = htmlspecialchars($recordData['label']);
            echo '<tr>';
            echo '<td>' . htmlspecialchars($recordNumber) . '</td>';
            echo '<td>' . $url . '</td>';
            echo '<td>' . $label . '</td>';
            echo '<td><a href="?edit=' . htmlspecialchars($recordNumber) . '">Edit</a></td>';
            echo '<td><a href="?delete=' . htmlspecialchars($recordNumber) . '">Delete</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    $records = readCsvFile($filename);

    // Handle record editing
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editRecordNumber = $_GET['edit'];
        if (isset($records[$editRecordNumber])) {
            $editRecordUrl = $records[$editRecordNumber]['url'];
            $editRecordLabel = $records[$editRecordNumber]['label'];
        }
    }

    // Handle record deletion
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $deleteRecordNumber = $_GET['delete'];
        if (isset($records[$deleteRecordNumber])) {
            unset($records[$deleteRecordNumber]);
            saveCsvFile($filename, $records);
            echo '<p>Record deleted successfully.</p>';
        }
    }

// Handle record editing or adding form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add'])) {
            $newRecordUrl = trim($_POST['record']);
            $newRecordLabel = trim($_POST['label']);
            $newRecordNumber = max(array_keys($records)) + 1;

            if (!empty($newRecordUrl) && filter_var($newRecordUrl, FILTER_VALIDATE_URL)) {
                $records[$newRecordNumber] = array('url' => $newRecordUrl, 'label' => $newRecordLabel);
                saveCsvFile($filename, $records);
		echo '<p>Preset added successfully.</p>';
		header("Location: index.php");
		exit();
            } else {
                echo '<p>Invalid URL format. Please enter a valid URL.</p>';
            }
        } elseif (isset($_POST['edit'])) {
            $editedRecordNumber = $_POST['edited_record_number'];
            $editedRecordUrl = trim($_POST['edited_record']);
            $editedRecordLabel = trim($_POST['edited_label']);

            if (isset($records[$editedRecordNumber])) {
                if (!empty($editedRecordUrl) && filter_var($editedRecordUrl, FILTER_VALIDATE_URL)) {
                    $records[$editedRecordNumber]['url'] = $editedRecordUrl;
                    $records[$editedRecordNumber]['label'] = $editedRecordLabel;
                    saveCsvFile($filename, $records);
		    echo '<p>Preset edited successfully.</p>';
		    header("Location: index.php");
		    exit();
                } else {
                    echo '<p>Invalid URL format. Please enter a valid URL.</p>';
                }
            }
        }
    }


    ?>

    <h2>Add Station Preset URL</h2>
    <form method="post">
        <label for="record">URL:</label>
        <input type="url" name="record" id="record" required>
        <label for="label">Label:</label>
        <input type="text" name="label" id="label" required>
        <button type="submit" name="add">Add Preset</button>
    </form>

    <?php if (isset($editRecordNumber) && isset($editRecordUrl)) : ?>
        <h2>Edit Record</h2>
        <form method="post">
            <label for="edited_record">Edit URL:</label><br>
            <input type="url" name="edited_record" id="edited_record" value="<?php echo htmlspecialchars($editRecordUrl); ?>" required><br>
            <label for="edited_label">Edit Label:</label><br>
            <input type="text" name="edited_label" id="edited_label" value="<?php echo htmlspecialchars($editRecordLabel); ?>" required>
            <input type="hidden" name="edited_record_number" value="<?php echo htmlspecialchars($editRecordNumber); ?>">
            <button type="submit" name="edit">Save Edit</button>
        </form>
    <?php endif; ?>
    <?php displayRecords($records); ?>

</center>
</body>
</html>

