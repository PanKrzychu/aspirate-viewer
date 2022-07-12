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
add_shortcode('pv', 'av_getShortcodePodcasts');
add_shortcode('bv', 'av_getShortcodeBooks');
add_shortcode('cv', 'av_getShortcodeCourses');

add_shortcode('dv', 'av_getShortcodeDictionary');

add_shortcode('object-info', 'av_getShortcodeObject');
add_shortcode('lider-info', 'av_getShortcodeLiderInfo');
add_shortcode('podcast-info', 'av_getShortcodePodcastInfo');
add_shortcode('book-info', 'av_getShortcodeBookInfo');
add_shortcode('course-info', 'av_getShortcodeCourseInfo');
// add_shortcode('product', 'av_getShortcodeProduct');

add_shortcode('cc', 'av_getShortcodeContentCounter');

add_action('rest_api_init', 'AVApi::registerRoutes');




function av_getShortcodeLiders( $atts ) {

    $args = shortcode_atts( array(
        'is_foreign' => false
    ), $atts );

    require_once plugin_dir_path(__FILE__) . 'templates/templates/lists/shortcode-liders.php';

    return $content;

}

// function av_getShortcodeProduct() {

//     require_once plugin_dir_path(__FILE__) . 'templates/shortcode-product.php';

//     return $content;

// }

function av_getShortcodePodcasts() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/lists/shortcode-podcasts.php';

    return $content;

}

function av_getShortcodeBooks() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/lists/shortcode-books.php';

    return $content;

}

function av_getShortcodeCourses() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/lists/shortcode-courses.php';

    return $content;

}

function av_getShortcodeDictionary() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/lists/shortcode-dictionary.php';

    return $content;

}


function av_getShortcodeObject() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/objects/shortcode-object.php';

    return $content;

}

function av_getShortcodeLiderInfo() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/objects/shortcode-lider-info.php';

    return $content;

}

function av_getShortcodePodcastInfo() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/objects/shortcode-podcast-info.php';

    return $content;

}

function av_getShortcodeBookInfo() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/objects/shortcode-book-info.php';

    return $content;

}

function av_getShortcodeCourseInfo() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/objects/shortcode-course-info.php';

    return $content;

}

function av_getShortcodeContentCounter() {

    require_once plugin_dir_path(__FILE__) . 'templates/templates/other/shortcode-content-counter.php';

    return $content;

}
