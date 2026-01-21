<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap claude-seo-wrap">
    <h1>Performance Analysis</h1>
    
    <div class="claude-seo-card">
        <h2>Analyze Your Site</h2>
        <p>Get AI-powered recommendations to improve your site's performance and speed.</p>
        <button id="run-performance-analysis" class="button button-primary button-large">
            <span class="dashicons dashicons-performance"></span>
            Run Performance Analysis
        </button>
    </div>
    
    <div id="performance-results" class="claude-seo-card" style="display:none;">
        <h2>Analysis Results</h2>
        <div id="performance-content">
            <div class="loading">
                <span class="spinner is-active"></span>
                Analyzing your site...
            </div>
        </div>
    </div>
    
    <div class="claude-seo-card">
        <h2>Performance Optimizations</h2>
        <p>Enable automatic performance optimizations:</p>
        
        <form id="performance-settings-form">
            <table class="form-table">
                <tr>
                    <th scope="row">Auto-Optimize</th>
                    <td>
                        <label>
                            <input type="checkbox" name="auto_optimize" id="auto_optimize" 
                                   <?php checked(get_option('claude_seo_auto_optimize'), true); ?>>
                            Enable automatic performance optimizations
                        </label>
                        <p class="description">
                            This will enable:
                            <ul style="margin-top: 5px;">
                                <li>• Gzip compression</li>
                                <li>• Remove query strings from static resources</li>
                                <li>• Disable WordPress emojis</li>
                                <li>• Defer JavaScript loading</li>
                            </ul>
                        </p>
                    </td>
                </tr>
            </table>
            <button type="submit" class="button button-primary">Save Performance Settings</button>
        </form>
    </div>
    
    <div class="claude-seo-card">
        <h2>Manual Optimization Tasks</h2>
        <div class="optimization-tasks">
            <div class="task-item">
                <h3>Image Optimization</h3>
                <p>Large images slow down your site. Consider using an image optimization plugin like:</p>
                <ul>
                    <li>• <a href="https://wordpress.org/plugins/ewww-image-optimizer/" target="_blank">EWWW Image Optimizer</a></li>
                    <li>• <a href="https://wordpress.org/plugins/shortpixel-image-optimiser/" target="_blank">ShortPixel</a></li>
                    <li>• <a href="https://wordpress.org/plugins/imagify/" target="_blank">Imagify</a></li>
                </ul>
            </div>
            
            <div class="task-item">
                <h3>Caching</h3>
                <p>Caching dramatically improves load times. Recommended caching plugins:</p>
                <ul>
                    <li>• <a href="https://wordpress.org/plugins/wp-super-cache/" target="_blank">WP Super Cache</a></li>
                    <li>• <a href="https://wordpress.org/plugins/w3-total-cache/" target="_blank">W3 Total Cache</a></li>
                    <li>• <a href="https://wordpress.org/plugins/wp-fastest-cache/" target="_blank">WP Fastest Cache</a></li>
                </ul>
            </div>
            
            <div class="task-item">
                <h3>Database Cleanup</h3>
                <p>Clean up your database to improve performance:</p>
                <ul>
                    <li>• Delete post revisions</li>
                    <li>• Remove spam comments</li>
                    <li>• Clean transients</li>
                </ul>
                <p>Use plugins like <a href="https://wordpress.org/plugins/wp-optimize/" target="_blank">WP-Optimize</a></p>
            </div>
        </div>
    </div>
</div>
