<?php
$currentDate = $date;
$currentYear = $year;

$entryFile = getEntryFilePath($currentDate, $currentYear);
$content = '';
$photoLink = '';

if (file_exists($entryFile)) {
    $content = file_get_contents($entryFile);
    $photoFile = getPhotoFilePath($currentDate, $currentYear);
    if (file_exists($photoFile)) {
        $photoLink = trim(file_get_contents($photoFile));
    }
}

$displayDate = date('jS F, Y', strtotime($currentDate));
?>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="mb-4 pb-3 border-bottom">
                    <h1 class="h2 fw-bold mb-2 d-inline"><?= $displayDate ?></h1>
                    <?php if (!empty($photoLink)): ?>
                        <a href="<?= htmlspecialchars($photoLink) ?>" target="_blank" class="ms-2 fs-4 text-decoration-none" title="View photo on Google Photos">📷</a>
                    <?php endif; ?>
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
                            }
                        }
                        ?>
                        
                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-primary" onclick="editEntry('<?= $currentDate ?>', '<?= $currentYear ?>')">✏️ Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editEntry(date, year) {
    window.location.href = '?view=new_entry&date=' + date + '&year=' + year + '&edit=1';
}
</script>
