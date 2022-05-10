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
        $podcasts = AVApi::getPodcasts();

        foreach ($podcasts as $podcast ) {

            if($podcast->visibility == "1") {

                $links = json_decode($podcast->links);

                $podcast->top == 1 ? $isTop = 'av-top' : $isTop = '';

                $photoAlt = "$podcast->authors_id - Podcast: $podcast->name";
                if($podcast->company != "") {
                    $photoAlt = $photoAlt . " - " . $podcast->company;
                }
                $title = "$podcast->authors_id: ";
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

                $authors = "<p class='av-authors'>$podcast->authors_id</p>";

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
                            $authors
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
        }
        
    ?>
    <div class="av-no-results">
        <p>Brak wyników</p>
    </div>
</span>
