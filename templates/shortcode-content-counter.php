<?php

require_once plugin_dir_path(__FILE__) . '../AVApi.php';

wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/content-counter-styles.css'));

$value1 = 765;


?>

<div class="av-content-counter-container">
    <h2 class="av-content-counter-title">ASPIRATE w liczbach</h2>
    <div class="av-counter-container">
        <div class="av-single-counter">
            <div class="av-counter-value"><?php echo $value1 ?></div>
            <div class="av-counter-title">Dodanych newsów marketingowych</div>
        </div>
        <div class="av-single-counter">
            <div class="av-counter-value">7</div>
            <div class="av-counter-title">Polskich liderów marketingu</div>
        </div>
        <div class="av-single-counter">
            <div class="av-counter-value">7</div>
            <div class="av-counter-title">Książek, podcastów, kursów</div>
        </div>
        <div class="av-single-counter">
            <div class="av-counter-value">7</div>
            <div class="av-counter-title">Dni istnienia</div>
        </div>
    </div>
</div>
