jQuery(document).ready(function($) {
    
    // Meta description character counter
    $('#claude_meta_description').on('input', function() {
        var count = $(this).val().length;
        $('#meta-char-count').text(count);
        
        if (count > 160) {
            $('#meta-char-count').css('color', '#dc3232');
        } else if (count >= 150) {
            $('#meta-char-count').css('color', '#46b450');
        } else {
            $('#meta-char-count').css('color', '#666');
        }
    }).trigger('input');
    
    // Analyze SEO button
    $('#analyze-seo-btn').on('click', function() {
        var button = $(this);
        var postId = $('#post_ID').val();
        
        if (!claudeSEO.hasApiKey) {
            alert('Please configure your Claude API key in settings first.');
            return;
        }
        
        button.addClass('loading').prop('disabled', true);
        
        $.ajax({
            url: claudeSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_seo_analyze',
                nonce: claudeSEO.nonce,
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    // Update score
                    $('#seo-score-display').text(data.score + '/100');
                    
                    // Show suggestions
                    var suggestionsHtml = '<ul>';
                    if (data.suggestions && data.suggestions.length > 0) {
                        data.suggestions.forEach(function(suggestion) {
                            suggestionsHtml += '<li>' + suggestion + '</li>';
                        });
                    }
                    suggestionsHtml += '</ul>';
                    
                    if (data.keyword_usage) {
                        suggestionsHtml += '<p><strong>Keyword Usage:</strong> ' + data.keyword_usage + '</p>';
                    }
                    
                    if (data.internal_linking) {
                        suggestionsHtml += '<p><strong>Internal Linking:</strong> ' + data.internal_linking + '</p>';
                    }
                    
                    $('#suggestions-content').html(suggestionsHtml);
                    $('#seo-suggestions').slideDown();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while analyzing the post.');
            },
            complete: function() {
                button.removeClass('loading').prop('disabled', false);
            }
        });
    });
    
    // Generate meta description button
    $('#generate-meta-btn').on('click', function() {
        var button = $(this);
        var postId = $('#post_ID').val();
        
        if (!claudeSEO.hasApiKey) {
            alert('Please configure your Claude API key in settings first.');
            return;
        }
        
        button.addClass('loading').prop('disabled', true);
        
        $.ajax({
            url: claudeSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_seo_generate_meta',
                nonce: claudeSEO.nonce,
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    $('#claude_meta_description').val(response.data.meta_description).trigger('input');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while generating the meta description.');
            },
            complete: function() {
                button.removeClass('loading').prop('disabled', false);
            }
        });
    });
    
    // Performance analysis button
    $('#run-performance-analysis').on('click', function() {
        var button = $(this);
        
        button.addClass('loading').prop('disabled', true);
        $('#performance-results').slideDown();
        $('#performance-content').html('<div class="loading"><span class="spinner is-active"></span>Analyzing your site...</div>');
        
        $.ajax({
            url: claudeSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_seo_analyze_performance',
                nonce: claudeSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var html = '';
                    
                    // Display metrics
                    html += '<div class="performance-metric">';
                    html += '<h4>Site Metrics</h4>';
                    html += '<p><strong>Total Posts:</strong> ' + data.total_posts + '</p>';
                    html += '<p><strong>Total Pages:</strong> ' + data.total_pages + '</p>';
                    html += '<p><strong>Database Size:</strong> ' + data.database_size + '</p>';
                    html += '<p><strong>Active Plugins:</strong> ' + data.plugins_count + '</p>';
                    html += '<p><strong>Theme:</strong> ' + data.theme + '</p>';
                    html += '</div>';
                    
                    // Display recommendations
                    if (data.recommendations && data.recommendations.length > 0) {
                        html += '<h3 style="margin-top: 20px;">Recommendations</h3>';
                        data.recommendations.forEach(function(rec) {
                            html += '<div class="performance-recommendation">';
                            html += '<p><strong>⚠️</strong> ' + rec + '</p>';
                            html += '</div>';
                        });
                    } else {
                        html += '<div class="performance-metric" style="margin-top: 20px;">';
                        html += '<p><strong>✓ Great job!</strong> No critical performance issues detected.</p>';
                        html += '</div>';
                    }
                    
                    $('#performance-content').html(html);
                } else {
                    $('#performance-content').html('<p>Error analyzing performance.</p>');
                }
            },
            error: function() {
                $('#performance-content').html('<p>An error occurred while analyzing performance.</p>');
            },
            complete: function() {
                button.removeClass('loading').prop('disabled', false);
            }
        });
    });
    
    // API settings form
    $('#api-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var apiKey = $('#claude_api_key').val().trim();
        
        if (!apiKey) {
            alert('Please enter an API key.');
            return;
        }
        
        submitBtn.addClass('loading').prop('disabled', true);
        
        $.ajax({
            url: claudeSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_seo_save_settings',
                nonce: claudeSEO.nonce,
                api_key: apiKey,
                auto_optimize: $('#auto_optimize').is(':checked')
            },
            success: function(response) {
                if (response.success) {
                    alert('Settings saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while saving settings.');
            },
            complete: function() {
                submitBtn.removeClass('loading').prop('disabled', false);
            }
        });
    });
    
    // Test API key button
    $('#test-api-key').on('click', function() {
        var button = $(this);
        var apiKey = $('#claude_api_key').val().trim();
        
        if (!apiKey) {
            alert('Please enter an API key first.');
            return;
        }
        
        button.addClass('loading').prop('disabled', true);
        $('#test-result').text('');
        
        // Simple test: try to make a minimal API call
        $.ajax({
            url: 'https://api.anthropic.com/v1/messages',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'x-api-key': apiKey,
                'anthropic-version': '2023-06-01'
            },
            data: JSON.stringify({
                model: 'claude-sonnet-4-20250514',
                max_tokens: 10,
                messages: [{
                    role: 'user',
                    content: 'test'
                }]
            }),
            success: function() {
                $('#test-result').text('✓ API key is valid!').addClass('success').removeClass('error');
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    $('#test-result').text('✗ Invalid API key').addClass('error').removeClass('success');
                } else {
                    $('#test-result').text('✓ API key appears valid').addClass('success').removeClass('error');
                }
            },
            complete: function() {
                button.removeClass('loading').prop('disabled', false);
            }
        });
    });
    
    // Performance settings form
    $('#performance-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        
        submitBtn.addClass('loading').prop('disabled', true);
        
        $.ajax({
            url: claudeSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_seo_save_settings',
                nonce: claudeSEO.nonce,
                api_key: '', // Don't change API key
                auto_optimize: $('#auto_optimize').is(':checked')
            },
            success: function(response) {
                if (response.success) {
                    alert('Performance settings saved! Please refresh your site for changes to take effect.');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while saving settings.');
            },
            complete: function() {
                submitBtn.removeClass('loading').prop('disabled', false);
            }
        });
    });
});
