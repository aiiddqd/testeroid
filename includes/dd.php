<?php

require_once __DIR__.'/dd/kint.phar';

use Kint\Kint;

/**
 * d() - visual
 * s() - cli
 */

//raw data dump
if (! \function_exists('dd')) {
	function dd(...$args)
	{
		// Kint::$mode_default = Kint::MODE_TEXT;
		foreach ($args as $item) {
			echo '<pre>';
			var_dump($item);
			echo '</pre>';
		}

		//get backtrace
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		if (isset($bt[0])) {
			$caller = $bt[0];
			echo '<pre>';
			echo "\nCalled from {$caller['file']}:{$caller['line']}\n";
			echo '</pre>';
		}
	}
}

function dd_only_admins(...$args)
{
	if (is_user_logged_in() && current_user_can('manage_options')) {
		dd(...$args);
	}
}


if (! \function_exists('dd_qm')) {
	/**
	 * Debugging function for Query Monitor
	 *
	 * @param mixed ...$args
	 */
	function dd_qm(...$args)
	{
		foreach ($args as $item) {
			do_action('qm/debug', $item);
		}
	}
}

// for web mode
if (! \function_exists('dd_web')) {
	function dd_web(...$args)
	{
		d(...$args);
	}
	Kint::$aliases[] = 'dd_web';

}

// for cli mode
if (! \function_exists('dd_cli')) {
	function dd_cli(...$args)
	{
		s(...$args);
	}
	Kint::$aliases[] = 'dd_cli';
}
