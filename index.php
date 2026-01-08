<?php
// Session settings must be set before session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();
require_once 'config.php';
require_once 'functions.php';

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['password'] === JOURNAL_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Incorrect password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Check if logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

// Handle AJAX requests
if ($isLoggedIn && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'save_entry') {
        $date = $_POST['date'];
        $year = $_POST['year'];
        $content = $_POST['content'];
        $photo = $_POST['photo'] ?? '';
        
        $success = saveEntry($date, $year, $content, $photo);
        echo json_encode(['success' => $success]);
        exit;
    }
    
    if ($_POST['action'] === 'save_anniversaries') {
        $date = $_POST['date'];
        $anniversaries = explode("\n", $_POST['anniversaries']);
        
        $success = saveAnniversaries($date, $anniversaries);
        echo json_encode(['success' => $success]);
        exit;
    }
    
    if ($_POST['action'] === 'get_entry') {
        $date = $_POST['date'];
        $year = $_POST['year'];
        $entryFile = getEntryFilePath($date, $year);
        
        if (file_exists($entryFile)) {
            $content = file_get_contents($entryFile);
            $photoFile = getPhotoFilePath($date, $year);
            $photo = file_exists($photoFile) ? file_get_contents($photoFile) : '';
            
            echo json_encode([
                'success' => true,
                'content' => $content,
                'photo' => $photo
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
    
    if ($_POST['action'] === 'save_planning_entry') {
        $year = $_POST['year'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        
        $success = saveHighLevelEntry($year, $title, $content);
        echo json_encode(['success' => $success]);
        exit;
    }
    
    if ($_POST['action'] === 'create_planning_entry') {
        $year = $_POST['year'];
        $title = $_POST['title'];
        $template = $_POST['template'];
        
        $success = createHighLevelEntry($year, $title, $template);
        echo json_encode(['success' => $success, 'title' => $title]);
        exit;
    }
}

// Get current view
$view = $_GET['view'] ?? 'dashboard';
$date = $_GET['date'] ?? date('Y-m-d');
$year = $_GET['year'] ?? date('Y');

// Show login page if not logged in
if (!$isLoggedIn) {
    include 'login.php';
    exit;
}

// Include the appropriate view
include 'header.php';

if ($view === 'dashboard') {
    include 'dashboard.php';
} elseif ($view === 'entry') {
    include 'entry.php';
} elseif ($view === 'new_entry') {
    include 'new_entry.php';
} elseif ($view === 'planning') {
    include 'planning.php';
} elseif ($view === 'planning_new') {
    include 'planning_new.php';
} elseif ($view === 'planning_edit') {
    include 'planning_edit.php';
} elseif ($view === 'planning_view') {
    include 'planning_view.php';
}

include 'footer.php';
