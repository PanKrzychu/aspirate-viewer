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
    <div id="av-dictionary-anchors">
        <!-- <a href="#av-dictionary-letter-anchor-a">A</a> -->

        <?php 

            $lettersArray = AVApi::getDictionaryLetters();
            foreach ($lettersArray as $letter) {
                $href = strtolower('#av-dictionary-letter-anchor-' . $letter->letter);
                if($letter->is_present == 1) {
                    echo "<a class='av-dictionary-anchor-link' href='$href'>$letter->letter</a>";
                } else {
                    echo "<span class='av-dictionary-anchor-link av-dictionary-anchor-link-inactive'>$letter->letter</span>";
                }
            }

        ?>
    </div>
</div>

<span id="check">
    <?php
        $results = AVApi::getResults('av_dictionary', "id > 0", "phrase");

        $currentLetter = "";

        foreach ($results as $row ) {    

            if($row->phrase[0] != $currentLetter) {
                $currentLetter = $row->phrase[0];
                $currentId = strtolower('av-dictionary-letter-anchor-' . $currentLetter);
                echo "<span class='av-dictionary-letter-anchor' id='$currentId'>$currentLetter</span>";
            }

            echo "
            <div class='av-item-container' search-text=`$row->search_text`>
                <div class='av-item-content'>
                    <div class='av-right-column'>
                        <div class='av-name-row av-name-row-small'>
                            <h2>$row->phrase</h2>
                        </div>
                        <p class='av-description'>$row->description</p>
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