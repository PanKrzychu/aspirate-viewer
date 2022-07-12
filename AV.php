<?php

/**
 * @package  Aspirate Viewer
 */
/*
Plugin Name: Aspirate Viewer
Plugin URI: http://markofani.com.pl
Description: This plugin generates shortcode containing table with liders of marketing, podcasts and books and custom search engine. Shortcodes: [lv], [av], [av], [cv], [cc].
Version: 1.0.0
Author: Krzysztof Czachor
Author URI: https://markofani.com.pl/
Text Domain: aspirate-viewer
*/

if(!defined('ABSPATH'))
{
	die ('You can not access this file!');
}
require_once plugin_dir_path(__FILE__) . 'AVApi.php';

$plugin = plugin_basename(__FILE__);

add_shortcode('lv', 'av_getShortcodeLiders');
add_shortcode('lv-lider', 'av_getShortcodeLider');
add_shortcode('lv-products', 'av_getShortcodeLiderProducts');
add_shortcode('product', 'av_getShortcodeProduct');

add_shortcode('pv', 'av_getShortcodePodcasts');
add_shortcode('bv', 'av_getShortcodeBooks');
add_shortcode('cv', 'av_getShortcodeCourses');
add_shortcode('dv', 'av_getShortcodeDictionary');

add_shortcode('cc', 'av_getShortcodeContentCounter');

add_action('rest_api_init', 'AVApi::registerRoutes');




function av_getShortcodeLiders( $atts ) {

    $args = shortcode_atts( array(
        'is_foreign' => false
    ), $atts );

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-liders.php';

    return $content;

}

function av_getShortcodeLider() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-lider.php';

    return $content;

}

function av_getShortcodeLiderProducts() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-lider-products.php';

    return $content;

}

function av_getShortcodeProduct() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-product.php';

    return $content;

}

function av_getShortcodePodcasts() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-podcasts.php';

    return $content;

}

function av_getShortcodeBooks() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-books.php';

    return $content;

}

function av_getShortcodeContentCounter() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-content-counter.php';

    return $content;

}

function av_getShortcodeCourses() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-courses.php';

    return $content;

}

function av_getShortcodeDictionary() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-dictionary.php';

    return $content;

}

