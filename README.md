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

**Usage:**
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

**Required arguments:**
- **--name**: Identifier of the block, it will be registered to that name and the folder structure will be named after it.
- **--title**: Name of the block, displayed in the WP admin area

**Usage:**
```bash
wp create-wpb-block --name=my-block --title="My Block"
```

**Creates:**
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

**Required arguments:**
- **--name**: Identifier of the page, the template will be named after it.

**Usage:**
```bash
wp create-wpb-page --name=standard
```

**Creates:**
```
pages/standard/
├── standard-page-renderer.php
└── standard-page-template.twig
```

### Create Menu Configuration
Creates a new menu configuration file.

**Required arguments:**
- **--name**: Identifier of the menu, the configuration file will be named after it

**Usage:**
```bash
wp create-wpb-menu --name=main
```

**Creates:**
```
menus/main-menu.php
```

### Create Options Page
Creates a new ACF options page configuration.

**Required arguments:**
- **--name**: Identifier of the options page, the configuration file will be named after it

**Usage:**
```bash
wp create-wpb-options --name=site
```

**Creates:**
```
options/site-options.php
```

### Create Custom Post Type
Creates a custom post type configuration file to be automatically loaded by WP-Battery.

Required arguments:
- **--name**: Identifier of the post type, the configuration file will be named after it
- **--namespace**: Plugin/Theme namespace, for translations

**Usage:**
```bash
wp create-wpb-post-type --name=product --namespace=my-theme
```

**Creates:**
```
post-types/post-type-products.php
```

### Create Custom Taxonomy
Creates a custom taxonomy configuration file to be automatically loaded by WP-Battery.

**Required arguments:**
- **--name**: Identifier of the taxonomy, the configuration file will be named after it
- **--namespace**: Plugin/Theme namespace, for translations
- **--post-type**: Which post type this taxonomy will be registered for 
  - Note: Currently WP-Battery CLI only supports a single post type here. Of course, more can be added later.

**Usage:**
```bash
wp create-wpb-taxonomy --name=product-category --namespace=my-theme --post-type=product
```

**Creates:**
```
taxonomies/taxonomy-product-category.php
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
wp create-wpb-options --name=site
wp create-wpb-post-type --name=product --namespace=my-theme
wp create-wpb-taxonomy --name=product-category --namespace=my-theme --post-type=product

# Invalid names
wp create-wpb-block --name="Hero Section" # Contains spaces
wp create-wpb-block --name="MYBLOCK"      # Contains uppercase
wp create-wpb-block --name="-block-"      # Starts/ends with hyphen
```

----

## Renderer Functions

Both blocks and pages support custom render functions for data processing. These functions must:
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