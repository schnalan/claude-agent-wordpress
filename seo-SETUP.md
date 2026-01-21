# Quick Setup Guide

## Step 1: Get Your Claude API Key

1. Go to https://console.anthropic.com/
2. Sign up or log in
3. Navigate to "API Keys"
4. Create a new API key
5. Copy the key (starts with `sk-ant-`)

## Step 2: Install the Plugin

1. Download all files from this folder
2. Create a zip file of the entire `claude-seo-optimizer` folder
3. In WordPress Admin, go to Plugins → Add New
4. Click "Upload Plugin"
5. Choose your zip file and click "Install Now"
6. Click "Activate Plugin"

## Step 3: Configure

1. In WordPress Admin, go to "Claude SEO" → "Settings"
2. Paste your API key
3. Click "Save API Key"
4. (Optional) Click "Test Connection" to verify it works

## Step 4: Start Optimizing!

### For SEO:
1. Edit any post or page
2. Scroll down to find "Claude SEO Assistant" box
3. Enter a focus keyword (optional)
4. Click "Analyze with Claude" for SEO score and tips
5. Click "Generate with Claude" for a meta description
6. Save your post

### For Performance:
1. Go to "Claude SEO" → "Performance"
2. Click "Run Performance Analysis"
3. Review recommendations
4. Enable "Auto-Optimize" for automatic improvements
5. Follow manual suggestions for additional gains

## Costs

- Each SEO analysis costs approximately $0.01-$0.03
- You only pay when you click the analysis buttons
- No recurring fees, just pay-as-you-go

## Troubleshooting

**"API key required" error:**
- Make sure you saved your API key in Settings
- Verify the key starts with `sk-ant-`

**Analysis not working:**
- Check your API key has credits
- Verify your server can make outbound HTTPS requests
- Check browser console for errors (F12)

**Performance features not working:**
- Make sure you've enabled "Auto-Optimize" in Performance settings
- Some features require server-level access

## Tips for Best Results

1. **Write good content first** - AI can optimize, but can't fix poor content
2. **Use focus keywords** - Tell Claude what you're targeting
3. **Review suggestions** - Claude provides guidance, but you make final decisions
4. **Run analysis before publishing** - Catch issues early
5. **Check performance regularly** - Monthly analysis helps maintain speed

## Need Help?

- Check the main README.md for more details
- Review Anthropic's API documentation: https://docs.anthropic.com/
- Verify your WordPress meets requirements (WP 5.0+, PHP 7.4+)
