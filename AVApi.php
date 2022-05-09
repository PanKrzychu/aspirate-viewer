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
                exit();
            }
        ));

        register_rest_route('AVApi/v1', '/set-visibility', array(
            'methods' => 'GET',
            'callback' => function() {
                global $wpdb;
                
                $table_name = $wpdb->prefix . 'lv_liders';

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
                            'visibility' => $visibility
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

        function prepareText($text) {

            $newText = preg_replace('/\s+/', '', $text);
            $newText = str_replace(',', '', $newText);
            $newText = str_replace('"', '', $newText);
    
            return $newText;
        }
        

    }

    public static function getLiders() {
        global $wpdb;
                
        $table_name = $wpdb->prefix . 'lv_liders';

        $query = "SELECT * FROM $table_name ORDER BY is_top DESC, first_name";
        
        $response = $wpdb->get_results($query);

        return $response;
    }

    public static function getBooks() {
        global $wpdb;
                
        $table_name = $wpdb->prefix . 'bv_books';

        $query = "SELECT * FROM $table_name ORDER BY top DESC, name";
        
        $response = $wpdb->get_results($query);

        return $response;
    }

    public static function getPodcasts() {
        global $wpdb;
                
        $table_name = $wpdb->prefix . 'pv_podcasts';

        $query = "SELECT * FROM $table_name ORDER BY top DESC, name";
        
        $response = $wpdb->get_results($query);

        return $response;
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