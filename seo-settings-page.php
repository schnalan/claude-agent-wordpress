<?php
if (!defined('ABSPATH')) exit;

$api_key = get_option('claude_seo_api_key', '');
?>

<div class="wrap claude-seo-wrap">
    <h1>Claude SEO Settings</h1>
    
    <div class="claude-seo-card">
        <h2>API Configuration</h2>
        <p>To use AI-powered SEO features, you need a Claude API key from Anthropic.</p>
        
        <div class="notice notice-info inline">
            <p>
                <strong>Don't have an API key?</strong><br>
                Get your API key from <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a>
            </p>
        </div>
        
        <form id="api-settings-form">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="claude_api_key">Claude API Key</label>
                    </th>
                    <td>
                        <input type="password" id="claude_api_key" name="api_key" 
                               value="<?php echo esc_attr($api_key); ?>" 
                               class="regular-text" placeholder="sk-ant-...">
                        <p class="description">
                            Your API key is stored securely and never shared.
                        </p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">Save API Key</button>
                <?php if ($api_key): ?>
                    <button type="button" id="test-api-key" class="button">Test Connection</button>
                    <span id="test-result"></span>
                <?php endif; ?>
            </p>
        </form>
    </div>
    
    <div class="claude-seo-card">
        <h2>Features</h2>
        <p>What this plugin does:</p>
        <ul class="feature-list">
            <li>✓ AI-powered SEO analysis for posts and pages</li>
            <li>✓ Automatic meta description generation</li>
            <li>✓ Focus keyword optimization suggestions</li>
            <li>✓ Internal linking recommendations</li>
            <li>✓ Performance analysis and optimization</li>
            <li>✓ Image optimization recommendations</li>
            <li>✓ Database cleanup suggestions</li>
        </ul>
    </div>
    
    <div class="claude-seo-card">
        <h2>Usage Costs</h2>
        <p>This plugin uses the Claude API, which has usage costs:</p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Cost per Analysis</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Claude Sonnet 4</td>
                    <td>~$0.01 - $0.03 per SEO analysis</td>
                </tr>
            </tbody>
        </table>
        <p class="description">
            Costs are approximate and depend on content length. 
            Check <a href="https://www.anthropic.com/pricing" target="_blank">Anthropic's pricing page</a> for current rates.
        </p>
    </div>
    
    <div class="claude-seo-card">
        <h2>Privacy & Security</h2>
        <ul>
            <li>• Your API key is stored in your WordPress database</li>
            <li>• Content is sent to Anthropic's API for analysis only when you request it</li>
            <li>• No data is stored on external servers except during API calls</li>
            <li>• Review Anthropic's <a href="https://www.anthropic.com/privacy" target="_blank">privacy policy</a></li>
        </ul>
    </div>
</div>
