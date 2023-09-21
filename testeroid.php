<?php 
/**
 * Plugin Name: Testeroid
 * Description: TDD and simple auto tests with WP CLI
 * Version: 0.2
 */

namespace Testeroid;

use WP_CLI;

if(class_exists('WP_CLI')){
    WP_CLI::add_command( 'tests', function(){
        $results = testing();
        if($results['success'] && empty($results['fails'])){
            WP_CLI::success( 'tests success' );
        } else {
            WP_CLI::error( 'tests fails', $exit = false );
        }
        var_dump($results);
        exit;
    } );

}

function testing(){

    global $test_results;
    
    if(empty($test_results)){
        $test_results = [
            'success' => 0,
            'fail' => 0,
            'fails' => [],
        ];
    }

    $path_pattern = __DIR__ . '/includes/*.php';
    if(defined('TESTEROID_TESTS_PATH')){
        $path_pattern = TESTEROID_TESTS_PATH;
    }

    foreach(glob($path_pattern) as $php_include) {
        require_once($php_include);
    }

    return $test_results;
}

function test($text, $function, $active = false){
    
    global $test_results;
    
    if($active){
        $result = call_user_func($function);
        if($result){
            $test_results['success']++;
        } else {
            $test_results['fail']++;
            $test_results['fails'][] = $text;
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
