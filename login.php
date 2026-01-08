<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <form method="POST" action="index.php">
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required autofocus>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary btn-lg w-100">Login</button>
                            <?php if (isset($loginError)): ?>
                                <div class="alert alert-danger mt-3 mb-0"><?= htmlspecialchars($loginError) ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
