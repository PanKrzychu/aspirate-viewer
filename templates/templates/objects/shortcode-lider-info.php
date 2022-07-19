<?php

require_once plugin_dir_path(__FILE__) . '../../../AVApi.php';

wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Catamaran&family=Source+Sans+Pro:wght@700;900&display=swap', false );
wp_enqueue_style('av-styles', plugins_url('aspirate-viewer/templates/styles/viewer-styles.css'));
wp_enqueue_script( 'av-search', plugins_url( 'aspirate-viewer/templates/scripts/search.js' ), array(), NULL, true);

$uri = $_SERVER['REQUEST_URI'];
if(substr($uri, -1) == "/") $uri = substr($uri, 0, -1);
$slug = end(explode("/", $uri));    

$lider = AVApi::getResults('av_liders', "slug = '$slug'", "first_name")[0];
$products = AVApi::getLiderProducts($lider->id);

$allProducts = [];

foreach ($products->books['objects'] as $book) {
    $categories = explode(",", $book->categories);
            $categoriesElements = '';
            foreach ($categories as $category) {
                if(strlen($category) > 1) {
                    $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
                }
            }

    array_push($allProducts, [
        'name' => $book->name,
        'type' => "<span class='av-info-badge av-badge'> KSIĄŻKA </span>",
        'categories' => $categoriesElements,
        'coverPath' => 'aspirate-viewer/templates/assets/photos/books/' . $book->cover,
        'link' => 'https://aspirate.pl/ksiazki-o-marketingu/' . $book->slug
    ]);
}

foreach ($products->courses['objects'] as $course) {
    $categories = explode(",", $course->categories);
            $categoriesElements = '';
            foreach ($categories as $category) {
                if(strlen($category) > 1) {
                    $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
                }
            }

    array_push($allProducts, [
        'name' => $course->name,
        'type' => "<span class='av-info-badge av-badge'> KURS </span>",
        'categories' => $categoriesElements,
        'coverPath' => 'aspirate-viewer/templates/assets/photos/courses/' . $course->cover,
        'link' => 'https://aspirate.pl/kursy-marketingowe-i-biznesowe/' . $course->slug
    ]);
}

foreach ($products->podcasts['objects'] as $podcast) {
    $categories = explode(",", $podcast->categories);
            $categoriesElements = '';
            foreach ($categories as $category) {
                if(strlen($category) > 1) {
                    $categoriesElements .= "<span class='av-category-badge av-badge'> $category </span>";
                }
            }

    array_push($allProducts, [
        'name' => $podcast->name,
        'type' => "<span class='av-info-badge av-badge'> PODCAST </span>",
        'categories' => $categoriesElements,
        'coverPath' => 'aspirate-viewer/templates/assets/photos/podcasts/' . $podcast->cover,
        'link' => 'https://aspirate.pl/podcasty-o-marketingu/' . $podcast->slug
    ]);
}
?>

<div class="av-lider-products-container">

<?php

if(count($allProducts) > 0) {

    echo "
        <span class='av-lider-products-header'>Dostępne produkty</span>
    ";

    foreach ($allProducts as $product ) {
        echo "
            <div class='av-item-container'>
                <div class='av-item-content'>
                    <div class='av-left-column'>
                    <a href=' " . $product['link'] . " ' class='av-show-more-link'>
                        <img src=" . plugins_url($product['coverPath']) . ">
                    </a>
                    </div>
                    <div class='av-right-column'>
                        <div class='av-name-row av-name-row-big av-space-10'>
                        <a href=' " . $product['link'] . " ' class='av-show-more-link'>
                            <h2>". $product['name'] ."</h2>
                        </a>
                        </div>
                        <div class='av-categories av-space-40'>" . $product['categories'] . $product['type'] . "</div>
                        <a href=' " . $product['link'] . " ' class='av-show-more-link'>Dowiedz się więcej...</a>
                    </div>
                </div>
            </div>
        ";
    }

}



?>

</div>