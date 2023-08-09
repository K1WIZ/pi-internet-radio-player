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

    // Read file and display records
    $records = array();
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        while (($record = fgetcsv($file)) !== false) {
            $records[$record[0]] = $record[1]; // Use record number as key
        }
        fclose($file);
    }

    function displayRecords($records) {
        echo '<h2>Station Presets:</h2>';
        echo '<table border="1">';
        echo '<tr><th>Preset Number</th><th>URL</th><th>Edit</th><th>Delete</th></tr>';
        foreach ($records as $recordNumber => $url) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($recordNumber) . '</td>';
            echo '<td>' . htmlspecialchars($url) . '</td>';
            echo '<td><a href="?edit=' . htmlspecialchars($recordNumber) . '">Edit</a></td>';
            echo '<td><a href="?delete=' . htmlspecialchars($recordNumber) . '">Delete</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }


    // Handle record editing
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editRecordNumber = $_GET['edit'];
        if (isset($records[$editRecordNumber])) {
            $editRecordUrl = $records[$editRecordNumber];
        }
    }

    // Handle record deletion
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $deleteRecordNumber = $_GET['delete'];
        if (isset($records[$deleteRecordNumber])) {
            unset($records[$deleteRecordNumber]);
            $file = fopen($filename, 'w');
            foreach ($records as $recordNumber => $url) {
                fputcsv($file, [$recordNumber, $url]);
            }
            fclose($file);
            echo '<p>Record deleted successfully.</p>';
        }
    }

    // Add or edit record
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add'])) {
            $newRecordUrl = trim($_POST['record']);
            $newRecordNumber = max(array_keys($records)) + 1; // Generate unique record number

            if (!empty($newRecordUrl) && filter_var($newRecordUrl, FILTER_VALIDATE_URL)) {
                $records[$newRecordNumber] = $newRecordUrl;
                $file = fopen($filename, 'a');
                fputcsv($file, [$newRecordNumber, $newRecordUrl]);
                fclose($file);
                echo '<p>Preset added successfully.</p>';
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo '<p>Invalid URL format. Please enter a valid URL.</p>';
            }
        } elseif (isset($_POST['edit']) && isset($editRecordNumber) && isset($editRecordUrl)) {
            $newEditValue = trim($_POST['edited_record']);

            if (!empty($newEditValue) && filter_var($newEditValue, FILTER_VALIDATE_URL)) {
                $records[$editRecordNumber] = $newEditValue;
                $file = fopen($filename, 'w');
                foreach ($records as $recordNumber => $url) {
                    fputcsv($file, [$recordNumber, $url]);
                }
                fclose($file);
                echo '<p>Preset edited successfully.</p>';
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo '<p>Invalid URL format. Please enter a valid URL.</p>';
            }
        }
    }
    ?>

    <h2>Add Station Preset URL:</h2>
    <form method="post">
        <label for="record">URL:</label>
        <input type="url" name="record" id="record" required>
        <button type="submit" name="add">Add Preset</button>
    </form>

    <?php if (isset($editRecordNumber) && isset($editRecordUrl)) : ?>
        <h2>Edit Record:</h2>
        <form method="post">
            <label for="edited_record">Edited URL:</label>
            <input type="url" name="edited_record" id="edited_record" value="<?php echo htmlspecialchars($editRecordUrl); ?>" required>
            <button type="submit" name="edit">Save Edit</button>
        </form>
    <?php endif; ?>
    <!--
    <h2>Clear Form:</h2>
    <form method="post">
        <button type="reset">Clear Form</button>
    </form>
    -->
    <?php displayRecords($records); ?>
</center>
</body>
</html>

