<?php
session_start();

$valid_email = "admin@gmail.com";
$valid_password = "admin123";
$error = "";


if (isset($_GET['action'])) {
    $_SESSION['intended_action'] = $_GET['action'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $action = $_SESSION['intended_action'] ?? '';

    if ($email === $valid_email && $password === $valid_password) {
        switch ($action) {
            case 'add':
                header("Location: add-product.php");
                break;
            case 'update':
                header("Location: update-product.php");
                break;
            case 'delete':
                header("Location: delete-product.php");
                break;
            default:
                $error = "Invalid or missing action.";
                break;
        }
        exit();
    } else {
        $error = "Invalid owner credentials.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 150px;
        }
    </style>
</head>
<body>

<form method="POST">
    <table>
        <tr>
            <th><h3>Owner's approval is required.</h3></th>
        </tr>

        <?php if ($error): ?>
        <tr>
            <td><div class="alert alert-danger"><?= $error ?></div></td>
        </tr>
        <?php endif; ?>

        <tr>
            <td>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input name="email" type="email" class="form-control" id="email" required>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="password" required>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Remember me</label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="home.php" class="btn btn-danger">Cancel</a>
            </td>
        </tr>
    </table>
</form>

</body>
</html>