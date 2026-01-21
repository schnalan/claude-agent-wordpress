<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>Learning & Insights</h1>
    <p class="description">Claude learns from your preferences and approval patterns to provide better assistance</p>
    
    <div class="learning-stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3 id="total-interactions">0</h3>
                <p>Total Interactions</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <h3 id="approval-rate">0%</h3>
                <p>Approval Rate</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <h3 id="preferred-changes">0</h3>
                <p>Learned Preferences</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🧠</div>
            <div class="stat-content">
                <h3 id="learning-status">
                    <?php echo get_option('claude_learning_enabled', true) ? 'Active' : 'Disabled'; ?>
                </h3>
                <p>Learning Status</p>
            </div>
        </div>
    </div>
    
    <div class="learning-section">
        <h2>Your Preferences</h2>
        <p>Based on your past approvals, Claude has learned these preferences:</p>
        <div id="preferences-list" class="preferences-container">
            <p class="loading">Loading preferences...</p>
        </div>
    </div>
    
    <div class="learning-section">
        <h2>Interaction Breakdown</h2>
        <div id="interaction-breakdown"></div>
    </div>
    
    <div class="learning-controls">
        <h2>Learning Controls</h2>
        <form method="post" action="options.php">
            <?php settings_fields('claude_agent_settings'); ?>
            <label>
                <input type="checkbox" name="claude_learning_enabled" value="1" 
                    <?php checked(get_option('claude_learning_enabled', true), 1); ?>>
                Enable continuous learning from interactions
            </label>
            <p class="description">When enabled, Claude learns from your approvals and rejections.</p>
            <?php submit_button('Save Learning Settings'); ?>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    loadLearningData();
    
    function loadLearningData() {
        $.ajax({
            url: claudeAgent.ajaxUrl,
            type: 'POST',
            data: {
                action: 'claude_get_learning_data',
                nonce: claudeAgent.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayLearningData(response.data);
                }
            }
        });
    }
    
    function displayLearningData(data) {
        $('#total-interactions').text(data.patterns.total_interactions || 0);
        $('#approval-rate').text((data.patterns.approval_rate || 0) + '%');
        $('#preferred-changes').text(data.preferences.preferred_changes?.length || 0);
        
        if (data.preferences.preferred_changes && data.preferences.preferred_changes.length > 0) {
            let html = '';
            data.preferences.preferred_changes.slice(0, 6).forEach(pref => {
                html += `<div class="preference-card">
                    <h4>${pref.type.replace(/_/g, ' ').toUpperCase()}</h4>
                    <p>Frequently used preference</p>
                </div>`;
            });
            $('#preferences-list').html(html);
        } else {
            $('#preferences-list').html('<p>No preferences learned yet. Continue using Claude to build your preference profile.</p>');
        }
        
        if (data.statistics) {
            let chartHtml = '<div class="interaction-chart">';
            for (const [type, stats] of Object.entries(data.statistics)) {
                const percentage = stats.total > 0 ? (stats.approved / stats.total * 100).toFixed(1) : 0;
                chartHtml += `
                    <div class="chart-bar">
                        <div class="chart-label">${type.replace(/_/g, ' ')}</div>
                        <div class="chart-visual">
                            <div class="chart-fill" style="width: ${percentage}%">
                                ${stats.approved}/${stats.total} (${percentage}%)
                            </div>
                        </div>
                    </div>
                `;
            }
            chartHtml += '</div>';
            $('#interaction-breakdown').html(chartHtml);
        }
    }
});
</script>

<style>
.learning-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 36px;
}

.stat-content h3 {
    margin: 0;
    font-size: 28px;
    color: #2271b1;
    font-weight: 700;
}

.stat-content p {
    margin: 5px 0 0;
    color: #666;
    font-size: 13px;
}

.learning-section, .learning-controls {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.preferences-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.preference-card {
    background: #f6f7f7;
    border: 1px solid #ddd;
    border-left: 4px solid #2271b1;
    padding: 15px;
    border-radius: 4px;
}

.preference-card h4 {
    margin: 0 0 10px;
    color: #1d2327;
}

.chart-bar {
    display: flex;
    align-items: center;
    margin: 10px 0;
}

.chart-label {
    width: 150px;
    font-weight: 600;
    font-size: 13px;
}

.chart-visual {
    flex: 1;
    height: 30px;
    background: #f6f7f7;
    border-radius: 4px;
    overflow: hidden;
}

.chart-fill {
    height: 100%;
    background: linear-gradient(90deg, #2271b1, #50e3c2);
    display: flex;
    align-items: center;
    padding: 0 10px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
}
</style>