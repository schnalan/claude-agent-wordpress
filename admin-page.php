<?php if (!defined('ABSPATH')) exit;

$autonomy_level = get_option('claude_autonomy_level', 'supervised');
$learning_enabled = get_option('claude_learning_enabled', true);
?>

<div class="wrap claude-agent-wrap">
    <h1>Claude AI Agent</h1>
    
    <?php
    $api_key = get_option('claude_api_key', '');
    if (empty($api_key)):
    ?>
    <div class="notice notice-warning">
        <p><strong>API Key Required:</strong> Please add your Anthropic API key in <a href="<?php echo admin_url('admin.php?page=claude-agent-settings'); ?>">Settings</a></p>
    </div>
    <?php endif; ?>
    
    <div class="agent-status-bar">
        <div class="status-item">
            <span class="status-label">Autonomy:</span>
            <span class="status-value autonomy-<?php echo esc_attr($autonomy_level); ?>">
                <?php echo esc_html(ucfirst($autonomy_level)); ?>
            </span>
        </div>
        <div class="status-item">
            <span class="status-label">Learning:</span>
            <span class="status-value">
                <?php echo $learning_enabled ? '✓ Enabled' : '✗ Disabled'; ?>
            </span>
        </div>
        <div class="status-item">
            <span class="status-label">Model:</span>
            <span class="status-value">
                <?php 
                $model = get_option('claude_model', 'claude-sonnet-4-20250514');
                echo strpos($model, 'sonnet') !== false ? 'Sonnet 4' : 'Opus 4';
                ?>
            </span>
        </div>
    </div>
    
    <div class="claude-container">
        <div class="claude-sidebar">
            <div class="sidebar-section">
                <h3>Quick Actions</h3>
                <button class="button button-secondary claude-btn" id="analyze-site">
                    📊 Analyze Site
                </button>
                <button class="button button-secondary claude-btn" id="list-pages">
                    📄 List Pages
                </button>
                <button class="button button-secondary claude-btn" id="get-theme">
                    🎨 View Theme
                </button>
            </div>
            
            <div class="sidebar-section">
                <h3>Site Info</h3>
                <p><strong>Site:</strong> <?php echo esc_html(get_bloginfo('name')); ?></p>
                <p><strong>Theme:</strong> <?php echo esc_html(wp_get_theme()->get('Name')); ?></p>
                <p><strong>Pages:</strong> <?php echo wp_count_posts('page')->publish; ?></p>
                <p><strong>Posts:</strong> <?php echo wp_count_posts('post')->publish; ?></p>
            </div>
            
            <?php if ($learning_enabled): ?>
            <div class="sidebar-section">
                <h3>Learning Status</h3>
                <?php
                $learning_system = new Claude_Learning_System();
                $patterns = $learning_system->get_approval_patterns();
                ?>
                <p><strong>Interactions:</strong> <?php echo $patterns['total_interactions']; ?></p>
                <p><strong>Approval Rate:</strong> <?php echo $patterns['approval_rate']; ?>%</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="claude-main">
            <div class="chat-container">
                <div id="chat-messages" class="chat-messages">
                    <div class="welcome-message">
                        <h2>👋 Hello! I'm Claude, your WordPress AI agent.</h2>
                        <p>I'm running in <strong><?php echo esc_html($autonomy_level); ?> mode</strong>.</p>
                        <p>I can help you with:</p>
                        <ul>
                            <li>🎨 Theme customization</li>
                            <li>📝 Content creation and editing</li>
                            <li>🔍 Site analysis and optimization</li>
                            <li>⚡ Performance improvements</li>
                            <li>🛠️ Troubleshooting issues</li>
                        </ul>
                        <?php if ($learning_enabled): ?>
                        <p class="learning-note">
                            💡 <strong>Learning Mode Active:</strong> I'm learning from your preferences to provide better suggestions.
                        </p>
                        <?php endif; ?>
                        <p><strong>What would you like help with today?</strong></p>
                    </div>
                </div>
                
                <div class="chat-input-container">
                    <textarea id="chat-input" placeholder="Ask Claude anything about your WordPress site..." rows="3"></textarea>
                    <div class="input-actions">
                        <button id="send-message" class="button button-primary">
                            Send Message
                        </button>
                        <button id="clear-chat" class="button button-secondary">
                            Clear Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>