<?php

require_once plugin_dir_path(__FILE__) . '../AVApi.php';


wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/styles.css'));
wp_enqueue_script( 'av-search', plugins_url( 'aspirate-viewer/templates/scripts/search.js' ), array(), NULL, true);


function replaceAccents($str)
{
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
  return str_replace($a, $b, $str);
}

?>

<div class="search-bar">
    <input type="text" name="search-input" id="av-search-input" placeholder="Wpisz dowolne słowo, aby wyszukać ...">
    <div id="counter">
        <p id="counter-displayed" class="av-displayed" hidden>Znalezionych: </p>
        <p id="counter-all">Wszystkich pozycji: </p>
    </div>
</div>

<span id="check">
    <?php
        $liders = AVApi::getResults('lv_liders', 'is_top DESC, first_name');
        $cooperationElement = "
            <div class='av-tooltip'>
                <img src=" . plugins_url('aspirate-viewer/templates/assets/icons/aspirate-zweryfikowany-lider-marketingu.svg') . " alt='cooperation'>
                <span class='av-tooltip-text av-tooltip-top'>Zweryfikowany</span>
            </div>
        ";

        foreach ($liders as $lider ) {

            $settings = json_decode($lider->settings);

            if($lider->visibility == "1") {

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

            $nameSlug = strtolower(replaceAccents($lider->first_name . "-" . $lider->last_name));
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
        }
    ?>
    <div class="av-no-results">
        <p>Brak wyników</p>
    </div>
</span>
