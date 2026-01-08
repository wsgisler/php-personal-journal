<?php
$currentYear = $_GET['year'] ?? date('Y');
$title = $_GET['title'] ?? '';

$content = getHighLevelEntryContent($currentYear, $title);
if ($content === false) {
    $content = '';
}
?>

<div class="row mt-4">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form id="planningEntryForm" onsubmit="savePlanningEntry(event)">
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
                            id="planningContent" 
                            name="content" 
                            class="form-control font-monospace"
                            rows="20"
                            placeholder="Start writing..."
                            required
                        ><?= htmlspecialchars($content) ?></textarea>
                    </div>

                    <input type="hidden" name="year" value="<?= htmlspecialchars($currentYear) ?>">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">

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
    const textarea = document.getElementById('planningContent');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const newText = before + selectedText + after;
    
    textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + before.length, end + before.length);
}

function insertList(numbered = false) {
    const textarea = document.getElementById('planningContent');
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

function savePlanningEntry(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const data = new URLSearchParams();
    data.append('ajax', '1');
    data.append('action', 'save_planning_entry');
    data.append('year', formData.get('year'));
    data.append('title', formData.get('title'));
    data.append('content', formData.get('content'));
    
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
            window.location.href = '?view=planning_view&year=' + formData.get('year') + '&title=' + encodeURIComponent(formData.get('title'));
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
