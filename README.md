# Simppple-child Theme

![WordPress Version](https://img.shields.io/badge/wordpress-%3E%3D%206.4-blue)
![Node](https://img.shields.io/badge/node-%3E%3D%2018-brightgreen)
![PHP](https://img.shields.io/badge/php-%5E8.0-blue)
![ACF Version](https://img.shields.io/badge/acf-%3E%3D%206.0-cyan)

Simppple-child is a Wordpress child theme of the [Simppple theme](https://github.com/LaTableRouge/Simppple) designed for developpers. You can use this theme as a sandbox to create your own react/acf blocks or patterns.
<br>
The [parent theme](https://github.com/LaTableRouge/Simppple) will be updated often (maybe once a year idk 🤷‍♀️) so don't touch it. If you want to change things in the parent theme, use this one instead to do it.

## File Structure

- 📂 **simple-child** (child theme)
  - 📂 [assets](./assets/)
    - Contains assets that will be compiled (scss, js, fonts, img, etc...)
    - 📂 fonts
      - 📂 icomoon
    - 📂 img
    - 📂 js
    - 📂 scss
  - 📂 build
    - Contains all the compiled assets (css, js, fonts, img, etc...)
  - 📂 blocks
    - 📂 [acf](./blocks/acf/README.md)
    - 📂 [react](./blocks/react/README.md)
    - 📂 [core](./blocks/core/README.md)
    - 📂 [woocommerce](./blocks/woocommerce/README.md)
  - 📂 [patterns](./patterns/README.md)
  - 📂 [inc](./inc/)
    - Contains PHP files that are used to customize the theme & assist in theme development
    - 📂 blocks
      - Everything related to custom blocks (category creation, register, etc...)
    - 📂 pattern
      - Everything related to custom patterns (category creation, etc...)
    - 📂 theme-customization
      - Everything related to deeper theme customization (unnecessary menus, etc...)
  - 📂 [lang](./lang/)
    - Contains translation files specific to the theme
  - 📂 [parts](./parts/README.md)
    - Theme template parts (Header, Footer, etc...)
  - 📂 styles
    - All the style variations of the theme
  - 📂 [templates](./templates/README.md)
    - Pages templates of the theme (404, archive, single, product, etc...)
  - [theme.json](./theme.json)
    - Contains all possible global configuration for the theme's styles
  - [functions.php](./functions.php)
    - Calls PHP files that are used to customize the theme & assist in theme development
  - [style.css](./style.css)
    - Contains useful theme information (author, version, etc...)
  - screenshot.png
    - Presentation image of the theme

## Development Guide

### Installing Dependencies

If not already done, run `npm install` in this directory

### 🧙‍♂️ Development Scripts

We use vite.js to facilitate and optimize our development.

The list of development scripts is listed below:

| NPM Command                | Action                                                                                                                                               |
| -------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| npm run prod               | compiles `simppple-child` theme files (\*.scss, \*.js) and deploys static files to the **build/** directory of the theme.                                  |
| npm run build              | lints, formats, and compiles `simppple-child` theme files (\*.php, \*.scss, \*.js) and deploys static files to the **build/** directory of the theme.      |
| npm run watch              | starts a local development server accessible directly on **local.your-host.com**, compiles and reloads static files (\*.scss, \*.js) on each change. |
| npm run watch:react-blocks | starts the compilation of React blocks, compiles and reloads static files (\*.scss, \*.js) on each change.                                           |
| npm run build:react-blocks | compiles React blocks, the blocks are compiled in the **blocks/react/build/** directory of the theme.                                                |

### Overriding Gutenberg Native Blocks

Gutenberg's native editor blocks can be overridden by creating files in the `blocks/core/` ([see README](./blocks/core/README.md)) directory of your theme.

### Overriding Woocommerce Native Blocks

Woocommerce's native Gutenberg blocks can be overridden by creating files in the `blocks/woocommerce/` ([see README](./blocks/woocommerce/README.md)) directory of your theme.

### Creating ACF Blocks

ACF (Advanced Custom Fields) blocks should be created and edited in the `blocks/acf/` ([see README](./blocks/acf/README.md)) directory of your theme.

### Creating REACT Blocks

React blocks should be created and edited in the `blocks/react/src/` ([see README](./blocks/react/src/README.md)) directory of your theme.

### Creating Patterns

Patterns can be created and edited in the `patterns/` ([see README](./patterns/README.md)) directory of your theme.

### Creating Parts

Parts can be created and edited in the `parts/` ([see README](./parts/README.md)) directory of your theme.

### Creating Templates

Templates can be created and edited in the `templates/` ([see README](./templates/README.md)) directory of your theme.

### Translation

To generate the .pot file (from the theme's directory):

```bash
wp i18n make-pot . lang/simppple.pot --domain=simppple --exclude=node_modules,vendor,lang --include=*.php,blocks,build
```

To generate the translation json files for JS (from the theme's directory):

```bash
wp i18n make-json lang/ --no-purge
```

## Additional Resources

### Documentation

To learn more about using and customizing the theme:

- [Full Site Editing With WordPress](https://fullsiteediting.com/)
- [Theme example with Woocommerce store](https://themedemos.com/jace/)
