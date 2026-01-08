<?php
$currentDate = $date;
$entries = getEntriesForDate($currentDate);
$anniversaries = getAnniversaries($currentDate);

// Get date navigation
$prevDate = date('Y-m-d', strtotime($currentDate . ' -1 day'));
$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
$displayDate = formatDateForDisplay($currentDate);
?>

<div class="row mt-4 g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-center align-items-center mb-4">
                    <a href="?view=dashboard&date=<?= $prevDate ?>" class="btn btn-link text-primary fs-3 text-decoration-none">‹</a>
                    <h2 class="mx-4 mb-0" onclick="openDatePicker()" style="cursor: pointer;" title="Click to select a date"><?= $displayDate ?></h2>
                    <a href="?view=dashboard&date=<?= $nextDate ?>" class="btn btn-link text-primary fs-3 text-decoration-none">›</a>
                </div>

                <?php 
                // Check if current year entry exists
                $currentYear = date('Y');
                $hasCurrentYearEntry = isset($entries[$currentYear]);
                
                // Show current year entry or placeholder first
                if (!$hasCurrentYearEntry): 
                ?>
                    <div class="mb-4 pb-4 border-bottom">
                        <h3 class="h4 fw-bold mb-2 text-dark"><?= $currentYear ?></h3>
                        <a href="?view=new_entry&date=<?= $currentDate ?>" class="text-primary text-decoration-none">Make a new entry...</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($entries as $entry): ?>
                    <div class="mb-4 pb-4 <?= $entry !== end($entries) ? 'border-bottom' : '' ?>">
                        <h3 class="h4 fw-bold mb-2 text-dark">
                            <a href="?view=entry&date=<?= $currentDate ?>&year=<?= $entry['year'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($entry['year']) ?>
                            </a>
                            <?php if (!empty($entry['photo'])): ?>
                                <a href="<?= htmlspecialchars($entry['photo']) ?>" target="_blank" class="text-decoration-none ms-2" title="View photo on Google Photos">📷</a>
                            <?php endif; ?>
                        </h3>
                        <a href="?view=entry&date=<?= $currentDate ?>&year=<?= $entry['year'] ?>" class="text-decoration-none">
                            <div class="text-muted">
                                <?= nl2br(htmlspecialchars($entry['summary'])) ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="h5 fw-bold mb-4">On this day</h3>
                
                <div class="mb-4">
                    <h4 class="h6 fw-semibold mb-3">📧 Emails</h4>
                    <div class="row g-2">
                        <?php
                        $currentYearNum = date('Y');
                        $years = [];
                        for ($i = 0; $i < 12; $i++) {
                            $years[] = $currentYearNum - $i;
                        }
                        
                        foreach ($years as $y):
                            // Construct date for this year with same month/day
                            $yearDate = $y . '-' . date('m-d', strtotime($currentDate));
                            $dateBefore = date('Y/m/d', strtotime($yearDate . ' -1 day'));
                            $dateAfter = date('Y/m/d', strtotime($yearDate . ' +3 days')); // +3 because 'before' is exclusive in Gmail
                            $gmailUrl = 'https://mail.google.com/mail/u/0/#search/after:' . $dateBefore . '+before:' . $dateAfter;
                        ?>
                            <div class="col-4">
                                <a href="<?= $gmailUrl ?>" target="_blank" class="btn btn-primary w-100 btn-sm text-decoration-none"><?= $y ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="h6 fw-semibold mb-3">📷 Photos</h4>
                    <div class="row g-2">
                        <?php 
                        foreach ($years as $y): 
                            // Construct date for this year with same month/day
                            $yearDate = $y . '-' . date('m-d', strtotime($currentDate));
                            $photoDate = str_replace('/','%2F', date('Y/m/d', strtotime($yearDate)));
                            $photosUrl = 'https://photos.google.com/search/' . $photoDate;
                        ?>
                            <div class="col-4">
                                <a href="<?= $photosUrl ?>" target="_blank" class="btn btn-primary w-100 btn-sm text-decoration-none"><?= $y ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="h6 fw-semibold mb-0">Birthdays / Anniversaries</h4>
                        <button class="btn btn-link text-decoration-none p-0" onclick="openAnniversariesModal('<?= $currentDate ?>')" title="Edit">✏️</button>
                    </div>
                    <div class="anniversaries-list">
                        <?php if (empty($anniversaries)): ?>
                            <p class="text-muted small fst-italic">No anniversaries recorded</p>
                        <?php else: ?>
                            <?php foreach ($anniversaries as $anniversary): ?>
                                <div class="mb-2">
                                    <?php
                                    // Check if it starts with a special character for birthday/anniversary
                                    if (strpos($anniversary, 'Peti') !== false || strpos($anniversary, 'Leo') !== false) {
                                        echo '🎂 ';
                                    } else {
                                        echo '- ';
                                    }
                                    echo htmlspecialchars($anniversary);
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Picker Modal -->
<div class="modal fade" id="datePickerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="date" id="datePicker" class="form-control" value="<?= $currentDate ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="goToSelectedDate()">Go to Date</button>
            </div>
        </div>
    </div>
</div>

<!-- Anniversaries Modal -->
<div class="modal fade" id="anniversariesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Birthdays / Anniversaries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="anniversariesText" class="form-control" rows="10" placeholder="One entry per line..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAnniversaries()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEditDate = '';
let anniversariesModal;
let datePickerModal;

document.addEventListener('DOMContentLoaded', function() {
    anniversariesModal = new bootstrap.Modal(document.getElementById('anniversariesModal'));
    datePickerModal = new bootstrap.Modal(document.getElementById('datePickerModal'));
});

function openDatePicker() {
    datePickerModal.show();
}

function goToSelectedDate() {
    const selectedDate = document.getElementById('datePicker').value;
    if (selectedDate) {
        window.location.href = '?view=dashboard&date=' + selectedDate;
    }
}

function openAnniversariesModal(date) {
    currentEditDate = date;
    const textarea = document.getElementById('anniversariesText');
    
    // Load current anniversaries
    const anniversariesList = document.querySelector('.anniversaries-list').innerText;
    // Remove the emoji prefixes and clean up
    let cleanText = anniversariesList.replace(/🎂\s*/g, '').replace(/-\s*/g, '').trim();
    if (cleanText === 'No anniversaries recorded') {
        cleanText = '';
    }
    textarea.value = cleanText;
    
    anniversariesModal.show();
}

function saveAnniversaries() {
    const textarea = document.getElementById('anniversariesText');
    const anniversaries = textarea.value;
    
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ajax=1&action=save_anniversaries&date=' + encodeURIComponent(currentEditDate) + 
              '&anniversaries=' + encodeURIComponent(anniversaries)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error saving anniversaries');
        }
    });
}
</script>
