<?php
/**
 * Plugin Name: Claude SEO & Performance Optimizer
 * Plugin URI: https://github.com/YOUR-USERNAME/claude-seo-optimizer
 * Description: AI-powered SEO optimization and performance improvements using Claude API
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL v2 or later
 * Text Domain: claude-seo
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CLAUDE_SEO_VERSION', '1.0.0');
define('CLAUDE_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLAUDE_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));

class Claude_SEO_Optimizer {
    
    private static $instance = null;
    private $api_key;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->api_key = get_option('claude_seo_api_key', '');
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // AJAX handlers
        add_action('wp_ajax_claude_seo_analyze', array($this, 'ajax_analyze_page'));
        add_action('wp_ajax_claude_seo_generate_meta', array($this, 'ajax_generate_meta'));
        add_action('wp_ajax_claude_seo_analyze_performance', array($this, 'ajax_analyze_performance'));
        add_action('wp_ajax_claude_seo_save_settings', array($this, 'ajax_save_settings'));
        
        // Add meta box to posts and pages
        add_action('add_meta_boxes', array($this, 'add_seo_meta_box'));
        add_action('save_post', array($this, 'save_meta_box_data'));
        
        // Performance optimizations (if enabled)
        if (get_option('claude_seo_auto_optimize', false)) {
            $this->enable_performance_optimizations();
        }
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Claude SEO Optimizer',
            'Claude SEO',
            'manage_options',
            'claude-seo',
            array($this, 'render_main_page'),
            'dashicons-search',
            30
        );
        
        add_submenu_page(
            'claude-seo',
            'Performance',
            'Performance',
            'manage_options',
            'claude-seo-performance',
            array($this, 'render_performance_page')
        );
        
        add_submenu_page(
            'claude-seo',
            'Settings',
            'Settings',
            'manage_options',
            'claude-seo-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'claude-seo') === false && $hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        
        wp_enqueue_style('claude-seo-admin', CLAUDE_SEO_PLUGIN_URL . 'assets/admin.css', array(), CLAUDE_SEO_VERSION);
        wp_enqueue_script('claude-seo-admin', CLAUDE_SEO_PLUGIN_URL . 'assets/admin.js', array('jquery'), CLAUDE_SEO_VERSION, true);
        
        wp_localize_script('claude-seo-admin', 'claudeSEO', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('claude_seo_nonce'),
            'hasApiKey' => !empty($this->api_key)
        ));
    }
    
    public function add_seo_meta_box() {
        add_meta_box(
            'claude_seo_meta_box',
            'Claude SEO Assistant',
            array($this, 'render_meta_box'),
            array('post', 'page'),
            'normal',
            'high'
        );
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('claude_seo_meta_box', 'claude_seo_meta_box_nonce');
        
        $meta_description = get_post_meta($post->ID, '_claude_seo_meta_description', true);
        $focus_keyword = get_post_meta($post->ID, '_claude_seo_focus_keyword', true);
        $seo_score = get_post_meta($post->ID, '_claude_seo_score', true);
        ?>
        <div class="claude-seo-meta-box">
            <div class="claude-seo-score">
                <strong>SEO Score:</strong> 
                <span id="seo-score-display"><?php echo $seo_score ? $seo_score . '/100' : 'Not analyzed'; ?></span>
                <button type="button" class="button" id="analyze-seo-btn">Analyze with Claude</button>
            </div>
            
            <div class="claude-seo-field">
                <label for="claude_focus_keyword"><strong>Focus Keyword:</strong></label>
                <input type="text" id="claude_focus_keyword" name="claude_focus_keyword" 
                       value="<?php echo esc_attr($focus_keyword); ?>" class="widefat">
            </div>
            
            <div class="claude-seo-field">
                <label for="claude_meta_description"><strong>Meta Description:</strong></label>
                <textarea id="claude_meta_description" name="claude_meta_description" 
                          rows="3" class="widefat"><?php echo esc_textarea($meta_description); ?></textarea>
                <button type="button" class="button" id="generate-meta-btn">Generate with Claude</button>
                <span class="description">Characters: <span id="meta-char-count">0</span>/160</span>
            </div>
            
            <div id="seo-suggestions" class="claude-seo-suggestions" style="display:none;">
                <h4>SEO Suggestions:</h4>
                <div id="suggestions-content"></div>
            </div>
        </div>
        <?php
    }
    
    public function save_meta_box_data($post_id) {
        if (!isset($_POST['claude_seo_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['claude_seo_meta_box_nonce'], 'claude_seo_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['claude_meta_description'])) {
            update_post_meta($post_id, '_claude_seo_meta_description', sanitize_textarea_field($_POST['claude_meta_description']));
        }
        
        if (isset($_POST['claude_focus_keyword'])) {
            update_post_meta($post_id, '_claude_seo_focus_keyword', sanitize_text_field($_POST['claude_focus_keyword']));
        }
    }
    
    public function ajax_analyze_page() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        if (empty($this->api_key)) {
            wp_send_json_error('Please configure your Claude API key in settings');
        }
        
        // Analyze the post content with Claude
        $analysis = $this->analyze_with_claude($post);
        
        if ($analysis['success']) {
            update_post_meta($post_id, '_claude_seo_score', $analysis['score']);
            wp_send_json_success($analysis);
        } else {
            wp_send_json_error($analysis['message']);
        }
    }
    
    public function ajax_generate_meta() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        if (empty($this->api_key)) {
            wp_send_json_error('Please configure your Claude API key in settings');
        }
        
        $meta_description = $this->generate_meta_description($post);
        
        if ($meta_description) {
            wp_send_json_success(array('meta_description' => $meta_description));
        } else {
            wp_send_json_error('Failed to generate meta description');
        }
    }
    
    public function ajax_analyze_performance() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $analysis = $this->analyze_site_performance();
        wp_send_json_success($analysis);
    }
    
    public function ajax_save_settings() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $api_key = sanitize_text_field($_POST['api_key']);
        $auto_optimize = isset($_POST['auto_optimize']) ? true : false;
        
        update_option('claude_seo_api_key', $api_key);
        update_option('claude_seo_auto_optimize', $auto_optimize);
        
        wp_send_json_success('Settings saved successfully');
    }
    
    private function analyze_with_claude($post) {
        $content = $post->post_content;
        $title = $post->post_title;
        $focus_keyword = get_post_meta($post->ID, '_claude_seo_focus_keyword', true);
        
        $prompt = "Analyze this WordPress post for SEO optimization. Post title: '$title'. ";
        if ($focus_keyword) {
            $prompt .= "Focus keyword: '$focus_keyword'. ";
        }
        $prompt .= "Content: " . wp_strip_all_tags($content) . "\n\n";
        $prompt .= "Provide:\n1. An SEO score (0-100)\n2. Specific actionable suggestions for improvement\n3. Whether the focus keyword is used effectively\n4. Suggestions for internal linking opportunities\n\nFormat your response as JSON with keys: score, suggestions (array), keyword_usage, internal_linking";
        
        $response = $this->call_claude_api($prompt);
        
        if (!$response) {
            return array('success' => false, 'message' => 'Failed to connect to Claude API');
        }
        
        // Parse Claude's response
        try {
            $data = json_decode($response, true);
            return array(
                'success' => true,
                'score' => $data['score'],
                'suggestions' => $data['suggestions'],
                'keyword_usage' => $data['keyword_usage'],
                'internal_linking' => $data['internal_linking']
            );
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Failed to parse AI response');
        }
    }
    
    private function generate_meta_description($post) {
        $content = wp_strip_all_tags($post->post_content);
        $title = $post->post_title;
        $focus_keyword = get_post_meta($post->ID, '_claude_seo_focus_keyword', true);
        
        $prompt = "Write a compelling SEO meta description (150-160 characters) for this post.\n";
        $prompt .= "Title: $title\n";
        if ($focus_keyword) {
            $prompt .= "Focus keyword: $focus_keyword\n";
        }
        $prompt .= "Content summary: " . substr($content, 0, 500) . "\n\n";
        $prompt .= "Return ONLY the meta description text, nothing else.";
        
        return $this->call_claude_api($prompt);
    }
    
    private function analyze_site_performance() {
        global $wpdb;
        
        $analysis = array(
            'total_posts' => wp_count_posts()->publish,
            'total_pages' => wp_count_posts('page')->publish,
            'database_size' => $this->get_database_size(),
            'plugins_count' => count(get_option('active_plugins')),
            'theme' => wp_get_theme()->get('Name'),
            'recommendations' => array()
        );
        
        // Check for common performance issues
        if ($analysis['plugins_count'] > 20) {
            $analysis['recommendations'][] = "You have {$analysis['plugins_count']} active plugins. Consider deactivating unused plugins to improve performance.";
        }
        
        // Check image optimization
        $unoptimized_images = $this->count_unoptimized_images();
        if ($unoptimized_images > 0) {
            $analysis['recommendations'][] = "$unoptimized_images images could benefit from optimization. Consider using an image optimization plugin.";
        }
        
        // Check for old revisions
        $revision_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'");
        if ($revision_count > 100) {
            $analysis['recommendations'][] = "You have $revision_count post revisions. Cleaning these up can reduce database size.";
        }
        
        // Check caching
        if (!defined('WP_CACHE') || !WP_CACHE) {
            $analysis['recommendations'][] = "Object caching is not enabled. Consider enabling it for better performance.";
        }
        
        return $analysis;
    }
    
    private function get_database_size() {
        global $wpdb;
        $size = $wpdb->get_var("SELECT SUM(data_length + index_length) FROM information_schema.TABLES WHERE table_schema = '{$wpdb->dbname}'");
        return $this->format_bytes($size);
    }
    
    private function count_unoptimized_images() {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_wp_attachment_metadata',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $images = get_posts($args);
        $unoptimized = 0;
        
        foreach ($images as $image) {
            $file_path = get_attached_file($image->ID);
            if (file_exists($file_path)) {
                $file_size = filesize($file_path);
                // Simple heuristic: if image is > 500KB, it could be optimized
                if ($file_size > 500000) {
                    $unoptimized++;
                }
            }
        }
        
        return $unoptimized;
    }
    
    private function format_bytes($bytes) {
        $units = array('B', 'KB', 'MB', 'GB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    private function call_claude_api($prompt) {
        $api_url = 'https://api.anthropic.com/v1/messages';
        
        $body = array(
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 1024,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            )
        );
        
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $this->api_key,
                'anthropic-version' => '2023-06-01'
            ),
            'body' => json_encode($body),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['content'][0]['text'])) {
            return $body['content'][0]['text'];
        }
        
        return false;
    }
    
    private function enable_performance_optimizations() {
        // Enable Gzip compression
        add_action('init', function() {
            if (!headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
                if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
                    ob_start('ob_gzhandler');
                }
            }
        });
        
        // Remove query strings from static resources
        add_filter('script_loader_src', array($this, 'remove_query_strings'), 15);
        add_filter('style_loader_src', array($this, 'remove_query_strings'), 15);
        
        // Disable emojis
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        
        // Defer JavaScript loading
        add_filter('script_loader_tag', array($this, 'defer_javascript'), 10, 2);
    }
    
    public function remove_query_strings($src) {
        if (strpos($src, '?ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    
    public function defer_javascript($tag, $handle) {
        $defer_scripts = array('jquery', 'jquery-core', 'jquery-migrate');
        
        if (in_array($handle, $defer_scripts)) {
            return $tag;
        }
        
        return str_replace(' src', ' defer src', $tag);
    }
    
    public function render_main_page() {
        include CLAUDE_SEO_PLUGIN_DIR . 'templates/main-page.php';
    }
    
    public function render_performance_page() {
        include CLAUDE_SEO_PLUGIN_DIR . 'templates/performance-page.php';
    }
    
    public function render_settings_page() {
        include CLAUDE_SEO_PLUGIN_DIR . 'templates/settings-page.php';
    }
}

// Initialize the plugin
Claude_SEO_Optimizer::get_instance();
