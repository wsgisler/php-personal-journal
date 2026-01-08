<?php
$currentDate = $date;
$currentYear = $year;
$isEdit = isset($_GET['edit']);

$content = '';
$photoLink = '';

if ($isEdit) {
    $entryFile = getEntryFilePath($currentDate, $currentYear);
    if (file_exists($entryFile)) {
        $content = file_get_contents($entryFile);
    } else {
        createNewEntry($currentDate, $currentYear);
        $content = file_get_contents($entryFile);
    }
    
    $photoFile = getPhotoFilePath($currentDate, $currentYear);
    if (file_exists($photoFile)) {
        $photoLink = trim(file_get_contents($photoFile));
    }
} else {
    // Creating a new entry for current year
    $currentYear = date('Y', strtotime($currentDate));
    createNewEntry($currentDate, $currentYear);
    $entryFile = getEntryFilePath($currentDate, $currentYear);
    $content = file_get_contents($entryFile);
}

$displayDate = date('jS F, Y', strtotime($currentDate));
?>

<div class="row mt-4">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <?php if (!$isEdit): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h1 class="h3 fw-bold mb-2">New Journal Entry</h1>
                    </div>
                <?php endif; ?>

                <form id="entryForm" onsubmit="saveEntryForm(event)">
                    <div class="mb-3">
                        <div class="btn-toolbar mb-2" role="toolbar">
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="insertFormat('**', '**')" title="Bold"><strong>B</strong></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertFormat('*', '*')" title="Italic"><em>I</em></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertFormat('~~', '~~')" title="Strikethrough"><s>S</s></button>
                            </div>
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="insertList()" title="Bullet List">•</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertList(true)" title="Numbered List">1.</button>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="insertFormat('> ', '')" title="Quote">"</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertFormat('`', '`')" title="Code">&lt;&gt;</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertLink()" title="Link">🔗</button>
                            </div>
                        </div>
                        
                        <textarea 
                            id="entryContent" 
                            name="content" 
                            class="form-control font-monospace"
                            rows="20"
                            placeholder="Start writing your thoughts here..."
                            required
                        ><?= htmlspecialchars($content) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="photoLink" class="form-label text-muted">Link to 1 photo on Google Photos (optional)</label>
                        <div class="input-group">
                            <input 
                                type="url" 
                                id="photoLink" 
                                name="photo" 
                                class="form-control"
                                value="<?= htmlspecialchars($photoLink) ?>"
                                placeholder="https://photos.google.com/..."
                            >
                            <span class="input-group-text">📷</span>
                        </div>
                    </div>

                    <input type="hidden" name="date" value="<?= $currentDate ?>">
                    <input type="hidden" name="year" value="<?= $currentYear ?>">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function insertFormat(before, after) {
    const textarea = document.getElementById('entryContent');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const newText = before + selectedText + after;
    
    textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + before.length, end + before.length);
}

function insertList(numbered = false) {
    const textarea = document.getElementById('entryContent');
    const start = textarea.selectionStart;
    const prefix = numbered ? '1. ' : '- ';
    
    textarea.value = textarea.value.substring(0, start) + prefix + textarea.value.substring(start);
    textarea.focus();
    textarea.setSelectionRange(start + prefix.length, start + prefix.length);
}

function insertLink() {
    const url = prompt('Enter URL:');
    if (url) {
        const text = prompt('Enter link text:', url);
        insertFormat('[' + (text || url) + '](', url + ')');
    }
}

function saveEntryForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const data = new URLSearchParams();
    data.append('ajax', '1');
    data.append('action', 'save_entry');
    data.append('date', formData.get('date'));
    data.append('year', formData.get('year'));
    data.append('content', formData.get('content'));
    data.append('photo', formData.get('photo'));
    
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.location.href = '?view=entry&date=' + formData.get('date') + '&year=' + formData.get('year');
        } else {
            alert('Error saving entry');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving entry');
    });
}
</script>
