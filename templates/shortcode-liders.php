<?php

require_once plugin_dir_path(__FILE__) . '../AVApi.php';


wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/styles.css'));
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
        $liders = AVApi::getResults('av_liders', 'is_top DESC, first_name');
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
            $categories = explode(",", $lider->categories);
            $categoriesElements = '';
            foreach ($categories as $category ) {
                $categoriesElements .= "<span class='av-category'> $category </span>";
                $title = $title . "$category, ";
            }
            $title = substr($title,0,-2);

            //socials
            $sitePriv = '';
            if($socials->sites->private != "") {
                $sitePriv = "<a class='av-social-icon' href=http://" . $socials->sites->private . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-www.svg')) . "</a>";
            }
            $linkFB = '';
            if($socials->facebook->address != "") {
                $linkFB = "<a class='av-social-icon' href=" . $socials->facebook->address . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-fb.svg')) . "</a>";
            }
            $linkIG = '';
            if($socials->instagram->address != "") {
                $linkIG = "<a class='av-social-icon' href=" . $socials->instagram->address . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-ig.svg')) . "</a>";
            }
            $linkTik = '';
            if($socials->tiktok->address != "") {
                $linkTik = "<a class='av-social-icon' href=" . $socials->tiktok->address . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-tik.svg')) . "</a>";
            }
            $linkTT = '';
            if($socials->twitter->address != "") {
                $linkTT = "<a class='av-social-icon' href=" . $socials->twitter->address . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-tt.svg')) . "</a>";
            }
            $linkYT = '';
            if($socials->youtube->address != "") {
                $linkYT = "<a class='av-social-icon' href=" . $socials->youtube->address . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-yt.svg')) . "</a>";
            }

            $linkLink = '';
            if($socials->linkedin->address != "") {
                $linkLink = "<a class='av-social-icon' href=" . $socials->linkedin->address . " target='_blank' >" . file_get_contents(plugins_url('aspirate-viewer/templates/assets/icons/logo-link.svg')) . "</a>";
            }

            $company = "<p class='av-company'>$lider->company</p>";
            if($socials->sites->company != "") {
                $company = "<a href=http://" . $socials->sites->company . " target='_blank' ><p class='av-company'>$lider->company</p></a>";
            }

            $socialsElements = $sitePriv . $linkFB . $linkIG . $linkTik . $linkTT . $linkYT . $linkLink;

            $nameSlug = strtolower(AVApi::replaceAccents($lider->first_name . "-" . $lider->last_name));
            $photoName = "aspirate-blog-marketingowy-liderzy-marketingu-$nameSlug.jpg";
            

            echo "
            <div class='av-item-container' search-text=`$lider->search_text`>
                <div class='av-item-content $isTop'>
                    <div class='av-left-column'>
                        <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/liders/' . $photoName) . " alt='$photoAlt' title='$title' >
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row'>
                            <h2>$lider->first_name $lider->last_name</h2>
                            $isCooperation
                        </div>
                        $company
                        <div class='av-categories'>
                            $categoriesElements
                        </div>
                        <p class='av-description'>$lider->description</p>
                        <div class='av-socials'>
                            $socialsElements
                        </div>
                    </div>
                </div>
            </div>";
        }
    ?>
    <div class="av-no-results">
        <p>Brak wyników</p>
    </div>
</span>
