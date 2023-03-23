# rtCamp WordPress Slideshow Plugin

Programming submission for rtCamp Assessment - Senior WordPress Engineer.

## WordPress Plugin Challenge

There are already more than 10000 WordPress plugins out there. So it’s really hard to set up a challenge for you! Still, we need to create a playground for you. So we came up with a few ideas for the plugin. Go ahead with anyone below:

### Challenge-1: WordPress-Slideshow Plugin

This plugin will test if you are familiar with WordPress shortcodes.

#### Admin-Side:

- Create an admin-side settings page.
- Provide an interface to add/remove images from plugin settings page.
- User must be able to change order of uploaded image. (Hint: Use http://jqueryui.com/demos/sortable/#connect-lists )

#### Front-end:

- Create provision for a shortcode like [myslideshow]
- When you add shortcode [myslideshow] to any page, it will be replaced by a slideshow of images uploaded from admin-side.
- You can use any jQuery slideshow library/plugin.

## Description

A simple and customizable WordPress plugin that allows you to create beautiful and responsive slideshows for your website.

## Installation

- Download the plugin from GitHub.
- Extract and copy the rtcamp-wp-slideshow folder to the /wp-content/plugins/ directory on your WordPress installation.
- Activate the plugin through the 'Plugins' menu in WordPress.

## Usage - Add New Slider
- Click on the 'WP Slideshow' tab in the WordPress admin menu.
- Click on 'Add New Slider' to start creating your new slideshow.
- Give your slideshow a name that will help you identify it later, select the status (draft or published), and add slides by clicking on the 'Add Slide' button located at the top of the page.
- Add new images to your slider by clicking on the 'Add Image' button or select images from the WordPress media modal.
- Arrange the order of your images with ease using our intuitive drag and drop interface.
- Save your slideshow by clicking on the 'Save Slider' button.
To display your slideshow on your website, simply copy the shortcode from the main page or add the shortcode [rtcamp_wp_slideshow id="YOUR_SLIDESHOW_ID"] to any post or page.

## Usage - Edit Slider
- Click on the 'WP Slideshow' tab in the WordPress admin menu.
- To edit the content of a slide, click on the slide you want to edit and click 'Edit'.
- Make your changes to your slide.
- Click on the 'Save Changes' button to save your changes.

## Stacks

**Front-end:** SliderJS

**Back-end:** PHP, jQuery

**Coding Standard:** PHP Code Sniffer, Wordpress coding standards and JSHint



## How to run some tests

**PHP Code Sniffer with WordPress coding standards**:

Install:
```bash
  composer install
```

Test using this:
```bash
  phpcs --standard=WordPress file_name_here.php
```

Fix small things:
```bash
  phpcbf --standard=WordPress file_name_here.php
```

**JSHint**:

Install:
```bash
  npm install
```

Test JS file from admin:
```bash
  npm run lint-admin-js
```

Test JS file from client:
```bash
  npm run lint-client-js
```