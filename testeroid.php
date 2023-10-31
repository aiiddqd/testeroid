<?php
/**
 * Plugin Name: Testeroid
 * Description: TDD and simple auto tests with WP CLI
 * Version: 0.9
 */

namespace Testeroid;

use WP_CLI, Throwable;

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::add_command( 'test', function ($terms, $args) {
		$results = testing( $terms, $args );
        
        WP_CLI::log(sprintf('success: %s', $results['success']));
        if(!empty($results['fails'])){
            WP_CLI::log(sprintf('fails: %s', $results['fail']));
            WP_CLI::error_multi_line( $results['fails'] );
        }

        if ( $results['success'] && empty( $results['fails'] ) ) {
			WP_CLI::success( 'tests success' );
		} else {
			WP_CLI::error( 'tests fails', $exit = true );
		}
	});
}

function testing( $terms, $args ) {

	global $test_results, $testerod_tests;

	if(isset($args['filter'])){
		$text = $args['filter'];
	}

	$path = __DIR__ . '/includes/';
	if ( defined( 'TESTEROID_TESTS_PATH' ) ) {
		$path = trailingslashit( TESTEROID_TESTS_PATH );
	}

	if ( isset( $terms[0] ) ) {
		$term = $terms[0];
		if ( str_ends_with( $term, '.php' ) ) {
			$path_pattern_test = $path_pattern = trailingslashit( $path ) . $term;
			if ( file_exists( $path_pattern_test ) ) {
				require_once $path_pattern_test;
			} else {
				WP_CLI::error( 'Tests no found: ' . $path_pattern_test, $exit = false );
			}
		} else {
			WP_CLI::error( 'File name shoud be php format. ' . $term, $exit = false );
		}
	} else {
		$path_pattern = trailingslashit( $path ) . '*.php';

		foreach ( glob( $path_pattern ) as $php_include ) {
			require_once( $php_include );
		}
	}

	if ( empty( $testerod_tests ) ) {
		WP_CLI::error( 'No tests. ' . $term, $exit = false );
	}


	$progress = \WP_CLI\Utils\make_progress_bar( 'Testing', count( $testerod_tests ), $interval = 1 );

    if ( empty( $test_results ) ) {
		$test_results = [ 
			'success' => 0,
			'fail' => 0,
			'fails' => [],
		];
	}

	foreach ( $testerod_tests as $test ) {
		$progress->tick();

		if(isset($text)){
			if($text !== $test['text']){
				continue;
			}
		}

		if ( $test['active'] ) {

			try {
				$result = call_user_func( $test['callback'] );
				if ( $result ) {
					$test_results['success']++;
				} else {
					$test_results['fail']++;

                    $msg = $test['text'];

                    if(isset($test['debug_backtrace'][0])){
                        $log = $test['debug_backtrace'][0];
                        $msg .= ' | File: ' . $log['file'] . ":" . $log['line'];
                    }

					$test_results['fails'][] = $msg;

				}
			} catch (Throwable $th) {
				$test_results['fail']++;
				$test_results['fails'][] = $test['text'] . '; ' . $th->getMessage() . '; ' . $th->getFile() . ':' . $th->getLine();
			}
		}
	}

	$progress->finish();


	return $test_results;
}


function test( $text, $function, $active = true ) {

	global $testerod_tests;
	if ( empty( $testerod_tests ) ) {
		$testerod_tests = [];
	}

	$testerod_tests[] = [ 
		'text' => $text,
		'callback' => $function,
		'active' => $active,
		'debug_backtrace' => debug_backtrace(),
	];
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


function ddcli( ...$vars ) {
	foreach ( $vars as $key => $var ) {
		print_r( $var, false );
		echo PHP_EOL;
	}
	exit;
}