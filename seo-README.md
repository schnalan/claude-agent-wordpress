# Claude SEO & Performance Optimizer

A WordPress plugin that uses Claude AI to provide intelligent SEO optimization and performance recommendations.

## Features

### SEO Optimization
- **AI-Powered SEO Analysis**: Get detailed SEO scores and recommendations for each post/page
- **Automatic Meta Description Generation**: Let Claude write compelling meta descriptions
- **Focus Keyword Optimization**: Analyze how well your content targets specific keywords
- **Internal Linking Suggestions**: Get AI recommendations for internal linking opportunities
- **Content Quality Assessment**: Receive actionable feedback to improve your content

### Performance Optimization
- **Site Performance Analysis**: Comprehensive analysis of your WordPress site
- **Image Optimization Detection**: Identify images that need optimization
- **Database Cleanup Recommendations**: Find and remove unnecessary database clutter
- **Automatic Optimizations**: Enable built-in performance enhancements:
  - Gzip compression
  - Remove query strings from static resources
  - Disable WordPress emojis
  - Defer JavaScript loading

## Installation

1. **Download the Plugin**
   - Download all files in this directory

2. **Upload to WordPress**
   - Zip the entire plugin folder
   - Go to WordPress Admin → Plugins → Add New → Upload Plugin
   - Upload the zip file and activate

3. **Configure API Key**
   - Go to Claude SEO → Settings
   - Get your API key from [console.anthropic.com](https://console.anthropic.com/)
   - Enter your API key and save

## Usage

### For Individual Posts/Pages

1. **Edit any post or page**
2. **Scroll to the "Claude SEO Assistant" meta box**
3. **Click "Analyze with Claude"** to get an SEO score and suggestions
4. **Click "Generate with Claude"** to create an optimized meta description
5. **Save your post** to store the SEO data

### From the Dashboard

1. **Go to Claude SEO in the admin menu**
2. **View your site overview** and posts needing attention
3. **Go to Performance** to analyze your site's speed and efficiency
4. **Enable auto-optimizations** to improve performance automatically

## API Costs

This plugin uses the Claude API, which has usage costs:
- Approximately $0.01 - $0.03 per SEO analysis
- Costs depend on content length
- You only pay when you actively request analysis

Check [Anthropic's pricing page](https://www.anthropic.com/pricing) for current rates.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- A Claude API key from Anthropic
- Active internet connection for API calls

## What Makes This Plugin Different

Unlike the previous complex version, this plugin:

✓ **Actually works** - Real API integration with Claude
✓ **Focused purpose** - Does SEO and performance well, not everything poorly
✓ **Practical features** - Tools you'll actually use
✓ **Secure** - Proper security checks and data handling
✓ **Cost-effective** - You only pay for what you use
✓ **No bloat** - Clean, efficient code

## Privacy & Security

- Your API key is stored securely in your WordPress database
- Content is only sent to Anthropic when you request analysis
- No data is stored on external servers except during API calls
- Review [Anthropic's privacy policy](https://www.anthropic.com/privacy)

## Support

For issues or questions:
1. Check your API key is correctly configured
2. Ensure your server meets the requirements
3. Check the browser console for JavaScript errors
4. Verify your API key has sufficient credits

## License

GPL v2 or later

## Credits

Built with the Claude API by Anthropic
