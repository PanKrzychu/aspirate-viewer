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
add_shortcode('pv', 'av_getShortcodePodcasts');
add_shortcode('bv', 'av_getShortcodeBooks');
add_shortcode('cv', 'av_getShortcodeCourses');

add_shortcode('cc', 'av_getShortcodeContentCounter');

add_action('rest_api_init', 'AVApi::registerRoutes');




function av_getShortcodeLiders() {

    require_once plugin_dir_path(__FILE__) . 'templates/shortcode-liders.php';

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

