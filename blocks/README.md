# Blocks

This directory contains all custom blocks for the theme.

## Block Structure

All blocks follow a universal structure for both ACF and native (React) blocks. For detailed documentation on how blocks are structured and built, please refer to:

**[Universal Processing of WordPress Blocks: ACF & Native (React)](https://latablerouge.ninja/universal-processing-of-wordpress-blocks-acf-native-react/)**

## Block Registration

Blocks are automatically registered using the universal registration system in [`inc/blocks/blocks-register.php`](../inc/blocks/blocks-register.php).
This system scans for `block.json` files and registers all blocks and their assets automatically.

## Block Helpers

For ACF blocks, helper functions are available in [`inc/blocks/acf/blocks-helpers.php`](../inc/blocks/acf/blocks-helpers.php).
These helpers provide utilities for handling block styles, classes, and theme colors.

## Overrides

The `/overrides` folder is used for overriding plugins and core theme blocks. This allows you to customize existing blocks without modifying the original source files.

## Build Process

Blocks are built using the universal build process. See [`package.json`](../package.json) for available commands:

- `npm run watch:blocks` - Watch and build blocks in development mode
- `npm run build:blocks` - Build blocks for production

All blocks are automatically built and registered using the universal build process described in the documentation.
