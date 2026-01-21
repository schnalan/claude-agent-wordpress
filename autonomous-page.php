<?php
if (!defined('ABSPATH')) exit;

$autonomy_level = get_option('claude_autonomy_level', 'supervised');
$audits = get_option('claude_agent_audits', array());
?>

<div class="wrap">
    <h1>Autonomous Tasks & Monitoring</h1>
    <p class="description">Configure Claude's autonomous capabilities and monitor automatic actions</p>
    
    <div class="autonomy-config">
        <h2>Autonomy Level</h2>
        <form method="post" action="options.php">
            <?php settings_fields('claude_agent_settings'); ?>
            
            <div class="autonomy-levels">
                <label class="autonomy-level-card <?php echo $autonomy_level === 'supervised' ? 'active' : ''; ?>">
                    <input type="radio" name="claude_autonomy_level" value="supervised" 
                        <?php checked($autonomy_level, 'supervised'); ?>>
                    <div class="level-header">
                        <span class="level-icon">👀</span>
                        <h3>Supervised</h3>
                    </div>
                    <p>All changes require manual approval. Maximum control and safety.</p>
                    <ul>
                        <li>✓ Full approval workflow</li>
                        <li>✓ No automatic changes</li>
                        <li>✓ Highest security</li>
                    </ul>
                </label>
                
                <label class="autonomy-level-card <?php echo $autonomy_level === 'semi-autonomous' ? 'active' : ''; ?>">
                    <input type="radio" name="claude_autonomy_level" value="semi-autonomous" 
                        <?php checked($autonomy_level, 'semi-autonomous'); ?>>
                    <div class="level-header">
                        <span class="level-icon">⚡</span>
                        <h3>Semi-Autonomous</h3>
                    </div>
                    <p>Minor fixes approved automatically. Major changes need approval.</p>
                    <ul>
                        <li>✓ Auto-fix minor issues</li>
                        <li>✓ Manual approval for big changes</li>
                        <li>✓ Balanced approach</li>
                    </ul>
                </label>
                
                <label class="autonomy-level-card <?php echo $autonomy_level === 'autonomous' ? 'active' : ''; ?>">
                    <input type="radio" name="claude_autonomy_level" value="autonomous" 
                        <?php checked($autonomy_level, 'autonomous'); ?>>
                    <div class="level-header">
                        <span class="level-icon">🤖</span>
                        <h3>Autonomous</h3>
                    </div>
                    <p>Claude operates independently based on learned preferences.</p>
                    <ul>
                        <li>✓ Full automation</li>
                        <li>✓ AI-driven decisions</li>
                        <li>⚠️ Review logs regularly</li>
                    </ul>
                </label>
            </div>
            
            <?php submit_button('Save Autonomy Level'); ?>
        </form>
    </div>
    
    <div class="scheduled-tasks">
        <h2>Scheduled Tasks</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Frequency</th>
                    <th>Next Run</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Site Health Audit</td>
                    <td>Daily</td>
                    <td>
                        <?php 
                        $next_run = wp_next_scheduled('claude_agent_scheduled_audit');
                        echo $next_run ? date('Y-m-d H:i', $next_run) : 'Not scheduled';
                        ?>
                    </td>
                    <td><span class="status-badge active">Active</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="audit-results">
        <h2>Recent Audits</h2>
        <?php if (!empty($audits)): ?>
            <?php 
            $recent_audits = array_slice(array_reverse($audits), 0, 5);
            foreach ($recent_audits as $audit): 
            ?>
            <div class="audit-card">
                <div class="audit-header">
                    <strong>Audit: <?php echo esc_html($audit['timestamp']); ?></strong>
                </div>
                <div class="audit-content">
                    <p><strong>Site Health:</strong></p>
                    <ul>
                        <li>Pages: <?php echo $audit['site_health']['total_pages']; ?></li>
                        <li>Posts: <?php echo $audit['site_health']['total_posts']; ?></li>
                        <li>Active Plugins: <?php echo $audit['site_health']['active_plugins']; ?></li>
                    </ul>
                    
                    <?php if (!empty($audit['issues_found'])): ?>
                    <p><strong>Issues Found:</strong></p>
                    <ul>
                        <?php foreach ($audit['issues_found'] as $severity => $issues): ?>
                            <?php foreach ($issues as $issue): ?>
                            <li class="issue-<?php echo $severity; ?>">
                                [<?php echo strtoupper($severity); ?>] <?php echo esc_html($issue['message']); ?>
                            </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No audits run yet. The first scheduled audit will run soon.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.autonomy-config, .scheduled-tasks, .audit-results {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.autonomy-levels {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.autonomy-level-card {
    border: 2px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.autonomy-level-card:hover {
    border-color: #2271b1;
    box-shadow: 0 2px 8px rgba(34, 113, 177, 0.1);
}

.autonomy-level-card.active {
    border-color: #2271b1;
    background: #e8f4f8;
}

.autonomy-level-card input[type="radio"] {
    display: none;
}

.level-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.level-icon {
    font-size: 32px;
}

.level-header h3 {
    margin: 0;
    font-size: 18px;
}

.autonomy-level-card p {
    color: #666;
    margin: 10px 0;
    line-height: 1.5;
}

.autonomy-level-card ul {
    list-style: none;
    padding: 0;
    margin: 15px 0 0;
}

.autonomy-level-card li {
    padding: 5px 0;
    color: #50575e;
    font-size: 13px;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.active {
    background: #d5f4e6;
    color: #00a32a;
}

.audit-card {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
}

.audit-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.audit-content ul {
    margin: 10px 0;
    padding-left: 20px;
}

.issue-critical {
    color: #dc3545;
    font-weight: 600;
}

.issue-warning {
    color: #ffc107;
}

.issue-info {
    color: #17a2b8;
}
</style>