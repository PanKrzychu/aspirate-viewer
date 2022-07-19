<?php

require_once plugin_dir_path(__FILE__) . '../../../AVApi.php';


wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/viewer-styles.css'));

    $uri = $_SERVER['REQUEST_URI'];
    if(substr($uri, -1) == "/") $uri = substr($uri, 0, -1);
    $uriParts = explode("/", $uri);
    $slug = end($uriParts);    
    $productCategory = $uriParts[count($uriParts) - 2];

    $productCategories = [
        'ksiazki-o-marketingu' => ['av_books', 'book'],
        'podcasty-o-marketingu' => ['av_podcasts', 'podcast'],
        'kursy-marketingowe-i-biznesowe' => ['av_courses', 'course'],
    ];


    $product = AVApi::getResults($productCategories[$productCategory][0], "slug = '$slug'", "name")[0];

    $authorsText = AVApi::getLidersText($product->authors_id, $product->authors_other);
    $authorsElement = "<p class='av-authors'>$authorsText</p>";

    $categories = explode(",", $product->categories);
    $categoriesElements = '';
    foreach ($categories as $category) {
        if(strlen($category) > 1) {
            $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
        }
    }

    $infoElements = '';
    if($productCategories[$productCategory][1] == 'book') {
        $infoList = AVApi::getBookBadges($product);
        foreach ($infoList as $info) {
            $infoElements .= "<span class='av-info-badge av-badge'> $info </span>";
        }
    } else if($productCategories[$productCategory][1] == 'course') {
        $infoList = AVApi::getCourseBadges($product);
        foreach ($infoList as $info) {
            $infoElements .= "<span class='av-info-badge av-badge'> $info </span>";
        }
    }

    if($product->description_long != "") {
        $desc = $product->description_long;
    } else {
        $desc = $product->description;
    }

    //rodzaj produktu w liczbie mnogiej, np. book + s => folder w plikach
    $photoURL = 'aspirate-viewer/templates/assets/photos/' . $productCategories[$productCategory][1] . 's/' . $product->cover;
    
    echo "
    <div class='av-item-container' >
        <div class='av-item-content'>
            <div class='av-left-column'>
                <img src=" . plugins_url($photoURL) . " >
            </div>
            <div class='av-right-column'>
                <div class='av-name-row av-name-row-big'>
                    <h2>$product->name</h2>
                </div>
                $authorsElement
                <div class='av-categories'>
                    $categoriesElements
                    $infoElements
                </div>
                <p class='av-description'>$desc</p>
            </div>
        </div>
    </div>
    ";
    
?>
