<?php
/**
 * Plugin Name: Testeroid
 * Description: Simple auto tests & TDD with WP CLI
 * Version: 0.9.251009
 */

namespace Testeroid;

use WP_CLI, Throwable;

if (! function_exists('dd')) {
	require_once __DIR__.'/includes/dd.php';
}

add_action('wp_cli_init', function () {

	WP_CLI::add_command('testeroid', function ($terms, $args) {
		$results = testing($terms, $args);
		$is_success = ! in_array(false, array_column($results, 'success'), true);

		var_dump($results);

		if ($is_success) {
			WP_CLI::success('Testeroid is success');
		} else {
			WP_CLI::error('Testeroid is fail');
		}
	});

	WP_CLI::add_command('ttest', function ($terms, $args) {

		$case = $terms[0] ?? null;
		if (empty($case)) {
			WP_CLI::error('Please provide a test case name as the first argument.', $exit = false);
			return;
		}

		$tests = apply_filters('testeroid_tests', []);

		if (! isset($tests[$case])) {
			WP_CLI::error('Test case not found: '.$case, $exit = false);
			return;
		}

		$test = $tests[$case];
		if (! is_callable($test)) {
			WP_CLI::error('Test case callback is not callable: '.$case, $exit = false);
			return;
		}

		$result = $test();
		if ($result) {
			WP_CLI::success('Test case passed: '.$case);
		} else {
			WP_CLI::error('Test case failed: '.$case);
		}
	});
});

function handleTest($test, $case = null)
{
	/**
	 * @var string $test['case']
	 * @var callable $test['callback']
	 * @var bool $test['active']
	 * @var string $test['group']
	 * @var array $test['debug_backtrace']
	 */
	if ($case) {
		WP_CLI::log($case);
	} else {
		// WP_CLI::log($test['case']);
	}
	try {
		if (is_callable($test)) {
			$resultOfTest = $test();
			$group = 'default';
		} elseif (is_callable($test['callback'])) {
			$resultOfTest = $test['callback']();
			$group = $test['group'] ?? 'default';
		} else {
			throw new \Exception('Callback is not callable');
		}

		if ($resultOfTest) {
			return [
				'case' => $case ?? $test['case'],
				'message' => $resultOfTest === true ? 'ok' : $resultOfTest,
				'group' => $group,
				'success' => true
			];
		} else {
			return [
				'case' => $case ?? $test['case'],
				'message' => $resultOfTest,
				'group' => $group,
				'success' => false
			];
		}

	} catch (Throwable $e) {
		return [
			'case' => $case ?? $test['case'],
			'message' => $e->getMessage(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTraceAsString(),
			'group' => $group,
			'success' => false
		];

	}
}


function testing($terms, $args)
{
	WP_CLI::log('Testeroid is running');

	/**
	 * @var array $tests
	 * @var string $test['case']
	 * @var callable $test['callback']
	 * @var bool $test['active']
	 * @var string $test['group']
	 * @var array $test['debug_backtrace']
	 */

	$tests = apply_filters('testeroid_tests', []);
	$results = [];

	if (isset($args['case'])) {
		if (isset($tests[$args['case']])) {
			if (is_callable($tests[$args['case']])) {
				$testSingle = $tests[$args['case']];
				$case = $args['case'];

			} else {
				$testSingle = $tests[$args['case']]['callback'];
				$case = $args['case'];
			}
		} else {
			WP_CLI::error('Test case not found: '.$args['case'], $exit = false);
			return;
		}

		if ($testSingle) {
			$results[] = handleTest($testSingle, $case ?? null);
		}

	} else {

		foreach ($tests as $test) {
			if (empty($test['active'])) {
				continue;
			}

			if (is_callable($test['callback'])) {
				$results[] = handleTest($test);
			}
		}
	}


	return $results;
}


function test($text, $function, $active = true, $group = 'default')
{

	global $testerod_tests;
	if (empty($testerod_tests)) {
		$testerod_tests = [];
	}

	$testerod_tests[] = [
		'text' => $text,
		'callback' => $function,
		'active' => $active,
		'group' => $group,
		'debug_backtrace' => debug_backtrace(),
	];
}


/**
 * like wc_transaction_query()
 * $type: start, rollback or commit
 */
function transaction_query($type = 'start')
{
	global $wpdb;

	$wpdb->hide_errors();

	switch ($type) {
		case 'commit':
			$wpdb->query('COMMIT');
			break;
		case 'rollback':
			$wpdb->query('ROLLBACK');
			break;
		default:
			$wpdb->query('START TRANSACTION');
			break;
	}
}


function ddcli(...$vars)
{
	foreach ($vars as $key => $var) {
		print_r($var, false);
		echo PHP_EOL;
	}
	exit;
}