<?php declare(strict_types=1);
/**
 * Copyright (C) Apis Networks, Inc - All Rights Reserved.
 *
 * Unauthorized copying of this file, via any medium, is
 * strictly prohibited without consent. Any dissemination of
 * material herein is prohibited.
 *
 * For licensing inquiries email <licensing@apisnetworks.com>
 *
 * Written by Matt Saladna <matt@apisnetworks.com>, August 2020
 */


namespace apisnetworks\demo;

use Module\Support\Webapps\App\Type\Unknown\Handler as Unknown;

class Demo extends Unknown {

	const NAME = 'Demo App';
	/**
	 * Display application
	 *
	 * @return bool
	 */
	public function display(): bool
	{
		return true;
	}

	/**
	 * Get available versions
	 *
	 * @return array|string[]
	 */
	public function getVersions(): array
	{
		return ['1.0'];
	}

	/**
	 * API module that calls flow through
	 *
	 * @return string
	 */
	public function getClassMapping(): string
	{
		return 'demo2';
	}

	/**
	 * Wrapper to show API calls
	 *
	 * @param string $method
	 * @param null   $args
	 * @return bool
	 */
	public function __call($method, $args = null)
	{
		if (false === strpos($method, '_')) {
			return parent::__call($method, $args);
		}
		[$module, $fn] = explode('_', $method, 2);
		if ($module !== $this->getClassMapping()) {
			return parent::__call($method, $args);
		}

		if (is_debug()) {
			// optionally report all module calls on-screen as encountered
			// enable debug mode first,
			// cpcmd scope:set cp.debug true
			echo $module, ': ', $fn, "\n";
		}

		return parent::__call($method, $args);
	}

	public function handle(array $params): bool
	{
		if (!empty($params['say'])) {
			return success("Saying something nice: %s", $this->{$this->getClassMapping() . '_hello'}());
		}

		return parent::handle($params);
	}
}
