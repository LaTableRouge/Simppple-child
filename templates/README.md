# Templates

- Place template files here

1. A template file **your-template.html**
2. If necessary, a **your-template/** folder containing assets to be compiled

## Assets

### File Structure

- 📂 your-template
  - 📂 scss
    - \_template.scss
  - 📂 js
- your-template.html

### Enqueue

- The scss assets are automatically called using the sass glob method
  - [front.scss](../assets/scss/front.scss)
- The js assets need to be enqueued in the theme files
  - [front.js](../assets/js/front.js)

### Compilation

- see [package.json](../package.json)
