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

        $liders = [];
        if($args['is_foreign']) {
            $liders = AVApi::getResults('av_liders', "is_visible = 1 and country_group = 'zagraniczny'", 'is_top DESC, first_name');
        } else {
            $liders = AVApi::getResults('av_liders', "is_visible = 1 and country_group = 'polski'",  'is_top DESC, first_name');
        }

        $cooperationElement = "
            <div class='av-tooltip'>
                <img src=" . plugins_url('aspirate-viewer/templates/assets/icons/aspirate-zweryfikowany-lider-marketingu.svg') . " alt='cooperation'>
                <span class='av-tooltip-text av-tooltip-top'>Zweryfikowany</span>
            </div>
        ";

        foreach ($liders as $lider ) {

            $settings = json_decode($lider->settings);
            $socials = json_decode($lider->socials);

            $settings->cooperation == "1" ? $isCooperation = $cooperationElement : $isCooperation = '';
            $lider->is_top == "1" ? $isTop = 'av-top' : $isTop = '';

            $photoAlt = "$lider->first_name $lider->last_name";
            if($lider->company != "") {
                $photoAlt = $photoAlt . " - " . $lider->company;
            }
            $title = "$lider->first_name $lider->last_name: ";
            $categoriesElements = '';
            if($lider->categories != "") {
                $categories = explode(",", $lider->categories);
                foreach ($categories as $category ) {
                    $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
                    $title = $title . "$category, ";
                }
            }
            
            $title = substr($title,0,-2);

            $company = "<p class='av-company'>$lider->company</p>";
            if($socials->sites->company != "") {
                $company = "<a href=http://" . $socials->sites->company . " target='_blank' ><p class='av-company'>$lider->company</p></a>";
            }


            $nameSlug = preg_replace('/\s+/', '', strtolower(AVApi::replaceAccents($lider->first_name . "-" . $lider->last_name)));
            $photoName = "aspirate-blog-marketingowy-liderzy-marketingu-$nameSlug.jpg";            

            echo "
            <div class='av-item-container' search-text=`$lider->search_text`>
                <div class='av-item-content $isTop'>
                    <div class='av-left-column'>
                        <a href=' " . $_SERVER['REQUEST_URI'] . $lider->slug . " ' class='av-show-more-link'>
                            <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/liders/' . $photoName) . " alt='$photoAlt' title='$title' >
                        </a>
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row av-name-row-big'>
                            <a href=' " . $_SERVER['REQUEST_URI'] . $lider->slug . " ' class='av-show-more-link'><h2>$lider->first_name $lider->last_name</h2></a>
                            
                            $isCooperation
                        </div>
                        $company
                        <div class='av-categories'>
                            $categoriesElements
                        </div>
                        <p class='av-description'>$lider->description</p>
                        <a href=' " . $_SERVER['REQUEST_URI'] . $lider->slug . " ' class='av-show-more-link'>Dowiedz się więcej...</a>
                    </div>
                </div>
            </div>";
        }
    ?>
    <div class="av-no-results">
        <p>Brak wyników</p>
    </div>
</span>
