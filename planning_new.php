<?php
$currentYear = $_GET['year'] ?? date('Y');
$templates = getHighLevelTemplates();
?>

<div class="row mt-4">
    <div class="col-lg-6 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h1 class="h3 fw-bold mb-4">New entry</h1>
                
                <form id="newPlanningForm">
                    <div class="mb-4">
                        <input type="text" id="entryTitle" class="form-control form-control-lg" placeholder="Title for this entry" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <div class="row g-3">
                            <?php foreach ($templates as $template): ?>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-primary w-100 py-4 template-btn" data-template="<?= htmlspecialchars($template) ?>">
                                        <div class="fw-bold"><?= htmlspecialchars(ucwords(str_replace('-', ' ', $template))) ?></div>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <input type="hidden" id="selectedTemplate" value="">
                    
                    <button type="submit" class="btn btn-primary btn-lg px-5">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Template selection
document.querySelectorAll('.template-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active state from all buttons
        document.querySelectorAll('.template-btn').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        });
        
        // Add active state to clicked button
        this.classList.remove('btn-outline-primary');
        this.classList.add('btn-primary');
        
        // Store selected template
        document.getElementById('selectedTemplate').value = this.dataset.template;
    });
});

// Form submission
document.getElementById('newPlanningForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const title = document.getElementById('entryTitle').value.trim();
    const template = document.getElementById('selectedTemplate').value;
    
    if (!title) {
        alert('Please enter a title');
        return;
    }
    
    const data = new URLSearchParams();
    data.append('ajax', '1');
    data.append('action', 'create_planning_entry');
    data.append('year', '<?= $currentYear ?>');
    data.append('title', title);
    data.append('template', template);
    
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
            window.location.href = '?view=planning_edit&year=<?= $currentYear ?>&title=' + encodeURIComponent(result.title);
        } else {
            alert('Error creating entry. An entry with this title may already exist.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating entry');
    });
});
</script>
