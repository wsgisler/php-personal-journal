<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom border-primary border-3 sticky-top">
        <div class="container-fluid">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $view === 'dashboard' ? 'active' : '' ?>" href="index.php?view=dashboard">Journal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $view === 'planning' ? 'active' : '' ?>" href="index.php?view=planning">High Level Planning</a>
                </li>
            </ul>
            <div class="d-flex">
                <a href="index.php?logout=1" class="text-muted text-decoration-none">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid" style="max-width: 1400px;">
