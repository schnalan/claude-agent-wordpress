<?php
if (!defined('ABSPATH')) exit;

$workflow_engine = new Claude_Workflow_Engine();
$workflows = $workflow_engine->get_available_workflows();
?>

<div class="wrap">
    <h1>Autonomous Workflows</h1>
    <p class="description">Execute multi-step automated tasks with Claude AI Agent</p>
    
    <div class="workflow-grid">
        <?php foreach ($workflows as $workflow_id => $workflow): ?>
        <div class="workflow-card">
            <div class="workflow-header">
                <h2><?php echo esc_html($workflow['name']); ?></h2>
                <span class="workflow-badge"><?php echo count($workflow['steps']); ?> steps</span>
            </div>
            <p class="workflow-description"><?php echo esc_html($workflow['description']); ?></p>
            
            <div class="workflow-steps">
                <h4>Steps:</h4>
                <ol>
                    <?php foreach ($workflow['steps'] as $step): ?>
                    <li><?php echo esc_html(ucwords(str_replace('_', ' ', $step))); ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
            
            <button class="button button-primary execute-workflow" 
                    data-workflow="<?php echo esc_attr($workflow_id); ?>">
                Execute Workflow
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="workflow-results" id="workflow-results" style="display:none;">
        <h2>Workflow Execution Results</h2>
        <div id="workflow-progress"></div>
        <div id="workflow-output"></div>
    </div>
    
    <div class="create-custom-workflow">
        <h2>Create Custom Workflow</h2>
        <p>Ask Claude to create a custom workflow for your specific needs:</p>
        
        <textarea id="custom-workflow-request" rows="4" class="large-text" 
                  placeholder="Example: Create a workflow that updates all blog post images, optimizes their alt tags, and generates a performance report"></textarea>
        <br>
        <button class="button button-secondary" id="create-custom-workflow">Create Custom Workflow</button>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.execute-workflow').on('click', function() {
        const workflowType = $(this).data('workflow');
        executeWorkflow(workflowType);
    });
    
    $('#create-custom-workflow').on('click', function() {
        const request = $('#custom-workflow-request').val();
        if (!request.trim()) {
            alert('Please describe the workflow you want to create');
            return;
        }
        alert('Custom workflow creation: This would send your request to Claude to design a tailored workflow. Feature coming soon!');
    });
    
    function executeWorkflow(workflowType) {
        $('#workflow-results').show();
        $('#workflow-progress').html('<div class="progress-step active">🔄 Initializing workflow...</div>');
        
        $.ajax({
            url: claudeAgent.ajaxUrl,
            type: 'POST',
            data: {
                action: 'claude_execute_workflow',
                nonce: claudeAgent.nonce,
                workflow_type: workflowType,
                parameters: JSON.stringify({})
            },
            success: function(response) {
                if (response.success) {
                    displayWorkflowResults(response.data);
                } else {
                    $('#workflow-progress').html('<div class="progress-step error">❌ Workflow failed: ' + response.data + '</div>');
                }
            },
            error: function() {
                $('#workflow-progress').html('<div class="progress-step error">❌ Network error occurred</div>');
            }
        });
    }
    
    function displayWorkflowResults(data) {
        let html = '';
        
        for (const [step, result] of Object.entries(data.results)) {
            const status = result.success ? 'complete' : 'error';
            const icon = result.success ? '✅' : '❌';
            html += `<div class="progress-step ${status}">
                ${icon} <strong>${step.replace(/_/g, ' ')}:</strong> ${result.data}
            </div>`;
        }
        
        $('#workflow-progress').html(html);
        $('#workflow-output').html(`<p><strong>✓ ${data.message}</strong></p>`);
    }
});
</script>

<style>
.workflow-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.workflow-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s ease;
}

.workflow-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.workflow-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.workflow-header h2 {
    margin: 0;
    font-size: 18px;
}

.workflow-badge {
    background: #2271b1;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.workflow-description {
    color: #666;
    margin-bottom: 15px;
    line-height: 1.5;
}

.workflow-steps h4 {
    margin: 10px 0 5px;
    font-size: 13px;
    font-weight: 600;
}

.workflow-steps ol {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
}

.workflow-steps li {
    margin: 4px 0;
}

.execute-workflow {
    width: 100%;
    margin-top: 15px;
}

.workflow-results {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.progress-step {
    padding: 12px;
    margin: 8px 0;
    background: #f6f7f7;
    border-left: 4px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.progress-step.active {
    border-left-color: #2271b1;
    background: #e8f4f8;
    animation: pulse 1.5s ease-in-out infinite;
}

.progress-step.complete {
    border-left-color: #00a32a;
    background: #d5f4e6;
}

.progress-step.error {
    border-left-color: #dc3545;
    background: #f8d7da;
    color: #721c24;
}

@keyframes pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

.create-custom-workflow {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.create-custom-workflow h2 {
    margin-top: 0;
}

#custom-workflow-request {
    width: 100%;
    margin: 15px 0;
    font-family: inherit;
}
</style>
```

---

## 📂 Complete File Structure for v2.0
```
claude-agent/
├── claude-agent.php                    (Main plugin file - v2.0)
├── assets/
│   ├── admin.css                       (Complete v2.0 styles)
│   └── admin.js                        (Complete v2.0 JavaScript)
└── templates/
    ├── admin-page.php                  (Main chat interface)
    ├── workflows-page.php              (Autonomous workflows)
    ├── learning-page.php               (AI learning insights)
    ├── autonomous-page.php             (Autonomy configuration)
    └── settings-page.php               (Plugin settings)