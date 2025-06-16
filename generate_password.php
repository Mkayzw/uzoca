<?php
$hash = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (!empty($password)) {
$hash = password_hash($password, PASSWORD_DEFAULT);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Password Hash Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .hash-result {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
            word-break: break-all;
        }
        .copy-btn {
            background-color: #2196F3;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Password Hash Generator</h2>
    <form method="post">
        <div class="form-group">
            <label for="password">Enter Password:</label>
            <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
        </div>
        <button type="submit" class="submit-btn">Generate Hash</button>
    </form>

    <?php if (!empty($hash)): ?>
    <div class="hash-result">
        <h3>Generated Hash:</h3>
        <p id="hash-text"><?php echo htmlspecialchars($hash); ?></p>
        <button class="copy-btn" onclick="copyHash()">Copy Hash</button>
    </div>

    <script>
    function copyHash() {
        const hashText = document.getElementById('hash-text').textContent;
        navigator.clipboard.writeText(hashText).then(() => {
            alert('Hash copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
    </script>
    <?php endif; ?>
</body>
</html> 