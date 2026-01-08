<?php
// Helper functions for the journal application

require_once 'config.php';

/**
 * Get the folder name for a given date (MMDD format)
 */
function getDateFolder($date) {
    return date('md', strtotime($date));
}

/**
 * Get the entry file path for a given date and year
 */
function getEntryFilePath($date, $year = null) {
    if ($year === null) {
        $year = date('Y', strtotime($date));
    }
    $folder = getDateFolder($date);
    return ENTRIES_DIR . '/' . $folder . '/' . $year . '.md';
}

/**
 * Get the anniversaries file path for a given date
 */
function getAnniversariesFilePath($date) {
    $folder = getDateFolder($date);
    return ENTRIES_DIR . '/' . $folder . '/anniversaries.txt';
}

/**
 * Get the photo link file path for a given date and year
 */
function getPhotoFilePath($date, $year = null) {
    if ($year === null) {
        $year = date('Y', strtotime($date));
    }
    $folder = getDateFolder($date);
    return ENTRIES_DIR . '/' . $folder . '/' . $year . '-photo.txt';
}

/**
 * Get all entries for a specific date across all years
 */
function getEntriesForDate($date) {
    $folder = getDateFolder($date);
    $folderPath = ENTRIES_DIR . '/' . $folder;
    
    $entries = [];
    
    if (!is_dir($folderPath)) {
        return $entries;
    }
    
    $files = scandir($folderPath);
    foreach ($files as $file) {
        if (preg_match('/^(\d{4})\.md$/', $file, $matches)) {
            $year = $matches[1];
            $content = file_get_contents($folderPath . '/' . $file);
            $summary = extractSummary($content);
            $photoLink = '';
            
            $photoFile = $folderPath . '/' . $year . '-photo.txt';
            if (file_exists($photoFile)) {
                $photoLink = trim(file_get_contents($photoFile));
            }
            
            $entries[$year] = [
                'year' => $year,
                'content' => $content,
                'summary' => $summary,
                'photo' => $photoLink,
                'file' => $folderPath . '/' . $file
            ];
        }
    }
    
    krsort($entries); // Sort by year, newest first
    return $entries;
}

/**
 * Extract the summary section from a markdown entry
 */
function extractSummary($content) {
    // Match content between # Summary and the next # heading
    if (preg_match('/# Summary\s*\n(.*?)(?=\n#|$)/s', $content, $matches)) {
        return trim($matches[1]);
    }
    return '';
}

/**
 * Get anniversaries for a specific date
 */
function getAnniversaries($date) {
    $file = getAnniversariesFilePath($date);
    
    if (!file_exists($file)) {
        return [];
    }
    
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $anniversaries = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $anniversaries[] = $line;
        }
    }
    
    return $anniversaries;
}

/**
 * Save anniversaries for a specific date
 */
function saveAnniversaries($date, $anniversaries) {
    $folder = getDateFolder($date);
    $folderPath = ENTRIES_DIR . '/' . $folder;
    
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    
    $file = getAnniversariesFilePath($date);
    $content = implode("\n", array_filter($anniversaries));
    
    return file_put_contents($file, $content) !== false;
}

/**
 * Create a new entry from template
 */
function createNewEntry($date, $year = null) {
    if ($year === null) {
        $year = date('Y', strtotime($date));
    }
    
    $folder = getDateFolder($date);
    $folderPath = ENTRIES_DIR . '/' . $folder;
    
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    
    $entryFile = getEntryFilePath($date, $year);
    
    if (!file_exists($entryFile)) {
        if (file_exists(TEMPLATE_FILE)) {
            $template = file_get_contents(TEMPLATE_FILE);
            file_put_contents($entryFile, $template);
        } else {
            $defaultTemplate = "# Summary\n\n\n# Top priorities for the day\n- \n\n# What goals do these priorities serve\n\n\n# Journal\n\n\n# What are some things that I am grateful for today?\n\n\n# Evening reflection: what went well, and what didn't?\n\n";
            file_put_contents($entryFile, $defaultTemplate);
        }
    }
    
    // Create anniversaries file if it doesn't exist
    $anniversariesFile = getAnniversariesFilePath($date);
    if (!file_exists($anniversariesFile)) {
        file_put_contents($anniversariesFile, '');
    }
    
    return $entryFile;
}

/**
 * Save entry content
 */
function saveEntry($date, $year, $content, $photoLink = '') {
    $folder = getDateFolder($date);
    $folderPath = ENTRIES_DIR . '/' . $folder;
    
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    
    $entryFile = getEntryFilePath($date, $year);
    $result = file_put_contents($entryFile, $content);
    
    // Save photo link if provided
    if (!empty($photoLink)) {
        $photoFile = getPhotoFilePath($date, $year);
        file_put_contents($photoFile, $photoLink);
    } else {
        // Delete photo file if link is empty
        $photoFile = getPhotoFilePath($date, $year);
        if (file_exists($photoFile)) {
            unlink($photoFile);
        }
    }
    
    return $result !== false;
}

/**
 * Format date for display
 */
function formatDateForDisplay($date) {
    return date('jS F', strtotime($date));
}

/**
 * Parse markdown to HTML (simple version)
 */
function markdownToHtml($text) {
    // Headers (must be processed before other formatting)
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
    
    // Bold (must come before italic to avoid conflicts)
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    
    // Strikethrough
    $text = preg_replace('/~~(.+?)~~/', '<s>$1</s>', $text);
    
    // Italic
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    
    // Lists - process before line breaks to avoid extra spacing
    $text = preg_replace('/^- (.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
    
    // Remove line breaks inside lists (between </li> and <li>)
    $text = preg_replace('/(<\/li>)\n(<li>)/', '$1$2', $text);
    
    // Paragraphs (double line breaks) - MUST be processed before single line breaks
    $text = preg_replace('/\n\n+/', '</p><p>', $text);
    $text = '<p>' . $text . '</p>';
    
    // Single line breaks (convert to <br>) - but not within ul tags or around headers
    $text = preg_replace('/(?<!<\/li>)(?<!<ul>)(?<!<\/h[1-3]>)\n(?!<li>)(?!<\/ul>)(?!<h[1-3]>)(?!<\/p>)(?!<p>)/', '<br>', $text);
    
    // Clean up empty paragraphs and paragraphs around headers/lists
    $text = preg_replace('/<p>\s*<\/p>/', '', $text);
    $text = preg_replace('/<\/p>\s*<(h[1-3]|ul)>/', '<$1>', $text);
    $text = preg_replace('/<\/(h[1-3]|ul)>\s*<p>/', '</$1>', $text);
    $text = preg_replace('/<p>(<ul>)/', '$1', $text);
    $text = preg_replace('/(<\/ul>)<\/p>/', '$1', $text);
    
    return $text;
}

/**
 * Get all high level planning entries for a specific year
 */
function getHighLevelEntries($year) {
    $folderPath = __DIR__ . '/highlevel/' . $year;
    $entries = [];
    
    if (!is_dir($folderPath)) {
        return $entries;
    }
    
    $files = scandir($folderPath);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $filePath = $folderPath . '/' . $file;
            $title = pathinfo($file, PATHINFO_FILENAME);
            $creationTime = filectime($filePath);
            
            $entries[] = [
                'title' => $title,
                'file' => $filePath,
                'created' => $creationTime
            ];
        }
    }
    
    // Sort by creation date (oldest first)
    usort($entries, function($a, $b) {
        return $a['created'] - $b['created'];
    });
    
    return $entries;
}

/**
 * Get all years with high level planning entries
 */
function getHighLevelYears() {
    $highlevelPath = __DIR__ . '/highlevel';
    $years = [];
    
    if (!is_dir($highlevelPath)) {
        return $years;
    }
    
    $folders = scandir($highlevelPath);
    foreach ($folders as $folder) {
        if ($folder !== '.' && $folder !== '..' && is_dir($highlevelPath . '/' . $folder)) {
            if (preg_match('/^\d{4}$/', $folder)) {
                $years[] = $folder;
            }
        }
    }
    
    // Sort years descending
    rsort($years);
    
    return $years;
}

/**
 * Get available high level planning templates
 */
function getHighLevelTemplates() {
    $templatesPath = __DIR__ . '/highlevel/templates';
    $templates = [];
    
    if (!is_dir($templatesPath)) {
        return $templates;
    }
    
    $files = scandir($templatesPath);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $templates[] = pathinfo($file, PATHINFO_FILENAME);
        }
    }
    
    return $templates;
}

/**
 * Create a new high level planning entry
 */
function createHighLevelEntry($year, $title, $template = '') {
    $folderPath = __DIR__ . '/highlevel/' . $year;
    
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    
    $fileName = $title . '.md';
    $filePath = $folderPath . '/' . $fileName;
    
    // Don't overwrite existing file
    if (file_exists($filePath)) {
        return false;
    }
    
    // Load template content if specified
    $content = '';
    if (!empty($template)) {
        $templatePath = __DIR__ . '/highlevel/templates/' . $template . '.md';
        if (file_exists($templatePath)) {
            $content = file_get_contents($templatePath);
        }
    }
    
    return file_put_contents($filePath, $content) !== false;
}

/**
 * Save high level planning entry
 */
function saveHighLevelEntry($year, $title, $content) {
    $folderPath = __DIR__ . '/highlevel/' . $year;
    
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    
    $fileName = $title . '.md';
    $filePath = $folderPath . '/' . $fileName;
    
    return file_put_contents($filePath, $content) !== false;
}

/**
 * Get high level planning entry content
 */
function getHighLevelEntryContent($year, $title) {
    $filePath = __DIR__ . '/highlevel/' . $year . '/' . $title . '.md';
    
    if (file_exists($filePath)) {
        return file_get_contents($filePath);
    }
    
    return false;
}
