<?php
// Journal Application Configuration

// Password for accessing the journal
define('JOURNAL_PASSWORD', 'topsecret');

// Base directory for entries
define('ENTRIES_DIR', __DIR__ . '/entries');

// Template file
define('TEMPLATE_FILE', ENTRIES_DIR . '/template.md');

// Timezone (adjust as needed)
date_default_timezone_set('Europe/Zurich');
