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
        $courses = AVApi::getResults('av_courses');

        foreach ($courses as $course ) {

            $course->is_top == 1 ? $isTop = 'av-top' : $isTop = '';

            $authorsText = AVApi::getLidersText($course->authors_id, $course->authors_other);
            $authorsElement = "<p class='av-authors'>$authorsText</p>";

            $photoAlt = "$authorsText - course: $course->name";
            if($course->company != "") {
                $photoAlt = $photoAlt . " - " . $course->company;
            }
            $title = "$authorsText: ";
            $categories = explode(",", $course->categories);
            $categoriesElements = '';
            foreach ($categories as $category) {
                if(strlen($category) > 1) {
                    $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
                    $title = $title . "$category, ";
                }
            }
            $title = substr($title,0,-2);

            $infoElements = '';
            $infoList = AVApi::getCourseBadges($course);
            foreach ($infoList as $info) {
                $infoElements .= "<span class='av-info-badge av-badge'> $info </span>";
            }

            //socials
            $sitePriv = '';
            if($course->site != "") {
                $sitePriv = "<a class='av-social-icon' href=" . $course->site . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-www.svg')) . "</a>";
            }

            $course->cover != "" ? $photoName = $course->cover : $photoName = "photo-placeholder.png";        
            

            echo "
            <div class='av-item-container' search-text=`$course->search_text`>
                <div class='av-item-content $isTop'>
                    <div class='av-left-column'>
                        <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/courses/' . $photoName) . " alt='$photoAlt' title='$title' >
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row av-name-row-big'>
                            <h2>$course->name</h2>
                        </div>
                        $authorsElement
                        <div class='av-categories'>
                            $categoriesElements
                            $infoElements
                        </div>
                        <p class='av-description'>$course->description</p>
                        <div class='av-socials'>
                            $sitePriv
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
