// Claude AI Agent v2.0.1 - With Working Change Detection
(function($) {
    'use strict';
    
    let conversationContext = {};
    let pendingChanges = null;
    
    $(document).ready(function() {
        initializeChat();
        initializeQuickActions();
        initializeApprovalSystem();
    });
    
    function initializeChat() {
        $('#send-message').on('click', sendMessage);
        $('#clear-chat').on('click', clearChat);
        
        $('#chat-input').on('keypress', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    function initializeQuickActions() {
        $('#analyze-site').on('click', analyzeSite);
        $('#list-pages').on('click', listPages);
        $('#get-theme').on('click', getTheme);
    }
    
    function initializeApprovalSystem() {
        // Approval modal will be created dynamically
    }
    
    function sendMessage() {
        const message = $('#chat-input').val().trim();
        
        if (!message) {
            return;
        }
        
        addMessageToChat('user', message);
        $('#chat-input').val('');
        showTypingIndicator();
        
        $.ajax({
            url: claudeAgent.ajaxUrl,
            type: 'POST',
            data: {
                action: 'claude_chat',
                nonce: claudeAgent.nonce,
                message: message,
                context: JSON.stringify(conversationContext)
            },
            success: function(response) {
                hideTypingIndicator();
                
                if (response.success) {
                    const claudeResponse = response.data.response;
                    addMessageToChat('assistant', claudeResponse);
                    
                    // Check if Claude is proposing changes
                    detectAndPromptForChanges(claudeResponse);
                    
                    if (response.data.usage) {
                        console.log('API Usage:', response.data.usage);
                    }
                } else {
                    addMessageToChat('error', 'Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                hideTypingIndicator();
                addMessageToChat('error', 'Failed to communicate with Claude: ' + error);
            }
        });
    }
    
    function detectAndPromptForChanges(response) {
        // Look for keywords indicating Claude wants to make changes
        const changeIndicators = [
            'would you like me to',
            'shall i',
            'i can update',
            'i can modify',
            'i can add',
            'i can change',
            'would you like to apply',
            'ready to apply'
        ];
        
        const responseLower = response.toLowerCase();
        const suggestsChange = changeIndicators.some(indicator => responseLower.includes(indicator));
        
        if (suggestsChange) {
            // Show approval buttons
            showApprovalButtons();
        }
    }
    
    function showApprovalButtons() {
        const $approvalDiv = $('<div class="approval-prompt">')
            .html(`
                <div class="approval-message">
                    <strong>Claude is ready to make changes.</strong>
                    <p>Please specify what you'd like Claude to do, for example:</p>
                    <ul>
                        <li>"Update page ID 5 with title 'New Title' and content 'New content here'"</li>
                        <li>"Add CSS: body { background-color: #f0f0f0; }"</li>
                        <li>"Modify the homepage content"</li>
                    </ul>
                </div>
            `);
        
        $('#chat-messages').append($approvalDiv);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }
    
    // Manual approval for specific changes
    function applyChange(changeType, changeData) {
        if (confirm('Are you sure you want to apply these changes?')) {
            $.ajax({
                url: claudeAgent.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'claude_apply_changes',
                    nonce: claudeAgent.nonce,
                    change_type: changeType,
                    changes: JSON.stringify(changeData),
                    approved: 'true'
                },
                success: function(response) {
                    if (response.success) {
                        addMessageToChat('system', '✅ ' + response.data.message);
                    } else {
                        addMessageToChat('error', '❌ Failed to apply changes: ' + response.data);
                    }
                },
                error: function() {
                    addMessageToChat('error', '❌ Network error while applying changes');
                }
            });
        }
    }
    
    // Expose applyChange globally for manual testing
    window.claudeApplyChange = applyChange;
    
    function clearChat() {
        if (confirm('Clear all messages?')) {
            $('#chat-messages').html(`
                <div class="welcome-message">
                    <h2>👋 Chat cleared!</h2>
                    <p>What would you like help with?</p>
                </div>
            `);
            conversationContext = {};
            pendingChanges = null;
        }
    }
    
    function analyzeSite() {
        showTypingIndicator();
        
        $.ajax({
            url: claudeAgent.ajaxUrl,
            type: 'POST',
            data: {
                action: 'claude_analyze_site',
                nonce: claudeAgent.nonce
            },
            success: function(response) {
                hideTypingIndicator();
                
                if (response.success) {
                    conversationContext.siteStructure = response.data;
                    
                    let summary = `Site Analysis Complete!\n\n`;
                    summary += `📊 Site: ${response.data.site_name}\n`;
                    summary += `🎨 Theme: ${response.data.theme.name} v${response.data.theme.version}\n`;
                    summary += `📄 Pages: ${response.data.pages.length}\n`;
                    summary += `📝 Posts: ${response.data.posts_count}\n`;
                    summary += `💻 WordPress: ${response.data.wordpress_version}\n\n`;
                    
                    if (response.data.site_health) {
                        summary += `Site Health:\n`;
                        summary += `- Total Pages: ${response.data.site_health.total_pages}\n`;
                        summary += `- Total Posts: ${response.data.site_health.total_posts}\n`;
                        summary += `- PHP: ${response.data.site_health.php_version}\n\n`;
                    }
                    
                    // Add page IDs for easy reference
                    if (response.data.pages && response.data.pages.length > 0) {
                        summary += `\nPage IDs for reference:\n`;
                        response.data.pages.slice(0, 10).forEach(page => {
                            summary += `- ID ${page.id}: ${page.title}\n`;
                        });
                    }
                    
                    summary += `\nI now have full context about your site. You can ask me to modify specific pages by ID.`;
                    
                    addMessageToChat('assistant', summary);
                } else {
                    addMessageToChat('error', 'Failed to analyze site');
                }
            },
            error: function() {
                hideTypingIndicator();
                addMessageToChat('error', 'Network error during site analysis');
            }
        });
    }
    
    function listPages() {
        showTypingIndicator();
        
        $.ajax({
            url: claudeAgent.ajaxUrl,
            type: 'POST',
            data: {
                action: 'claude_analyze_site',
                nonce: claudeAgent.nonce
            },
            success: function(response) {
                hideTypingIndicator();
                
                if (response.success && response.data.pages) {
                    const pages = response.data.pages;
                    let summary = `Found ${pages.length} pages:\n\n`;
                    
                    const published = pages.filter(p => p.status === 'publish');
                    const drafts = pages.filter(p => p.status === 'draft');
                    
                    if (published.length > 0) {
                        summary += `Published (${published.length}):\n`;
                        published.forEach(page => {
                            summary += `✓ [ID ${page.id}] ${page.title}\n`;
                        });
                        summary += `\n`;
                    }
                    
                    if (drafts.length > 0) {
                        summary += `Drafts (${drafts.length}):\n`;
                        drafts.forEach(page => {
                            summary += `○ [ID ${page.id}] ${page.title}\n`;
                        });
                    }
                    
                    summary += `\nTo modify a page, use: "Update page ID X with new content"`;
                    addMessageToChat('assistant', summary);
                }
            }
        });
    }
    
    function getTheme() {
        showTypingIndicator();
        
        $.ajax({
            url: claudeAgent.ajaxUrl,
            type: 'POST',
            data: {
                action: 'claude_get_theme_files',
                nonce: claudeAgent.nonce
            },
            success: function(response) {
                hideTypingIndicator();
                
                if (response.success) {
                    conversationContext.themeFiles = response.data;
                    
                    let summary = `Theme Files:\n\n`;
                    
                    for (let file in response.data) {
                        const sizeKB = (response.data[file].size / 1024).toFixed(2);
                        summary += `📄 ${file} (${sizeKB} KB)\n`;
                    }
                    
                    summary += `\nI can help you modify these files safely.`;
                    
                    addMessageToChat('assistant', summary);
                }
            }
        });
    }
    
    function addMessageToChat(type, message) {
        const $messages = $('#chat-messages');
        const timestamp = new Date().toLocaleTimeString();
        
        let icon = type === 'user' ? '👤' : type === 'assistant' ? '🤖' : type === 'system' ? 'ℹ️' : '⚠️';
        
        const $message = $('<div>')
            .addClass('chat-message message-' + type)
            .html(`
                <div class="message-header">
                    <span class="message-icon">${icon}</span>
                    <span class="message-time">${timestamp}</span>
                </div>
                <div class="message-content">${formatMessage(message)}</div>
            `);
        
        $('.welcome-message').remove();
        $('.approval-prompt').remove(); // Remove old approval prompts
        $messages.append($message);
        $messages.scrollTop($messages[0].scrollHeight);
    }
    
    function formatMessage(message) {
        let formatted = escapeHtml(message);
        
        // Headers
        formatted = formatted.replace(/^### (.*?)$/gm, '<h4>$1</h4>');
        formatted = formatted.replace(/^## (.*?)$/gm, '<h3>$1</h3>');
        
        // Bold
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Code blocks
        formatted = formatted.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
        
        // Inline code
        formatted = formatted.replace(/`(.*?)`/g, '<code>$1</code>');
        
        // Line breaks
        formatted = formatted.replace(/\n/g, '<br>');
        
        return formatted;
    }
    
    function showTypingIndicator() {
        const $indicator = $('<div class="typing-indicator">')
            .html('🤖 <span>Claude is thinking</span><span class="dots"><span>.</span><span>.</span><span>.</span></span>');
        
        $('.welcome-message').remove();
        $('#chat-messages').append($indicator);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }
    
    function hideTypingIndicator() {
        $('.typing-indicator').remove();
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
})(jQuery);