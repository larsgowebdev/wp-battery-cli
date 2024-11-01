# WP-Battery CLI Plugin

A WordPress CLI plugin that provides commands for creating and managing [WP-Battery](https://github.com/larsgowebdev/wp-battery) theme components. This plugin extends WP-CLI to streamline the development workflow with WP-Battery themes.

## Requirements

- PHP >= 8.1
- WordPress >= 6.5 (Composer-based), [Bedrock](https://github.com/roots/bedrock) recommended
    - WP-CLI installed and configured
- [WP-Battery](https://github.com/larsgowebdev/wp-battery) theme framework installed

## Installation

1. Install the plugin with composer:
```bash
composer require larsgowebdev/wp-battery-cli
```

2. Make sure the plugin is activated:
```bash
wp plugin activate wp-battery-cli
```

3. Verify installation:
```bash
wp help create-wpb-block
```

## Available Commands

### Initialize WP-Battery Structure
Creates the required folder structure for a WP-Battery theme.

```bash
wp init-wpb
```

This creates the following structure in your theme:
```
wpb/
├── acf-sync/
├── blocks/
├── cf7-templates/
├── menus/
├── options/
├── pages/
└── template-parts/
```

### Create Block
Creates a new block with required files and structure.

```bash
wp create-wpb-block --name=my-block --title="My Block"
```

Creates:
```
blocks/my-block/
├── assets/
    ├── my-block-script-all.js 
    ├── my-block-script-editor.js 
    ├── my-block-script-frontend.js 
    ├── my-block-style-all.js 
    ├── my-block-style-editor.js 
    └── my-block-style-frontend.js 
├── block.json
├── my-block-block-renderer.php
└── my-block-block-template.twig
```

### Create Page Template
Creates a new page template with required files.

```bash
wp create-wpb-page --name=standard
```

Creates:
```
pages/standard/
├── standard-page-renderer.php
└── standard-page-template.twig
```

### Create Menu Configuration
Creates a new menu configuration file.

```bash
wp create-wpb-menu --name=main
```

Creates:
```
menus/main-menu.php
```

### Create Options Page
Creates a new ACF options page configuration.

```bash
wp create-wpb-options --name=site
```

Creates:
```
options/site-options.php
```

## Naming Conventions

All component names must follow these rules:
- Lowercase letters only
- Numbers allowed
- Hyphens and underscores allowed
- No spaces or special characters
- Cannot start or end with hyphen/underscore
- Cannot contain consecutive hyphens/underscores

Examples:
```bash
# Valid names
wp create-wpb-block --name=hero-section --title="Hero Section"
wp create-wpb-block --name=testimonials --title="Testimonials"
wp create-wpb-page --name=standard
wp create-wpb-menu --name=footer-menu

# Invalid names
wp create-wpb-block --name="Hero Section" # Contains spaces
wp create-wpb-block --name="MYBLOCK"      # Contains uppercase
wp create-wpb-block --name="-block-"      # Starts/ends with hyphen
```

## Component Structure

### Blocks
Each block consists of:
- `block.json`: Block configuration
- `*-block-template.twig`: Twig template for the block
- `*-block-renderer.php`: Optional PHP renderer for additional data processing

### Pages
Each page template consists of:
- `*-page-template.twig`: Base Twig template
- `*-page-renderer.php`: Optional PHP renderer for additional data processing

### Menus
Menu configuration files return an array defining the menu structure:
```php
return [
    'Menu Name' => [
        'items' => []
    ]
];
```

### Options
Options configuration files return an array defining the ACF options page:
```php
return [
    'identifier' => [
        'page_title' => 'Page Title',
        'menu_title' => 'Menu Title',
        'menu_slug'  => 'menu-slug',
        'capability' => 'edit_posts',
        'position'   => '25',
        'redirect'   => false
    ]
];
```

## Renderer Functions

Both blocks and pages support render functions for data processing. These functions must:
- Contain the word 'render' in their name
- Accept one parameter ($context)
- Return the modified context

Example:
```php
function render_my_component($context)
{
    $context['custom_data'] = 'value';
    return $context;
}
```

## Safety Features

- Prevents overwriting existing components
- Validates component names before creation
- Provides helpful error messages and suggestions
- Creates .gitkeep files in empty directories
- Adds appropriate .gitignore rules

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the GPL-2.0 License.