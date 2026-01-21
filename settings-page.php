<?php 
if (!defined('ABSPATH')) exit;

if (isset($_POST['submit']) && check_admin_referer('claude_agent_settings_nonce')) {
    update_option('claude_api_key', sanitize_text_field($_POST['claude_api_key']));
    update_option('claude_model', sanitize_text_field($_POST['claude_model']));
    update_option('claude_autonomy_level', sanitize_text_field($_POST['claude_autonomy_level']));
    update_option('claude_learning_enabled', isset($_POST['claude_learning_enabled']) ? 1 : 0);
    update_option('claude_scheduled_audits', isset($_POST['claude_scheduled_audits']) ? 1 : 0);
    update_option('claude_auto_fix_enabled', isset($_POST['claude_auto_fix_enabled']) ? 1 : 0);
    echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
}

$api_key = get_option('claude_api_key', '');
$model = get_option('claude_model', 'claude-sonnet-4-20250514');
$autonomy = get_option('claude_autonomy_level', 'supervised');
$learning = get_option('claude_learning_enabled', true);
$audits = get_option('claude_scheduled_audits', true);
$auto_fix = get_option('claude_auto_fix_enabled', false);
?>

<div class="wrap">
    <h1>Claude Agent Settings</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('claude_agent_settings_nonce'); ?>
        
        <h2>API Configuration</h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="claude_api_key">Anthropic API Key</label>
                </th>
                <td>
                    <input type="password" 
                           id="claude_api_key" 
                           name="claude_api_key" 
                           value="<?php echo esc_attr($api_key); ?>" 
                           class="regular-text">
                    <p class="description">
                        Get your API key from <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="claude_model">Claude Model</label>
                </th>
                <td>
                    <select id="claude_model" name="claude_model">
                        <option value="claude-sonnet-4-20250514" <?php selected($model, 'claude-sonnet-4-20250514'); ?>>
                            Claude Sonnet 4 (Recommended - Best Balance)
                        </option>
                        <option value="claude-opus-4-20250514" <?php selected($model, 'claude-opus-4-20250514'); ?>>
                            Claude Opus 4 (Most Powerful & Expensive)
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        
        <h2>Autonomous Features</h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="claude_autonomy_level">Autonomy Level</label>
                </th>
                <td>
                    <select id="claude_autonomy_level" name="claude_autonomy_level">
                        <option value="supervised" <?php selected($autonomy, 'supervised'); ?>>
                            Supervised - All changes require approval
                        </option>
                        <option value="semi-autonomous" <?php selected($autonomy, 'semi-autonomous'); ?>>
                            Semi-Autonomous - Minor fixes auto-approved
                        </option>
                        <option value="autonomous" <?php selected($autonomy, 'autonomous'); ?>>
                            Autonomous - Full automation (Advanced)
                        </option>
                    </select>
                    <p class="description">
                        <strong>Start with "Supervised"</strong> for maximum safety.
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Enable Auto-Fix</th>
                <td>
                    <label>
                        <input type="checkbox" name="claude_auto_fix_enabled" value="1" 
                            <?php checked($auto_fix, 1); ?>>
                        Allow Claude to automatically fix minor issues
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Scheduled Audits</th>
                <td>
                    <label>
                        <input type="checkbox" name="claude_scheduled_audits" value="1" 
                            <?php checked($audits, 1); ?>>
                        Run automated site health audits daily
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Enable Learning</th>
                <td>
                    <label>
                        <input type="checkbox" name="claude_learning_enabled" value="1" 
                            <?php checked($learning, 1); ?>>
                        Allow Claude to learn from your preferences
                    </label>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Save Settings'); ?>
    </form>
    
    <hr style="margin: 40px 0;">
    
    <h2>Current Status</h2>
    <table class="form-table">
        <tr>
            <th>API Key</th>
            <td>
                <?php if (empty($api_key)): ?>
                    <span style="color: #dc3545;">❌ Not configured</span>
                <?php else: ?>
                    <span style="color: #00a32a;">✅ Configured</span>
                    <code><?php echo substr($api_key, 0, 15); ?>...</code>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Model</th>
            <td><?php echo strpos($model, 'sonnet') !== false ? 'Sonnet 4' : 'Opus 4'; ?></td>
        </tr>
        <tr>
            <th>Autonomy Mode</th>
            <td><strong><?php echo ucfirst($autonomy); ?></strong></td>
        </tr>
        <tr>
            <th>Backups</th>
            <td><?php echo count(get_option('claude_agent_backups', array())); ?> stored</td>
        </tr>
        <tr>
            <th>Total Audits</th>
            <td><?php echo count(get_option('claude_agent_audits', array())); ?> completed</td>
        </tr>
    </table>
</div>