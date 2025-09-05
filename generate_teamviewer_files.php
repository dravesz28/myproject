<?php
// Handle download requests
if (isset($_GET['type']) && isset($_GET['id']) && isset($_GET['password'])) {
    $type = $_GET['type'];
    $id = preg_replace('/\D/', '', $_GET['id']);
    $password = preg_replace('/[^A-Za-z0-9]/', '', $_GET['password']);

    if (!$id || !$password) {
        http_response_code(400);
        echo "Missing or invalid TeamViewer ID or password.";
        exit;
    }

    if ($type === 'bat') {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="connect_teamviewer.bat"');

        $paths = [
            'C:\Program Files\TeamViewer\TeamViewer.exe',
            'C:\Program Files (x86)\TeamViewer\TeamViewer.exe'
        ];

        echo "@echo off\n";
        echo "echo Launching TeamViewer with ID: $id\n";
        foreach ($paths as $path) {
            echo "if exist \"$path\" (\n";
            echo "    start \"\" \"$path\" -i $id --Password $password\n";
            echo "    exit /b\n";
            echo ")\n";
        }
        echo "echo TeamViewer not found on this system.\n";
        echo "pause\n";
        exit;

    } elseif ($type === 'tvn') {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="connect_teamviewer.tvn"');

        echo "[Connection]\n";
        echo "Version=9\n";
        echo "ID=$id\n";
        echo "Password=$password\n";
        exit;
    } else {
        http_response_code(400);
        echo "Invalid type specified.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TeamViewer Launcher Generator</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; }
        label, input { display: block; margin-bottom: 10px; }
        a.button {
            display: inline-block;
            margin-right: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Generate TeamViewer Connect Scripts</h2>

    <form method="get" action="">
        <label for="id">TeamViewer ID:</label>
        <input type="text" id="id" name="id" placeholder="e.g. 123456789" required>

        <label for="password">Password:</label>
        <input type="text" id="password" name="password" placeholder="e.g. Pts0123680039" required>

        <input type="submit" value="Show Download Links">
    </form>

    <?php if (!empty($_GET['id']) && !empty($_GET['password'])): 
        $id = htmlspecialchars($_GET['id']);
        $password = htmlspecialchars($_GET['password']);
    ?>
        <h3>Download:</h3>
        <a class="button" href="?type=bat&id=<?= $id ?>&password=<?= $password ?>">▶ Download BAT File</a>
        <a class="button" href="?type=tvn&id=<?= $id ?>&password=<?= $password ?>">📁 Download TVN File</a>
    <?php endif; ?>
</body>
</html>
