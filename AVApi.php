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
                exit();
            }
        ));

        register_rest_route('AVApi/v1', '/set-visibility', array(
            'methods' => 'GET',
            'callback' => function() {
                global $wpdb;
                
                $table_name = $wpdb->prefix . 'av_liders';

                $query = "SELECT * FROM $table_name";

                $liders = $wpdb->get_results($query);

                foreach ($liders as $lider) {
                    $settings = json_decode($lider->settings);
                    $visibility = $settings->visibility;
                    unset($settings->visibility);
                    $settings = json_encode($settings);                    
                    $wpdb->update(
                        $table_name,
                        array(
                            'settings' => $settings,
                            'is_visible' => $visibility
                        ),
                        array(
                            'id' => $lider->id
                        )
                    );
                    echo "Settings dla lidera o id: " . $lider->id . " zostały zaktualizowane\n\n";
                }

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

        function generateLidersSearchText() {
            global $wpdb;
                    
            $table_name = $wpdb->prefix . 'lv_liders';
    
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
                
            $table_name = $wpdb->prefix . 'pv_podcasts';

            $query = "SELECT * FROM $table_name";

            $podcasts = $wpdb->get_results($query);

            foreach ($podcasts as $podcast) {
                $text = $podcast->name . $podcast->tags . $podcast->description . $podcast->authors_id;
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
            
            $table_name = $wpdb->prefix . 'bv_books';

            $query = "SELECT * FROM $table_name";

            $books = $wpdb->get_results($query);

            foreach ($books as $book) {
                $text = $book->name . $book->categories . $book->description . $book->authors_id;
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
                $text = $course->name . $course->categories . $course->description . $course->authors_id;
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

        function prepareText($text) {

            $newText = preg_replace('/\s+/', '', $text);
            $newText = str_replace(',', '', $newText);
            $newText = str_replace('"', '', $newText);
    
            return $newText;
        }

    }

    public static function getResults($tableName, $orderBy = "is_top DESC, name") {
        global $wpdb;
                
        $table_name = $wpdb->prefix . $tableName;

        $query = "SELECT * FROM $table_name WHERE is_visible = 1 ORDER BY " . $orderBy;
        
        $response = $wpdb->get_results($query);

        return $response;
    }

    public static function getLiderName($id) {
        global $wpdb;
                
        $table_name = $wpdb->prefix . 'lv_liders';

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

    public static function replaceAccents($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        
        return str_replace($a, $b, $str);
    }

    public static function getCounterValues() {

        $response["posts_count"] = wp_count_posts()->publish;

        global $wpdb;
                
        $table_name_liders = $wpdb->prefix . 'lv_liders';

        $query_liders = "SELECT COUNT(id) FROM $table_name_liders WHERE visibility = 1";
        
        $response["liders_count"] = $wpdb->get_results($query_liders, ARRAY_N)[0][0];


        $table_name_podcasts = $wpdb->prefix . 'pv_podcasts';

        $query_podcasts = "SELECT COUNT(id) FROM $table_name_podcasts WHERE visibility = 1";
        
        $response_podcasts = $wpdb->get_results($query_podcasts, ARRAY_N)[0][0];


        $table_name_books = $wpdb->prefix . 'bv_books';

        $query_books = "SELECT COUNT(id) FROM $table_name_books WHERE visibility = 1";
        
        $response_books = $wpdb->get_results($query_books, ARRAY_N)[0][0];

        $response["products_count"] = $response_podcasts + $response_books;


        $day_1 = new DateTime("2021-07-01");
        $today = new DateTime();

        $response["days_count"] = $today->diff($day_1)->format("%a");



        return $response;
    }
    
}