# ACF Blocks

- Blocks are created by referring to this [tutorial](https://www.advancedcustomfields.com/resources/blocks/), or by referring to an existing block in this directory.

## Assets

### File Structure

- 📂 your-block
  - 📂 scss
    - \_block.scss
    - \_editor.scss
  - 📂 js
  - block.json
  - your-block.php

### Enqueue

- The scss assets are automatically called using the sass glob method
  - [front.scss](../../assets/scss/front.scss)
  - [editor.scss](../../assets/scss/editor.scss)
- The js assets are automatically called using the vite.js glob method
  - [front.js](../../assets/js/front.js)
  - [editor.js](../../assets/js/editor.js)

### Compilation

- see [package.json](../../package.json)

## Translation

- The translations for the blocks should be generated at the same time as those for the theme (See [README](../../README.md#translation))
