# Bootstrap Product Display

**Contributors:** D Kandekore  
**Tags:** woocommerce, bootstrap, products, grid, elementor, shortcode  
**Requires at least:** 5.0  
**Tested up to:** 6.4  
**Stable tag:** 1.7  
**License:** GPLv2 or later  

Display WooCommerce products in responsive Bootstrap cards using a flexible Shortcode or Elementor widget.

## Description

Bootstrap Product Display is a lightweight plugin that allows you to showcase your WooCommerce products in a clean, responsive grid layout. It utilizes **Bootstrap 5** for styling and provides extensive customization options for colors, descriptions, and call-to-action buttons.

Whether you are using the classic WordPress editor or **Elementor**, this plugin makes it easy to list products by category, control column layouts, and add decorative icons.

### Key Features
* **Responsive Grid:** Automatically adjusts columns based on screen size using Bootstrap 5.
* **Customizable Design:** Change border colors, background colors, text colors, and button styles globally.
* **Smart Fallbacks:** Automatically displays a default image if a product has no thumbnail.
* **Shortcode Support:** Use `[bootstrap_products]` anywhere on your site.
* **Elementor Ready:** Includes a dedicated "Bootstrap Product Display" widget.
* **Font Awesome Integration:** Add icons to your product cards easily.

## Requirements

1.  **WooCommerce** (Required for product data).
2.  **Elementor** (Optional, required only if you wish to use the Elementor widget).

## Installation

1.  Upload the plugin files to the `/wp-content/plugins/bootstrap-wc-products` directory, or install the plugin through the WordPress plugins screen.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Ensure WooCommerce is installed and active.

## Configuration

Once activated, you can configure the global styling settings for your product cards.

1.  Navigate to **Settings > Product Display** in your WordPress Dashboard.
2.  **Adjust the following settings:**
    * **Border Color:** Color of the card border.
    * **Divider Color:** Color of the horizontal dividers inside the card.
    * **Background Color:** Background color of the product card.
    * **Text Color:** Color of the title and description text.
    * **Button Color:** Background color of the "Find out more" button.
    * **Description Limit:** Maximum number of characters to display for the product description.
    * **Button Text:** Custom text for the link button (e.g., "View Product").
    * **Default Image (URL):** A fallback image URL to use if a product is missing its thumbnail.

## Usage

You can display products using either the Shortcode or the Elementor Widget.

### 1. Shortcode Usage

Add the `[bootstrap_products]` shortcode to any page, post, or text widget.

**Attributes:**
* `category`: (Optional) The slug(s) of the product category you want to display. Separate multiple slugs with commas.
* `columns`: Number of columns to display per row (Default: 3).
* `limit`: Total number of products to show (Default: 12).
* `icon_class`: Font Awesome class for the decorative icon (e.g., `fa-solid fa-star`).

**Examples:**

* **Default display (recent products):**
    ```
    [bootstrap_products]
    ```

* **Display specific category in 4 columns:**
    ```
    [bootstrap_products category="clothing" columns="4" limit="8"]
    ```

* **Display with a specific icon:**
    ```
    [bootstrap_products icon_class="fa-solid fa-gift"]
    ```

### 2. Elementor Widget

If you use the Elementor Page Builder:

1.  Open your page in the **Elementor Editor**.
2.  Search for the **Bootstrap Product Display** widget (located in the "General" category).
3.  Drag and drop the widget onto your page.
4.  **Configure the settings in the panel:**
    * **Category:** Select one or multiple categories from the dropdown list.
    * **Columns:** Choose how many products to show per row (1-6).
    * **Number of Products:** Set the maximum number of items to display.
    * **Icon CSS Class:** Enter a Font Awesome class (see below).

## Font Awesome Icons

This plugin supports **Font Awesome 6**. You can add an icon that appears centrally on the divider between the product image and the content.

To find an icon:
1.  Visit the [Font Awesome Gallery](https://fontawesome.com/search?o=r&m=free).
2.  Click on an icon you like.
3.  Copy the HTML class code (e.g., `fa-solid fa-mug-hot`).
4.  Paste this class into the **icon_class** attribute in the shortcode or the **Icon CSS Class** field in Elementor.

## Changelog

### 1.7
* Added Elementor Widget support.
* Improved fallback image logic.
* Updated Font Awesome to version 6.5.2.
* Added global settings for styling (Settings > Product Display).