# LatePoint Quick Status

A WordPress plugin addon for LatePoint that adds a toggle button to enable/disable services directly from the services list.

![image](screenshots/quick-status-2.gif)

## Version 1.1.0 - Updated for LatePoint 5.2.1+

This version has been updated to be fully compatible with LatePoint 5.2.1 and Pro Features 1.20, featuring improved styling, better integration, and enhanced user experience.

## Features

- **Quick Toggle**: Enable/disable services with a single click
- **Real-time Updates**: AJAX-powered status changes without page reload
- **Visual Feedback**: Color-coded buttons showing current status (green for disabled services, red for active services)
- **Enhanced Loading States**: Smooth loading animations using LatePoint's design system
- **Improved Notifications**: Better integration with LatePoint's notification system
- **Security**: Proper nonce verification and capability checks
- **Seamless Integration**: Integrates perfectly with LatePoint's admin interface
- **Accessibility**: Full keyboard navigation and screen reader support
- **Responsive Design**: Works perfectly on all device sizes

## What's New in Version 1.1.0

### Updated for LatePoint 5.2.1 Compatibility
- **Modern CSS**: Updated to use CSS custom properties and LatePoint's new design system
- **Improved Button Styling**: Better visual hierarchy with proper color coding
- **Enhanced Loading States**: Smooth animations that match LatePoint's UI patterns
- **Better Error Handling**: More robust error states with visual feedback

### Enhanced JavaScript
- **Modular Architecture**: Cleaner, more maintainable code structure
- **Better Event Handling**: Improved event delegation and cleanup
- **Enhanced Notifications**: Better integration with LatePoint's notification system
- **Performance Improvements**: Optimized AJAX handling and DOM manipulation

### Accessibility Improvements
- **Keyboard Navigation**: Full keyboard support for all interactions
- **Screen Reader Support**: Proper ARIA labels and announcements
- **High Contrast Mode**: Support for high contrast display preferences
- **Reduced Motion**: Respects user's motion preferences

## Installation

1. Upload the `latepoint-quick-status` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure LatePoint is installed and activated

## Usage

1. Navigate to LatePoint → Services in your WordPress admin
2. Each service card will now have a status toggle button
3. Click the button to instantly enable/disable the service
4. The button will update in real-time with the new status

## Technical Details

### Integration Points

- **Registration**: Uses `latepoint_installed_addons` filter to register with LatePoint
- **UI Integration**: Uses `latepoint_service_tile_info_rows_after` hook to add toggle buttons
- **Asset Loading**: Uses `latepoint_admin_enqueue_scripts` to load JS/CSS
- **AJAX Handling**: Custom `wp_ajax_lp_toggle_service` endpoint for status toggling

### Security Features

- **Nonce Verification**: Prevents CSRF attacks
- **Capability Checking**: Only users with `manage_options` capability can toggle services
- **Input Sanitization**: All inputs are properly validated and sanitized
- **Error Handling**: Comprehensive error messages and proper HTTP status codes

### File Structure

```
latepoint-quick-status/
├── latepoint-quick-status.php         # Main plugin file
├── assets/
│   ├── js/
│   │   └── toggle.js                  # JavaScript functionality
│   └── css/
│       └── style.css                  # CSS styling
├── languages/                         # Translation files
├── lib/
│   ├── helpers/
│   │   └── quick_status_helper.php    # Helper functions
│   ├── controllers/                   # Controller classes
│   ├── models/                        # Model classes
│   └── views/                         # View templates
└── README.md                          # This file
```

## Migration from Version 1.0.0

### Automatic Updates
- CSS classes are backward compatible
- JavaScript functions maintain the same API
- No database changes required

### New Features Available
- Enhanced loading animations
- Better error handling
- Improved accessibility
- Modern design system integration

### Breaking Changes
- None - fully backward compatible

## Customization

### CSS Styling

The toggle button styles can be customized by modifying `assets/css/style.css`. Key CSS custom properties:

```css
/* Button styling */
.lp-toggle-service {
    border-radius: var(--latepoint-border-radius, 6px);
    /* Uses LatePoint's design tokens */
}

/* Status-specific colors */
.lp-toggle-service[data-status="disabled"] {
    background-color: #28a745; /* Green for Enable */
}

.lp-toggle-service[data-status="active"] {
    background-color: #dc3545; /* Red for Disable */
}
```

### JavaScript Behavior

The AJAX behavior can be customized in `assets/js/toggle.js`:

```javascript
// Configuration object
const CONFIG = {
    loadingDelay: 300,    // Loading state delay
    reloadDelay: 1500     // Page reload delay
};

// Public API for external customization
window.LPQuickStatus.reinitialize();
```

## Browser Support

- **Modern Browsers**: Chrome 60+, Firefox 60+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 60+
- **Accessibility**: WCAG 2.1 AA compliant

## Performance

- **CSS**: Optimized with modern properties and efficient selectors
- **JavaScript**: Minimal DOM manipulation with event delegation
- **AJAX**: Debounced requests with proper error handling
- **Loading**: Lazy initialization and cleanup

## Troubleshooting

### Common Issues

1. **Buttons not appearing**: Ensure LatePoint 5.2.1+ is active
2. **Styling issues**: Clear browser cache and check for CSS conflicts
3. **AJAX errors**: Check browser console and WordPress debug logs

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Support

For support and bug reports, please contact the plugin author or check the WordPress admin for error messages.

## License

This plugin is released under the GPL v2 or later license.

## Changelog

### 1.1.0 (Current)
- **Updated**: Full compatibility with LatePoint 5.2.1 and Pro Features 1.20
- **Enhanced**: Modern CSS using design system and custom properties
- **Improved**: JavaScript architecture with better error handling
- **Added**: Accessibility features and responsive design improvements
- **Added**: Loading animations and visual feedback enhancements
- **Added**: Support for reduced motion and high contrast preferences

### 1.0.0
- Initial release
- Complete integration with LatePoint
- AJAX-powered service toggling
- Comprehensive security measures
- Professional UI styling with color-coded buttons (green for disabled, red for enabled)
