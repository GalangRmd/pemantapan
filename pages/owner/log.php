
<?php  
session_start();
require_once('../../db/DB_connection.php');
require_once('../../db/DB_logactivity.php');

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: pages/superadmin/data-karyawan.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Activity</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Log Activity</h2>
    <table>
        <tr>
            <th>Timestamp</th>
            <th>Activity</th>
        </tr>
        <?php
        // Baca isi file activity_log.txt dan tampilkan dalam tabel
        $log_file = "activity_log.txt";
        if (file_exists($log_file)) {
            $log_contents = file($log_file, FILE_IGNORE_NEW_LINES);
            foreach ($log_contents as $log) {
                list($timestamp, $activity) = explode(": ", $log, 2);
                echo "<tr><td>$timestamp</td><td>$activity</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No activity recorded yet.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
