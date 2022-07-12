<?php

/**
 * @package  Aspirate Viewer
 */

class AVApi
{
    
    public static function registerRoutes() {

        register_rest_route('AVApi/v1', '/generate-search-text', array(
            'methods' => 'GET',
            'callback' => function() {
                generateLidersSearchText();
                generatePodcastsSearchText();
                generateBooksSearchText();
                generateCoursesSearchText();
                generateDictionarySearchText();
                exit();
            }
        ));

        register_rest_route('AVApi/v1', '/set-slug', array(
            'methods' => 'GET',
            'callback' => function() {
                global $wpdb;
                
                $table_name = $wpdb->prefix . 'av_liders';

                $query = "SELECT * FROM $table_name";

                $liders = $wpdb->get_results($query);

                foreach ($liders as $lider) {
                    $slug = $lider->first_name . "-" . $lider->last_name . "-" . $lider->company;
                    $slug = AVApi::replaceAccents($slug);    
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
                    if(substr($slug, -1) == "-") $slug = substr($slug, 0, -1);
                    $wpdb->update(
                        $table_name,
                        array(
                            'slug' => $slug
                        ),
                        array(
                            'id' => $lider->id
                        )
                    );
                    echo "Slug dla lidera o id: " . $lider->id . " został zaktualizowany: " . $slug ." \n\n";
                }

                exit();

            }
        ));

        register_rest_route('AVApi/v1', '/count-products', array(
            'methods' => 'GET',
            'callback' => function() {
                
                $books = AVApi::getResults('av_books', "is_visible = 1",  'name');
                $podcasts = AVApi::getResults('av_podcasts', "is_visible = 1",  'name');
                $courses = AVApi::getResults('av_courses', "is_visible = 1",  'name');

                $products = array_merge($books, $podcasts, $courses);

                echo "jest " . count($products) . " a osobno: " . count($books) . ", " . count($podcasts) . ", " . count($courses);
                exit();

            }
        ));

        register_rest_route('AVApi/v1', '/copy-pages', array(
            'methods' => 'GET',
            'callback' => function() {

                $books = AVApi::getResults('av_books', "is_visible = 1",  'name');
                $podcasts = AVApi::getResults('av_podcasts', "is_visible = 1",  'name');
                $courses = AVApi::getResults('av_courses', "is_visible = 1",  'name');
                $liders = AVApi::getResults('av_liders', "is_visible = 1",  'first_name');
                $products = array_merge($books, $podcasts, $courses, $liders);

                $existingPages = AVApi::getResults('av_pages', 'id > 0', 'id');

                $pageId = 9990;

                //lecimy po każdym objekcie w bazie
                foreach ($products as $product) {

                    //filter zwraca do tablicy dane jeżeli objekt już ma podstronę
                    $copyArrays = array_filter($existingPages, function($obj) use($product) {
                        if($obj->type == $product->type && $obj->object_id == $product->id) {
                            return true;
                        } else {
                            return false;
                        }
                    });

                    //jeżeli tablica jest pusta to dodajemy stronę
                    if(count($copyArrays) == 0) {

                        //rozbicie ze względu na różnice między liderami a resztą
                        if($product->type == "lider") {
                            $postTitle = $product->first_name . " " . $product->last_name;
                        } else {
                            $postTitle = $product->name;
                        }
                        
                        $post = (array) get_post( $pageId ); // Post to duplicate.
                        unset($post['ID']); // Remove id, wp will create new post if not set.
                        $post['post_title'] = $postTitle;
                        $post['post_name'] = $product->slug;
                        $post['post_status'] = 'publish';
                        $post['post_content'] = '[object-info][' . $product->type . '-info][elementor-template id="1141"]';
                        $post['post_parent'] = getParentPostId($product);
                        $new_post_id = wp_insert_post($post);
    
                        /*
                        * duplicate all post meta just in two SQL queries
                        */
                        global $wpdb;
                        $sql_query = "";
                        $sql_query_sel = [];
                        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                        if (count($post_meta_infos)!=0) {
                            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                            foreach ($post_meta_infos as $meta_info) {
                                $meta_key = $meta_info->meta_key;
                                $meta_value = $meta_info->meta_value;
                                if( $meta_key == '_wp_old_slug' ) continue;
                                if( $meta_key == '_yoast_wpseo_focuskw' ) {
                                    $meta_value = $postTitle;
                                } elseif( $meta_key == '_yoast_wpseo_title' ) {
                                    $meta_value = $product->seo_title;
                                } elseif( $meta_key == '_yoast_wpseo_metadesc' ) {
                                    $meta_value = $product->seo_description;
                                } elseif( $meta_key == '_yoast_wpseo_opengraph-image' ) {
                                    $meta_value = plugins_url("aspirate-viewer/templates/assets/og-images/" . $product->type . "s/" . $product->og_image);
                                }
                                $meta_value = addslashes($meta_value);
                                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                            }
                            $sql_query.= implode(" UNION ALL ", $sql_query_sel);
    
                            $sql_query = str_replace($post_id, $new_post_id, $sql_query);
    
                            $wpdb->query($sql_query);
                        }
    
                        //dodanie do bazy info o udostępnieniu strony
                        $table_name = $wpdb->prefix . 'av_pages';
    
                        $wpdb->insert(
                            $table_name,
                            array(
                                'page_id' => $new_post_id,
                                'object_id' => $product->id,
                                'type' => $product->type,
                            )
                        );
                        echo "Strona <b>$postTitle</b> została dodana. \n\n";
                        sleep(1);
                    }

                }
                
                exit();
            }
        ));

        register_rest_route('AVApi/v1', '/copy-pages-test', array(
            'methods' => 'GET',
            'callback' => function() {
                // $books = AVApi::getResults('av_books', "is_visible = 1",  'name');
                // $podcasts = AVApi::getResults('av_podcasts', "is_visible = 1",  'name');
                // $courses = AVApi::getResults('av_courses', "is_visible = 1",  'name');
                $liders = AVApi::getResults('av_liders', "is_visible = 1",  'first_name');
                // $products = array_merge($books, $podcasts, $courses, $liders);

                // $existingPages = AVApi::getResults('av_pages', 'id > 0', 'id');

                // $postId = 0;
                // //lecimy po każdym objekcie w bazie
                // foreach ($products as $product) {

                //     //filter zwraca do tablicy dane jeżeli objekt już ma podstronę
                //     $copyArrays = array_filter($existingPages, function($obj) use($product) {
                //         // echo $obj->type . " : " . $product->type . " | " . $obj->object_id . " : " . $product->id . "\n\n";
                //         if($obj->type == $product->type && $obj->object_id == $product->id) {
                //             return true;
                //         } else {
                //             return false;
                //         }
                //     });

                //     // jeżeli tablica jest pusta to dodajemy stronę
                //     if(count($copyArrays) == 0) {
                //         echo  " elo ";
                //     }
                // }


                
                exit();
            }
        ));


        register_rest_route('AVApi/v1', '/liders-generate-search-text', array(
            'methods' => 'GET',
            'callback' => function() {
                generateLidersSearchText();
                exit();
            }
        ));

        register_rest_route('AVApi/v1', '/podcasts-generate-search-text', array(
            'methods' => 'GET',
            'callback' => function() {
                generatePodcastsSearchText();
                exit();
            }

        ));

        register_rest_route('AVApi/v1', '/books-generate-search-text', array(
            'methods' => 'GET',
            'callback' => function() {
                generateBooksSearchText();
                exit();
            }

        ));

        register_rest_route('AVApi/v1', '/courses-generate-search-text', array(
            'methods' => 'GET',
            'callback' => function() {
                generateCoursesSearchText();
                exit();
            }

        ));

        register_rest_route('AVApi/v1', '/dictionary-generate-search-text', array(
            'methods' => 'GET',
            'callback' => function() {
                generateDictionarySearchText();
                exit();
            }

        ));

        function generateLidersSearchText() {
            global $wpdb;
                    
            $table_name = $wpdb->prefix . 'av_liders';
    
            $query = "SELECT * FROM $table_name";
    
            $liders = $wpdb->get_results($query);
    
            foreach ($liders as $lider) {
                $text = $lider->first_name . $lider->last_name . $lider->company . $lider->locality . $lider->categories . $lider->tags . $lider->slogan . $lider->description;
                $text = prepareText($text);
                
                $wpdb->update(
                    $table_name,
                    array(
                        'search_text' => $text
                    ),
                    array(
                        'id' => $lider->id
                    )
                );
                echo "Search_text dla lidera o id: " . $lider->id . " został wygenerowany: \n" . $text . "\n\n";
            }
    
            return true;
        }

        function generatePodcastsSearchText() {
            global $wpdb;
                
            $table_name = $wpdb->prefix . 'av_podcasts';

            $query = "SELECT * FROM $table_name";

            $podcasts = $wpdb->get_results($query);

            foreach ($podcasts as $podcast) {
                $text = $podcast->name . $podcast->tags . $podcast->description . AVApi::getLidersText($podcast->authors_id, $podcast->authors_other);
                $text = prepareText($text);
                
                $wpdb->update(
                    $table_name,
                    array(
                        'search_text' => $text
                    ),
                    array(
                        'id' => $podcast->id
                    )
                );
                echo "Search_text dla podcastu o id: " . $podcast->id . " został wygenerowany: \n" . $text . "\n\n";
            }

            return true;
        }

        function generateBooksSearchText() {
            global $wpdb;
            
            $table_name = $wpdb->prefix . 'av_books';

            $query = "SELECT * FROM $table_name";

            $books = $wpdb->get_results($query);

            foreach ($books as $book) {
                $text = $book->name . $book->categories . $book->description . AVApi::getLidersText($book->authors_id, $book->authors_other);
                $text = prepareText($text);
                
                $wpdb->update(
                    $table_name,
                    array(
                        'search_text' => $text
                    ),
                    array(
                        'id' => $book->id
                    )
                );
                echo "Search_text dla książki o id: " . $book->id . " został wygenerowany: \n" . $text . "\n\n";
            }

            return true;
        }

        function generateCoursesSearchText() {
            global $wpdb;
            
            $table_name = $wpdb->prefix . 'av_courses';

            $query = "SELECT * FROM $table_name";

            $courses = $wpdb->get_results($query);

            foreach ($courses as $course) {
                $text = $course->name . $course->categories . $course->description . AVApi::getLidersText($course->authors_id, $course->authors_other);
                $text = prepareText($text);
                
                $wpdb->update(
                    $table_name,
                    array(
                        'search_text' => $text
                    ),
                    array(
                        'id' => $course->id
                    )
                );
                echo "Search_text dla kursu o id: " . $course->id . " został wygenerowany: \n" . $text . "\n\n";
            }

            return true;
        }

        function generateDictionarySearchText() {
            global $wpdb;
            
            $table_name = $wpdb->prefix . 'av_dictionary';

            $query = "SELECT * FROM $table_name";

            $phrases = $wpdb->get_results($query);

            foreach ($phrases as $phrase) {
                $text = prepareText($phrase->phrase);
                
                $wpdb->update(
                    $table_name,
                    array(
                        'search_text' => $text
                    ),
                    array(
                        'id' => $phrase->id
                    )
                );
                echo "Search_text dla frazy o id: " . $phrase->id . " został wygenerowany: \n" . $text . "\n\n";
            }

            return true;
        }

        function prepareText($text) {

            $newText = preg_replace('/\s+/', '', $text);
            $newText = str_replace(',', '', $newText);
            $newText = str_replace('"', '', $newText);
    
            return $newText;
        }

        function getParentPostId($data) {
            $productParentIds = [
                'book' => 1991,
                'podcast' => 1955,
                'course' => 2222,
            ];

            $liderParentIds = [
                'polski' => 1584,
                'zagraniczny' => 2303,
            ];

            if($data->type == 'lider') {
                return $liderParentIds[$data->country_group];
            } else {
                return $productParentIds[$data->type];
            }
        }

    }

    public static function getResults($tableName, $where = "is_visible = 1", $orderBy = "is_top DESC, name") {
        global $wpdb;
                
        $table_name = $wpdb->prefix . $tableName;

        $query = "SELECT * FROM $table_name WHERE " . $where . " ORDER BY " . $orderBy;

        $response = $wpdb->get_results($query);

        return $response;
    }

    public static function getLiderName($id) {
        global $wpdb;
                
        $table_name = $wpdb->prefix . 'av_liders';

        $query = "SELECT first_name, last_name FROM $table_name WHERE id=" . $id;
        
        $response = $wpdb->get_results($query);

        $name = $response[0]->first_name . " " . $response[0]->last_name;

        return $name;
    }

    public static function getLidersText($ids = "", $plainText = "") {
        
        $authorsText = "";
        $lidersIDs = explode("," , $ids);
        foreach ($lidersIDs as $id) {
            $authorsText .= AVApi::getLiderName($id) . ", ";
        }

        if($plainText != "") {
            $authorsText .= $plainText;
            if($ids == "") {
                $authorsText = substr($authorsText,2);
            }
        } else {   
            $authorsText = substr($authorsText,0,-2);
        }

        return $authorsText;
    }

    public static function getLiderProducts($liderId = "") {

        $products = (object) array(
            'books' => [
                'quantity' => 0,
                'objects' => []
            ],
            'courses' => [
                'quantity' => 0,
                'objects' => []
            ],
            'podcasts' => [
                'quantity' => 0,
                'objects' => []
            ],
        );
        
        $books = AVApi::getResults("av_books", "authors_id IS NOT NULL");
        foreach ($books as $book) {
            $ids = explode(",", $book->authors_id);
            foreach ($ids as $id) {
                if($id == $liderId) {
                    ++$products->books['quantity'];
                    array_push($products->books['objects'], $book);
                }
            }
        }

        $courses = AVApi::getResults("av_courses", "authors_id IS NOT NULL");
        foreach ($courses as $course) {
            $ids = explode(",", $course->authors_id);
            foreach ($ids as $id) {
                if($id == $liderId) {
                    ++$products->courses['quantity'];
                    array_push($products->courses['objects'], $course);
                }
            }
        }

        $podcasts = AVApi::getResults("av_podcasts", "authors_id IS NOT NULL");
        foreach ($podcasts as $podcast) {
            $ids = explode(",", $podcast->authors_id);
            foreach ($ids as $id) {
                if($id == $liderId) {
                    ++$products->podcasts['quantity'];
                    array_push($products->podcasts['objects'], $podcast);
                }
            }
        }

        return $products;
    }

    public static function getBookBadges($book) {
        
        $badgesList = [];

        if(json_decode($book->info_printed)->available == 1) array_push($badgesList, "książka");
        if(json_decode($book->info_ebook)->available == 1) array_push($badgesList, "e-book");
        if(json_decode($book->info_audio)->available == 1) array_push($badgesList, "audio book");

        return $badgesList;
    }

    public static function getCourseBadges($course) {
        
        $badgesList = [];

        if($course->is_online == 1) array_push($badgesList, "online");
        if($course->is_stationary == 1) array_push($badgesList, "stacjonarny");

        return $badgesList;
    }

    public static function replaceAccents($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        
        return str_replace($a, $b, $str);
    }

    public static function getCounterValues() {

        $response["posts_count"] = wp_count_posts()->publish;

        global $wpdb;
                
        $table_name_liders = $wpdb->prefix . 'av_liders';

        $query_liders = "SELECT COUNT(id) FROM $table_name_liders WHERE is_visible = 1";
        
        $response["liders_count"] = $wpdb->get_results($query_liders, ARRAY_N)[0][0];

        $table_name_podcasts = $wpdb->prefix . 'av_podcasts';

        $query_podcasts = "SELECT COUNT(id) FROM $table_name_podcasts WHERE is_visible = 1";
        
        $response_podcasts = $wpdb->get_results($query_podcasts, ARRAY_N)[0][0];


        $table_name_books = $wpdb->prefix . 'av_books';

        $query_books = "SELECT COUNT(id) FROM $table_name_books WHERE is_visible = 1";
        
        $response_books = $wpdb->get_results($query_books, ARRAY_N)[0][0];

        $response["products_count"] = $response_podcasts + $response_books;


        $day_1 = new DateTime("2021-07-01");
        $today = new DateTime();

        $response["days_count"] = $today->diff($day_1)->format("%a");



        return $response;
    }

    public static function getDictionaryLetters() {
        $allLetters = array('A', 'Ą', 'B', 'C', 'D', 'E', 'Ę', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Ł', 'M', 'N', 'Ń', 'O', 'Ó', 'P', 'Q', 'R', 'S', 'Ś', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ź', 'Ż');
        $activeLetters = array();
        $returnArray = array();

        $results = AVApi::getResults('av_dictionary', "id > 0", "phrase");
        $currentLetter = "";
        foreach ($results as $row ) {    
            if($row->phrase[0] != $currentLetter) {
                $currentLetter = $row->phrase[0];
                array_push($activeLetters, $currentLetter);
            }
        }

        foreach ($allLetters as $letter) {
            if(in_array($letter, $activeLetters)) {
                array_push($returnArray, (object)[
                    "letter" => $letter,
                    "is_present" => 1
                ]);
            } else {
                array_push($returnArray, (object)[
                    "letter" => $letter,
                    "is_present" => 0
                ]);
            }
        }

        return $returnArray;
    }
    
}