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
        $books = AVApi::getResults('av_books');

        foreach ($books as $book ) {

            $photoAlt = "$book->authors_id - Book: $book->name";
            $book->top == 1 ? $isTop = 'av-top' : $isTop = '';
            
            $title = "$book->authors_id: ";
            $categories = explode(",", $book->categories);
            $categoriesElements = '';
            foreach ($categories as $category) {
                if(strlen($category) > 1) {
                    $categoriesElements .= "<span class='av-category'> $category </span>";
                    $title = $title . "$category, ";
                }
            }
            $title = substr($title,0,-2);

            $authorsText = AVApi::getLidersText($book->authors_id, $book->authors_other);

            $authorsElement = "<p class='av-authors'>$authorsText</p>";

            $photoName = $book->cover;
            

            echo "
            <div class='av-item-container' search-text=`$book->search_text`>
                <div class='av-item-content $isTop'>
                    <div class='av-left-column'>
                        <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/books/' . $photoName) . " alt='$photoAlt' title='$title' >
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row'>
                            <h2>$book->name</h2>
                        </div>
                        $authorsElement
                        <div class='av-categories'>
                            $categoriesElements
                        </div>
                        <p class='av-description'>$book->description</p>
                        <!-- <div class='av-socials'>
                            $socialsElements
                        </div> -->
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
