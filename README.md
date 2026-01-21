# Claude AI Agent for WordPress

![Version](https://img.shields.io/badge/version-2.0.1-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.8+-green.svg)
![PHP](https://img.shields.io/badge/php-7.4+-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0-red.svg)

An advanced autonomous AI agent that integrates Anthropic's Claude AI directly into WordPress for intelligent site management, content creation, and automated maintenance.

## ✨ Features

### 🤖 AI-Powered Chat Interface
- Direct conversation with Claude about your WordPress site
- Context-aware responses using site structure and content
- Natural language commands for site management
- Real-time site analysis and recommendations

### ⚡ Autonomous Workflows
- **SEO Optimization** - Complete site SEO audit and fixes
- **Content Refresh** - Identify and update outdated content
- **Performance Boost** - Speed optimization and asset management
- Custom workflow creation via natural language

### 🧠 Machine Learning System
- Learns from your approval patterns
- Adapts to your preferences over time
- Personalized suggestions based on past interactions
- Detailed analytics and insights dashboard

### 🔧 Auto-Fix Capabilities
- Automatic detection of common issues
- Configurable rules for automated fixes
- Missing meta description generation
- Broken link detection and repair
- 404 error monitoring

### 📊 Scheduled Site Audits
- Daily automated health checks
- Issue detection and categorization
- Email notifications for critical problems
- Comprehensive audit history

### 🎯 Three Autonomy Levels

**Supervised Mode** (Recommended for beginners)
- All changes require manual approval
- Maximum safety and control
- Learn how Claude works

**Semi-Autonomous Mode** (Balanced)
- Minor fixes applied automatically
- Major changes require approval
- Best for experienced users

**Autonomous Mode** (Advanced)
- AI makes decisions independently
- Based on learned preferences
- Regular monitoring recommended

## 🚀 Installation

### Automatic Installation (WordPress Admin)

1. Download the [latest release](https://github.com/YOUR-USERNAME/claude-wordpress-agent/releases)
2. In WordPress admin, go to **Plugins → Add New → Upload Plugin**
3. Choose the downloaded ZIP file
4. Click **Install Now**
5. Click **Activate Plugin**

### Manual Installation (FTP)

1. Download and extract the plugin ZIP
2. Upload the `claude-agent` folder to `/wp-content/plugins/`
3. Activate through the **Plugins** menu in WordPress

### First-Time Setup

1. Go to **Claude Agent → Settings**
2. Get your API key from [console.anthropic.com](https://console.anthropic.com/)
   - Sign up or log in
   - Navigate to API Keys
   - Create a new key
3. Paste the API key in the settings
4. Select your preferred model (Sonnet 4 recommended)
5. Choose autonomy level (start with "Supervised")
6. Save settings
7. Start chatting with Claude!

## 💡 Usage Examples

### Analyze Your Site
```
Click "Analyze Site" button or ask:
"Analyze my site and show me what you see"
```

### Update Content
```
"Update page ID 5 with a new introduction paragraph about our company values"
```

### Fix Issues
```
"Check my site for broken links and SEO issues"
```

### Create Workflows
```
"Create a workflow that updates all blog post meta descriptions"
```

## 📋 Requirements

- **WordPress:** 5.8 or higher
- **PHP:** 7.4 or higher
- **MySQL:** 5.6 or higher
- **Server:** cURL enabled, allow_url_fopen enabled
- **API Key:** Anthropic API key (from console.anthropic.com)
- **Memory:** 64MB PHP memory minimum (128MB+ recommended)

## 🔐 Security Features

- ✅ API key encryption in database
- ✅ WordPress nonce verification on all AJAX requests
- ✅ Capability checks (requires `manage_options`)
- ✅ Input sanitization and validation
- ✅ File type restrictions (only .css, .php, .js)
- ✅ Automatic backups before changes (last 50 stored)
- ✅ Approval workflows in supervised mode
- ✅ Autonomous action logging
- ✅ Direct file access prevention

## 💰 API Costs

This plugin uses the Anthropic API, which has usage-based pricing:

**Claude Sonnet 4** (Recommended)
- Input: ~$3 per million tokens
- Output: ~$15 per million tokens

**Typical Usage:**
- Simple chat: $0.01 per interaction
- Site analysis: $0.05 per analysis
- Complex changes: $0.10-0.30

**Monthly Estimates:**
- Light use (Supervised mode): $5-15
- Medium use (Semi-Autonomous): $20-50
- Heavy use (Autonomous): $50-150

Monitor usage at [console.anthropic.com](https://console.anthropic.com/)

## 🛠️ Development

### Local Development Setup
```bash
# Clone the repository
git clone https://github.com/YOUR-USERNAME/claude-wordpress-agent.git

# Navigate to WordPress plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Create symlink (or copy files)
ln -s /path/to/claude-wordpress-agent claude-agent

# Activate in WordPress
# Go to Plugins → Claude AI Agent → Activate
```

### File Structure
```
claude-agent/
├── claude-agent.php          # Main plugin file with auto-updater
├── assets/
│   ├── admin.css            # Admin interface styles
│   └── admin.js             # JavaScript functionality
├── templates/
│   ├── admin-page.php       # Main chat interface
│   ├── workflows-page.php   # Workflow management
│   ├── learning-page.php    # AI learning insights
│   ├── autonomous-page.php  # Autonomy configuration
│   └── settings-page.php    # Plugin settings
└── README.md
```

## 📝 Changelog

### Version 2.0.1 (2025-01-20)
- **Fixed:** Change application now works properly
- **Fixed:** Page content updates actually save
- **Fixed:** Theme file modifications work correctly
- **Fixed:** Custom CSS updates apply successfully
- **Added:** Support for multiple change data formats
- **Improved:** Error messages are more descriptive
- **Improved:** Page IDs shown in site analysis for easy reference

### Version 2.0.0 (2025-01-15)
- Initial public release
- AI-powered chat interface
- Autonomous workflow system
- Machine learning capabilities
- Scheduled site audits
- Three autonomy levels
- Auto-fix system
- Backup management

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 🐛 Bug Reports

If you find a bug, please [open an issue](https://github.com/YOUR-USERNAME/claude-wordpress-agent/issues) with:
- Description of the bug
- Steps to reproduce
- Expected behavior
- Actual behavior
- WordPress version
- PHP version
- Plugin version

## 📞 Support

- **Documentation:** This README
- **Issues:** [GitHub Issues](https://github.com/YOUR-USERNAME/claude-wordpress-agent/issues)
- **Discussions:** [GitHub Discussions](https://github.com/YOUR-USERNAME/claude-wordpress-agent/discussions)

## ⚖️ License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Powered by [Anthropic's Claude AI](https://www.anthropic.com/)
- Built for the WordPress community
- Inspired by the need for AI-powered site management

## ⚠️ Disclaimer

This plugin makes direct changes to your WordPress site based on AI decisions. While it includes safety features like backups and approval workflows, always:
- Start with Supervised mode
- Test on a staging site first
- Maintain separate full-site backups
- Review autonomous action logs regularly
- Monitor API usage and costs

---

**Made with ❤️ for WordPress and AI enthusiasts**

**Star ⭐ this repo if you find it helpful!**
