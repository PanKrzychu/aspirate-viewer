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
            
            $table_name = $wpdb->prefix . 'cv_courses';

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

    public static function getCourses() {
        global $wpdb;
                
        $table_name = $wpdb->prefix . 'cv_courses';

        $query = "SELECT * FROM $table_name ORDER BY top DESC, name";
        
        $response = $wpdb->get_results($query);

        return $response;
    }

    public static function getResults($tableName) {
        global $wpdb;
                
        $table_name = $wpdb->prefix . $tableName;

        $query = "SELECT * FROM $table_name ORDER BY top DESC, name";
        
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
    
}