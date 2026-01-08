<?php
$currentYear = date('Y');
$years = getHighLevelYears();

// Ensure current year is in the list
if (!in_array($currentYear, $years)) {
    array_unshift($years, $currentYear);
}
?>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h1 class="h2 fw-bold mb-4">High Level Planning & Review</h1>
                
                <?php foreach ($years as $year): ?>
                    <div class="mb-4">
                        <h2 class="h3 fw-bold mb-3"><?= $year ?></h2>
                        
                        <?php 
                        $entries = getHighLevelEntries($year);
                        
                        if (!empty($entries)):
                            foreach ($entries as $entry):
                        ?>
                            <div class="mb-2">
                                <a href="?view=planning_view&year=<?= $year ?>&title=<?= urlencode($entry['title']) ?>" class="text-decoration-none text-dark">
                                    - <?= htmlspecialchars($entry['title']) ?>
                                </a>
                            </div>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                        
                        <?php if ($year == $currentYear): ?>
                            <div class="mt-2">
                                <a href="?view=planning_new&year=<?= $currentYear ?>" class="text-primary text-decoration-none">Add a new entry...</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
