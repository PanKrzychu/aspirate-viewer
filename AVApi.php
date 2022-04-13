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
                $text = preg_replace('/\s+/', '', $text);
                $text = str_replace(',', '', $text);
                $text = str_replace('"', '', $text);
                
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
                $text = preg_replace('/\s+/', '', $text);
                $text = str_replace(',', '', $text);
                $text = str_replace('"', '', $text);
                
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
                $text = preg_replace('/\s+/', '', $text);
                $text = str_replace(',', '', $text);
                $text = str_replace('"', '', $text);
                
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
    
}