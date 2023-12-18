# Core Blocks

- Place the files for overriding Gutenberg native blocks here
  1. Create a **block-to-override/** folder containing assets to be compiled

## Assets

### File Structure

- 📂 block-paragraph
  - 📂 scss
    - \_block.scss
    - \_editor.scss
  - 📂 js

### Enqueue

- The scss assets are automatically called using the sass glob method
  - [front.scss](../../assets/scss/front.scss)
  - [editor.scss](../../assets/scss/editor.scss)
- The js assets are automatically called using the vite.js glob method
  - [front.js](../../assets/js/front.js)
  - [editor.js](../../assets/js/editor.js)

### Compilation

- see [package.json](../../package.json)
