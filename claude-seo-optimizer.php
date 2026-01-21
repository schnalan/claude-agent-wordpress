cd /home/claude && cat > seo-performance-agent.php << 'EOF'
<?php
/**
 * Plugin Name: Claude SEO & Performance Agent
 * Plugin URI: https://github.com/yourusername/claude-seo-agent
 * Description: AI-powered SEO analysis and performance optimization using Claude API
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: claude-seo-agent
 */

if (!defined('ABSPATH')) {
    exit;
}

class Claude_SEO_Performance_Agent {
    
    private static $instance = null;
    private $api_key = '';
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->api_key = get_option('claude_seo_api_key', '');
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // AJAX handlers
        add_action('wp_ajax_claude_analyze_page', array($this, 'ajax_analyze_page'));
        add_action('wp_ajax_claude_optimize_images', array($this, 'ajax_optimize_images'));
        add_action('wp_ajax_claude_generate_meta', array($this, 'ajax_generate_meta'));
        add_action('wp_ajax_claude_save_settings', array($this, 'ajax_save_settings'));
        
        // Add meta boxes to post editor
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box_data'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Claude SEO Agent',
            'SEO Agent',
            'manage_options',
            'claude-seo-agent',
            array($this, 'render_dashboard'),
            'dashicons-search',
            30
        );
        
        add_submenu_page(
            'claude-seo-agent',
            'Performance',
            'Performance',
            'manage_options',
            'claude-seo-performance',
            array($this, 'render_performance_page')
        );
        
        add_submenu_page(
            'claude-seo-agent',
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
        
        wp_enqueue_style('claude-seo-admin', plugins_url('assets/admin.css', __FILE__), array(), '1.0.0');
        wp_enqueue_script('claude-seo-admin', plugins_url('assets/admin.js', __FILE__), array('jquery'), '1.0.0', true);
        
        wp_localize_script('claude-seo-admin', 'claudeSEO', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('claude_seo_nonce')
        ));
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'claude_seo_assistant',
            'Claude SEO Assistant',
            array($this, 'render_meta_box'),
            array('post', 'page'),
            'side',
            'high'
        );
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('claude_seo_meta_box', 'claude_seo_meta_box_nonce');
        
        $seo_score = get_post_meta($post->ID, '_claude_seo_score', true);
        $last_analysis = get_post_meta($post->ID, '_claude_last_analysis', true);
        $meta_description = get_post_meta($post->ID, '_claude_meta_description', true);
        
        include plugin_dir_path(__FILE__) . 'templates/meta-box.php';
    }
    
    public function save_meta_box_data($post_id) {
        if (!isset($_POST['claude_seo_meta_box_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['claude_seo_meta_box_nonce'], 'claude_seo_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['claude_meta_description'])) {
            update_post_meta($post_id, '_claude_meta_description', sanitize_text_field($_POST['claude_meta_description']));
        }
    }
    
    public function render_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        include plugin_dir_path(__FILE__) . 'templates/dashboard.php';
    }
    
    public function render_performance_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        include plugin_dir_path(__FILE__) . 'templates/performance.php';
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        include plugin_dir_path(__FILE__) . 'templates/settings.php';
    }
    
    // AJAX: Analyze page SEO
    public function ajax_analyze_page() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        
        if (!$post) {
            wp_send_json_error('Post not found');
            return;
        }
        
        $analysis = $this->analyze_seo($post);
        
        // Save analysis results
        update_post_meta($post_id, '_claude_seo_score', $analysis['score']);
        update_post_meta($post_id, '_claude_last_analysis', current_time('mysql'));
        update_post_meta($post_id, '_claude_seo_issues', json_encode($analysis['issues']));
        
        wp_send_json_success($analysis);
    }
    
    // AJAX: Generate meta description
    public function ajax_generate_meta() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        
        if (!$post) {
            wp_send_json_error('Post not found');
            return;
        }
        
        $meta_description = $this->generate_meta_description($post);
        
        if ($meta_description) {
            update_post_meta($post_id, '_claude_meta_description', $meta_description);
            wp_send_json_success(array('meta_description' => $meta_description));
        } else {
            wp_send_json_error('Failed to generate meta description');
        }
    }
    
    // AJAX: Optimize images
    public function ajax_optimize_images() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $results = $this->analyze_image_optimization();
        wp_send_json_success($results);
    }
    
    // AJAX: Save settings
    public function ajax_save_settings() {
        check_ajax_referer('claude_seo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $api_key = sanitize_text_field($_POST['api_key']);
        update_option('claude_seo_api_key', $api_key);
        
        $this->api_key = $api_key;
        
        wp_send_json_success('Settings saved');
    }
    
    // Core SEO analysis function
    private function analyze_seo($post) {
        $issues = array();
        $score = 100;
        
        // Title analysis
        $title = $post->post_title;
        if (strlen($title) < 30) {
            $issues[] = array('type' => 'warning', 'message' => 'Title is too short (less than 30 characters)');
            $score -= 10;
        }
        if (strlen($title) > 60) {
            $issues[] = array('type' => 'warning', 'message' => 'Title is too long (more than 60 characters)');
            $score -= 10;
        }
        
        // Meta description
        $meta_desc = get_post_meta($post->ID, '_claude_meta_description', true);
        if (empty($meta_desc)) {
            $issues[] = array('type' => 'error', 'message' => 'No meta description found');
            $score -= 15;
        } elseif (strlen($meta_desc) < 120) {
            $issues[] = array('type' => 'warning', 'message' => 'Meta description is too short');
            $score -= 5;
        } elseif (strlen($meta_desc) > 160) {
            $issues[] = array('type' => 'warning', 'message' => 'Meta description is too long');
            $score -= 5;
        }
        
        // Content analysis
        $content = $post->post_content;
        $word_count = str_word_count(strip_tags($content));
        
        if ($word_count < 300) {
            $issues[] = array('type' => 'warning', 'message' => 'Content is thin (less than 300 words)');
            $score -= 15;
        }
        
        // Heading structure
        if (!preg_match('/<h1/', $content) && !preg_match('/<h2/', $content)) {
            $issues[] = array('type' => 'warning', 'message' => 'No headings (H1/H2) found in content');
            $score -= 10;
        }
        
        // Images without alt text
        preg_match_all('/<img[^>]+>/', $content, $images);
        $images_without_alt = 0;
        foreach ($images[0] as $img) {
            if (!preg_match('/alt=["\']([^"\']+)["\']/', $img)) {
                $images_without_alt++;
            }
        }
        if ($images_without_alt > 0) {
            $issues[] = array('type' => 'warning', 'message' => "$images_without_alt images missing alt text");
            $score -= ($images_without_alt * 5);
        }
        
        // Internal links
        preg_match_all('/<a[^>]+href=["\']' . preg_quote(site_url(), '/') . '[^"\']*["\'][^>]*>/', $content, $internal_links);
        if (count($internal_links[0]) === 0) {
            $issues[] = array('type' => 'info', 'message' => 'No internal links found');
            $score -= 5;
        }
        
        $score = max(0, min(100, $score));
        
        return array(
            'score' => $score,
            'issues' => $issues,
            'word_count' => $word_count,
            'readability' => $this->calculate_readability($content)
        );
    }
    
    private function calculate_readability($content) {
        $text = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = str_word_count($text);
        $syllables = $this->count_syllables($text);
        
        if (count($sentences) === 0 || $words === 0) {
            return 0;
        }
        
        // Flesch Reading Ease
        $avg_words_per_sentence = $words / count($sentences);
        $avg_syllables_per_word = $syllables / $words;
        
        $score = 206.835 - (1.015 * $avg_words_per_sentence) - (84.6 * $avg_syllables_per_word);
        
        return round(max(0, min(100, $score)), 1);
    }
    
    private function count_syllables($text) {
        $words = str_word_count(strtolower($text), 1);
        $syllables = 0;
        
        foreach ($words as $word) {
            $syllables += max(1, preg_match_all('/[aeiouy]+/', $word));
        }
        
        return $syllables;
    }
    
    // Generate meta description using Claude API
    private function generate_meta_description($post) {
        if (empty($this->api_key)) {
            return false;
        }
        
        $content = wp_strip_all_tags($post->post_content);
        $content = substr($content, 0, 2000); // Limit content length
        
        $prompt = "Based on the following blog post content, write a compelling meta description between 120-160 characters that will encourage clicks from search results. Be concise and include the main topic.\n\nTitle: {$post->post_title}\n\nContent excerpt:\n{$content}\n\nMeta description:";
        
        $response = $this->call_claude_api($prompt, 100);
        
        if ($response && isset($response['content'][0]['text'])) {
            $meta = trim($response['content'][0]['text']);
            // Ensure it's within limits
            if (strlen($meta) > 160) {
                $meta = substr($meta, 0, 157) . '...';
            }
            return $meta;
        }
        
        return false;
    }
    
    // Analyze image optimization opportunities
    private function analyze_image_optimization() {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'post_status' => 'any'
        );
        
        $images = get_posts($args);
        $results = array(
            'total_images' => count($images),
            'large_images' => array(),
            'missing_alt' => array(),
            'unoptimized_formats' => array()
        );
        
        foreach ($images as $image) {
            $file_path = get_attached_file($image->ID);
            
            if (file_exists($file_path)) {
                $file_size = filesize($file_path);
                
                // Check for large images (over 200KB)
                if ($file_size > 204800) {
                    $results['large_images'][] = array(
                        'id' => $image->ID,
                        'title' => $image->post_title,
                        'size' => size_format($file_size),
                        'url' => wp_get_attachment_url($image->ID)
                    );
                }
                
                // Check for missing alt text
                $alt_text = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
                if (empty($alt_text)) {
                    $results['missing_alt'][] = array(
                        'id' => $image->ID,
                        'title' => $image->post_title,
                        'url' => wp_get_attachment_url($image->ID)
                    );
                }
                
                // Check for non-WebP formats
                $file_type = wp_check_filetype($file_path);
                if (in_array($file_type['ext'], array('jpg', 'jpeg', 'png')) && $file_size > 51200) {
                    $results['unoptimized_formats'][] = array(
                        'id' => $image->ID,
                        'title' => $image->post_title,
                        'format' => $file_type['ext'],
                        'size' => size_format($file_size),
                        'url' => wp_get_attachment_url($image->ID)
                    );
                }
            }
        }
        
        return $results;
    }
    
    // Call Claude API
    private function call_claude_api($prompt, $max_tokens = 1024) {
        if (empty($this->api_key)) {
            return false;
        }
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $this->api_key,
                'anthropic-version' => '2023-06-01'
            ),
            'body' => json_encode(array(
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => $max_tokens,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                )
            ))
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body;
    }
}
