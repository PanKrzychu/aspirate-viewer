<?php

require_once plugin_dir_path(__FILE__) . '../AVApi.php';


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

            $links = json_decode($podcast->links);

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
                    $categoriesElements .= "<span class='av-category'> $category </span>";
                    $title = $title . "$category, ";
                }
            }
            $title = substr($title,0,-2);

            //socials
            $sitePriv = '';
            if($links->page != "") {
                $sitePriv = "<a class='av-social-icon' href=" . $links->page . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-www.svg')) . "</a>";
            }
            $linkApple = '';
            if($links->apple != "") {
                $linkApple = "<a class='av-social-icon' href=" . $links->apple . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/apple-podcast.svg')) . "</a>";
            }
            $linkGoogle = '';
            if($links->google != "") {
                $linkGoogle = "<a class='av-social-icon' href=" . $links->google . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/google-podcast.svg')) . "</a>";
            }
            $linkSpotify = '';
            if($links->spotify != "") {
                $linkSpotify = "<a class='av-social-icon' href=" . $links->spotify . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/spotify-podcast.svg')) . "</a>";
            }

            $socialsElements = $sitePriv . $linkApple . $linkGoogle . $linkSpotify;

            $photoName = $podcast->cover;
            

            echo "
            <div class='av-item-container' search-text=`$podcast->search_text`>
                <div class='av-item-content $isTop'>
                    <div class='av-left-column'>
                        <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/podcasts/' . $photoName) . " alt='$photoAlt' title='$title' >
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row'>
                            <h2>$podcast->name</h2>
                        </div>
                        $authorsElement
                        <div class='av-categories'>
                            $categoriesElements
                        </div>
                        <p class='av-description'>$podcast->description</p>
                        <div class='av-socials'>
                            $socialsElements
                        </div>
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
