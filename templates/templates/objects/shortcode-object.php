<?php

require_once plugin_dir_path(__FILE__) . '../../../AVApi.php';

wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/viewer-styles.css'));  
wp_enqueue_style('av-blue-container-styles', plugins_url('aspirate-viewer/templates/styles/lider-page-blue-container-fixes.css'));

$uri = $_SERVER['REQUEST_URI'];
if(substr($uri, -1) == "/") $uri = substr($uri, 0, -1);
$uriParts = explode("/", $uri);
$slug = end($uriParts);    
$productCategory = $uriParts[count($uriParts) - 2];

$productCategories = [
    'zagraniczni-liderzy-marketingu' => ['av_liders', 'lider'],
    'liderzy-marketingu' => ['av_liders', 'lider'],
    'ksiazki-o-marketingu' => ['av_books', 'book'],
    'podcasty-o-marketingu' => ['av_podcasts', 'podcast'],
    'kursy-marketingowe-i-biznesowe' => ['av_courses', 'course'],
];


$product = AVApi::getResults($productCategories[$productCategory][0], "slug = '$slug'", "id")[0];

    $product->type == "lider" ? $isLider = true : $isLider = false;

    $photoSrc = plugins_url('aspirate-viewer/templates/assets/photos/' . $product->type . 's/' . $product->cover);

    if($isLider) {
        $title = "$product->first_name $product->last_name";
        $settings = json_decode($product->settings);
        $socials = json_decode($product->socials);
        $settings->cooperation == "1" ? $isCooperation = $cooperationElement : $isCooperation = '';
    
        $subtitle = "<p class='av-company av-company-blue'>$product->company</p>";
        if($socials->sites->company != "") {
            $subtitle = "<a href=http://" . $socials->sites->company . " target='_blank' ><p class='av-company av-company-blue'>$product->company</p></a>";
        }

        $photoAlt = "$product->first_name $product->last_name";
        if($product->company != "") {
            $photoAlt = $photoAlt . " - " . $product->company;
        }
    } else {
        $title = $product->name;
        $subtitle = "<p class='av-company av-company-blue'>" . AVApi::getLidersText($product->authors_id, $product->authors_other) . "</p>";
        $photoAlt = "$product->name - " . AVApi::getLidersText($product->authors_id, $product->authors_other);
    }
    $photoTitle = "$photoAlt: ";

    $categoriesElements = '';
    if($product->categories != "") {
        $categories = explode(",", $product->categories);
        foreach ($categories as $category ) {
            $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
            $photoTitle = $photoTitle . "$category, ";
        }
    }
    $photoTitle = substr($photoTitle,0,-2);

    //badge
    if($isLider) {
        $products = AVApi::getLiderProducts($product->id);
        if($products->books['quantity'] > 0) $categoriesElements .= "<span class='av-product-quantity-badge av-badge'> książki: " . $products->books['quantity'] . " </span>";
        if($products->courses['quantity'] > 0) $categoriesElements .= "<span class='av-product-quantity-badge av-badge'> kursy: " . $products->courses['quantity'] . " </span>";
        if($products->podcasts['quantity'] > 0) $categoriesElements .= "<span class='av-product-quantity-badge av-badge'> podcasty: " . $products->podcasts['quantity'] . " </span>";
    } elseif ($product->type == "book") {
        $infoElements = '';
        $infoList = AVApi::getBookBadges($product);
        foreach ($infoList as $info) {
            $infoElements .= "<span class='av-product-quantity-badge av-badge'> $info </span>";
        }
        $categoriesElements .= $infoElements;
    } elseif ($product->type == "course") {
        $infoElements = '';
        $infoList = AVApi::getCourseBadges($product);
        foreach ($infoList as $info) {
            $infoElements .= "<span class='av-product-quantity-badge av-badge'> $info </span>";
        }
        $categoriesElements .= $infoElements;
    }
    
    $socialsElements = AVApi::getSocialIcons($product);


    if($product->description_long != "") {
        $desc = $product->description_long;
    } else {
        $desc = $product->description;
    }

    echo "
    <div class='av-item-container-blue'>
        <div class='av-item-content-blue'>
            <div class='av-left-column'>
                <img src='$photoSrc' alt='$photoAlt' title='$photoTitle'>
            </div>
            <div class='av-right-column'>
                <div class='av-name-row-blue av-name-row-big'>
                    <h2>$title</h2>
                </div>
                $subtitle
                <div class='av-categories'>
                    $categoriesElements
                </div>
                <p class='av-description av-description-blue'>$desc</p>
                <div class='av-socials av-socials-blue'>
                    $socialsElements
                </div>
            </div>
        </div>
    </div>";

?>
