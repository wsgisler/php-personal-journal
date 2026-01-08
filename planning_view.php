<?php
$currentYear = $_GET['year'] ?? date('Y');
$title = $_GET['title'] ?? '';

$content = getHighLevelEntryContent($currentYear, $title);
if ($content === false) {
    $content = 'Entry not found.';
}
?>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="mb-4 pb-3 border-bottom">
                    <h1 class="h2 fw-bold mb-2"><?= htmlspecialchars($title) ?></h1>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php
                        // Split content into sections
                        $sections = preg_split('/^(# .+)$/m', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
                        
                        for ($i = 0; $i < count($sections); $i++) {
                            if (preg_match('/^# (.+)$/', $sections[$i], $matches)) {
                                $sectionTitle = $matches[1];
                                $sectionContent = isset($sections[$i + 1]) ? $sections[$i + 1] : '';
                                
                                echo '<div class="mb-4">';
                                echo '<h2 class="h5 fw-bold mb-2">' . htmlspecialchars($sectionTitle) . '</h2>';
                                echo '<div class="text-muted">' . markdownToHtml(trim($sectionContent)) . '</div>';
                                echo '</div>';
                                
                                $i++; // Skip the content part as we've already processed it
                            } else if (!empty(trim($sections[$i]))) {
                                // Content without a header
                                echo '<div class="mb-4">';
                                echo '<div class="text-muted">' . markdownToHtml(trim($sections[$i])) . '</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                        
                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-primary" onclick="editPlanningEntry('<?= htmlspecialchars($currentYear) ?>', '<?= htmlspecialchars(addslashes($title)) ?>')">✏️ Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editPlanningEntry(year, title) {
    window.location.href = '?view=planning_edit&year=' + year + '&title=' + encodeURIComponent(title);
}
</script>
