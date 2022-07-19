<?php

require_once plugin_dir_path(__FILE__) . '../../../AVApi.php';


wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/viewer-styles.css'));
wp_enqueue_script( 'av-search', plugins_url( 'aspirate-viewer/templates/scripts/search.js' ), array(), NULL, true);


?>

<div class="search-bar">
    <input type="text" name="search-input" id="av-search-input" placeholder="Wpisz dowolne słowo, aby wyszukać ...">
    <div id="av-counter">
        <p id="av-counter-displayed" class="av-displayed" hidden>Znalezionych: </p>
        <p id="av-counter-all">Wszystkich pozycji: </p>
    </div>
</div>

<span id="check">
    <?php
        $podcasts = AVApi::getResults('av_podcasts');

        foreach ($podcasts as $podcast ) {

            $authorsText = AVApi::getLidersText($podcast->authors_id, $podcast->authors_other);
            $authorsElement = "<p class='av-authors'>$authorsText</p>";

            $podcast->top == 1 ? $isTop = 'av-top' : $isTop = '';

            $photoAlt = "$authorsText - Podcast: $podcast->name";
            if($podcast->company != "") {
                $photoAlt = $photoAlt . " - " . $podcast->company;
            }
            $title = "$authorsText: ";
            $categories = explode(",", $podcast->categories);
            $categoriesElements = '';
            foreach ($categories as $category) {
                if(strlen($category) > 1) {
                    $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
                    $title = $title . "$category, ";
                }
            }
            $title = substr($title,0,-2);

            $photoName = $podcast->cover;
            

            echo "
            <div class='av-item-container' search-text=`$podcast->search_text`>
                <div class='av-item-content $isTop'>
                    <div class='av-left-column'>
                        <a href=' " . $_SERVER['REQUEST_URI'] . $podcast->slug . " ' class='av-show-more-link'>
                            <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/podcasts/' . $photoName) . " alt='$photoAlt' title='$title' >
                        </a>
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row av-name-row-big'>
                            <a href=' " . $_SERVER['REQUEST_URI'] . $podcast->slug . " ' class='av-show-more-link'><h2>$podcast->name</h2></a>
                        </div>
                        $authorsElement
                        <div class='av-categories'>
                            $categoriesElements
                        </div>
                        <p class='av-description'>$podcast->description</p>
                        <a href=' " . $_SERVER['REQUEST_URI'] . $podcast->slug . " ' class='av-show-more-link'>Dowiedz się więcej...</a>
                    </div>
                </div>
            </div>
            ";
        }
        
    ?>
    <div class="av-no-results">
        <p>Brak wyników</p>
    </div>
</span>
