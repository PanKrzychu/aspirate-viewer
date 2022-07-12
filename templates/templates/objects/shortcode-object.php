<?php

require_once plugin_dir_path(__FILE__) . '../AVApi.php';

wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/viewer-styles.css'));  
wp_enqueue_style('av-blue-container-styles', plugins_url('aspirate-viewer/templates/styles/lider-page-blue-container-fixes.css'));  
wp_enqueue_script( 'av-search', plugins_url( 'aspirate-viewer/templates/scripts/search.js' ), array(), NULL, true);

$uri = $_SERVER['REQUEST_URI'];
if(substr($uri, -1) == "/") $uri = substr($uri, 0, -1);
$slug = end(explode("/", $uri));    

$lider = AVApi::getResults('av_liders', "slug = '$slug'", "first_name")[0];

    $settings = json_decode($lider->settings);

    $socials = json_decode($lider->socials);
    $settings->cooperation == "1" ? $isCooperation = $cooperationElement : $isCooperation = '';

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

    $products = AVApi::getLiderProducts($lider->id);
    if($products->books['quantity'] > 0) $categoriesElements .= "<span class='av-product-quantity-badge av-badge'> książki: " . $products->books['quantity'] . " </span>";
    if($products->courses['quantity'] > 0) $categoriesElements .= "<span class='av-product-quantity-badge av-badge'> kursy: " . $products->courses['quantity'] . " </span>";
    if($products->podcasts['quantity'] > 0) $categoriesElements .= "<span class='av-product-quantity-badge av-badge'> podcasty: " . $products->podcasts['quantity'] . " </span>";
    
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

    $company = "<p class='av-company av-company-blue'>$lider->company</p>";
    if($socials->sites->company != "") {
        $company = "<a href=http://" . $socials->sites->company . " target='_blank' ><p class='av-company-blue'>$lider->company</p></a>";
    }

    $socialsElements = $sitePriv . $linkFB . $linkIG . $linkTik . $linkTT . $linkYT . $linkLink;

    $nameSlug = preg_replace('/\s+/', '', strtolower(AVApi::replaceAccents($lider->first_name . "-" . $lider->last_name)));
    $photoName = "aspirate-blog-marketingowy-liderzy-marketingu-$nameSlug.jpg";

    if($lider->description_long != "") {
        $desc = $lider->description_long;
    } else {
        $desc = $lider->description;
    }

    echo "
    <div class='av-item-container-blue'>
        <div class='av-item-content-blue'>
            <div class='av-left-column'>
                <img src=" . plugins_url('aspirate-viewer/templates/assets/photos/liders/' . $photoName) . " alt='$photoAlt' title='$title' >
            </div>
            <div class='av-right-column'>
                <div class='av-name-row-blue av-name-row-big'>
                    <h2>$lider->first_name $lider->last_name</h2>
                </div>
                $company
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
