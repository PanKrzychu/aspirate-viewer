<?php

require_once plugin_dir_path(__FILE__) . '../AVApi.php';

wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/content-counter-styles.css'));

?>

<div class="av-content-counter-container">
    <h2 class="av-content-counter-title">Jakiś tytuł</h2>
    <div>
        <div class="av-single-counter">x</div>
        <div class="av-single-counter">y</div>
        <div class="av-single-counter">z</div>
        <div class="av-single-counter">za</div>
    </div>
</div>
