# Parts

- Place part files here

1. A part file **your-part.html**
2. If necessary, a **your-part/** folder containing assets to be compiled

## Assets

### File Structure

- 📂 your-part
  - 📂 scss
    - \_part.scss
  - 📂 js
- your-part.html

### Enqueue

- The scss assets are automatically called using the sass glob method
  - [front.scss](../assets/scss/front.scss)
- The js assets need to be enqueued in the theme files
  - [front.js](../assets/js/front.js)

### Compilation

- see [package.json](../package.json)
