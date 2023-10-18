<?php 
/**
 * Plugin Name: Testeroid
 * Description: TDD and simple auto tests with WP CLI
 * Version: 0.7
 */

namespace Testeroid;

use WP_CLI;

if(class_exists('WP_CLI')){
    WP_CLI::add_command( 'test', function($terms, $args){


        $results = testing($terms, $args);
        if($results['success'] && empty($results['fails'])){
            WP_CLI::success( 'tests success' );
        } else {
            WP_CLI::error( 'tests fails', $exit = false );
        }
        var_dump($results);
        exit;
    } );

}

function testing($terms, $args){

    global $test_results;
    
    if(empty($test_results)){
        $test_results = [
            'success' => 0,
            'fail' => 0,
            'fails' => [],
        ];
    }

    $path = __DIR__ . '/includes/';
    if(defined('TESTEROID_TESTS_PATH')){
        $path = trailingslashit(TESTEROID_TESTS_PATH);
    }

    if(isset($terms[0])){
        $term = $terms[0];
        if( str_ends_with($term, '.php')){
            $path_pattern_test = $path_pattern = trailingslashit($path) . $term;
            if(file_exists($path_pattern_test)){
                require_once $path_pattern_test;
            } else {
                WP_CLI::error( 'Tests no found: ' . $path_pattern_test, $exit = false );
            }
        } else {
            WP_CLI::error( 'File name shoud be php format. ' . $term, $exit = false );
        }
    } else {
        $path_pattern = trailingslashit($path) . '*.php';

        foreach(glob($path_pattern) as $php_include) {
            require_once($php_include);
        }
    }

    

    return $test_results;
}


function test($text, $function, $active = true){
    
    global $test_results;
    
    if($active){
        try {
            $result = call_user_func($function);
            if($result){
                $test_results['success']++;
            } else {
                $test_results['fail']++;
                $test_results['fails'][] = $text;
            }
        } catch (\Throwable $th) {
            $test_results['fail']++;
            $test_results['fails'][] = $text . '; ' . $th->getMessage() . '; ' . $th->getFile() . ':' . $th->getLine();
        }   
    }
}


/**
 * like wc_transaction_query()
 * $type: start, rollback or commit
 */
function transaction_query( $type = 'start' ) {
	global $wpdb;

	$wpdb->hide_errors();

	switch ( $type ) {
        case 'commit':
            $wpdb->query( 'COMMIT' );
            break;
        case 'rollback':
            $wpdb->query( 'ROLLBACK' );
            break;
        default:
            $wpdb->query( 'START TRANSACTION' );
            break;
    }
}


function ddcli(...$vars){
    foreach($vars as $key => $var){
        print_r($var, false);
        echo PHP_EOL;
    }
    exit;
}
