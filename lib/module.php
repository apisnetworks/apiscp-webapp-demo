<?php declare(strict_types=1);

	namespace apisnetworks\demo;

	use Module\Support\Webapps;

	class Demo_Module extends Webapps
	{

		protected $aclList = [
			'max' => [
				// allow write access to foo by the web server
				'foo'
			]
		];
		const APP_NAME = 'Demo';
		/**
		 * Installed app is type "demo"
		 *
		 * @param string $mixed
		 * @param string $path
		 * @return bool
		 */
		public function valid(string $mixed, string $path = ''): bool
		{
			// $mixed is passed as [hostname, path] combination
			// convert to filesystem path
			if ($mixed[0] !== '/') {
				$mixed = $this->getDocumentRoot($mixed, $path);
			}
			return file_exists($this->domain_fs_path($mixed) . '/foo');
		}

		/**
		 * An extra API method available via "demo2:hello"
		 *
		 * @return string
		 */
		public function hello(): string
		{
			return "beautiful!";
		}

		/**
		 * Install application
		 *
		 * @param string $hostname
		 * @param string $path
		 * @param array  $opts
		 * @return bool
		 */
		public function install(string $hostname, string $path = '', array $opts = array()): bool
		{
			if (!$this->parseInstallOptions($opts, $hostname, $path)) {
				return false;
			}

			$docroot = $this->getDocumentRoot($hostname, $path);
			$contents = '1.0';

			if (!empty($opts['say'])) {
				// in module API call, enabled by specifying ['hello' => true] in $opts
				$contents .= ' ' . $this->hello();
			}

			$oldex = \Error_Reporter::exception_upgrade(\Error_Reporter::E_ERROR);
			$success = false;
			try {
				$success = $this->file_put_file_contents($docroot . '/foo', $contents);
				// set Web App meta data, used for inference as to what app is installed here
				$this->initializeMeta($docroot, $opts);
			} finally {
				// Restore ER exception promotion
				\Error_Reporter::exception_upgrade($oldex);
				if (!$success) {
					return false;
				}
			}

			// Apply Fortification, useful with PHP applications which run under a different UID
			// see Fortification.md
			$this->fortify($hostname, $path, 'max');

			// Send notification email
			$this->notifyInstalled($hostname, $path, $opts);

			return true;
		}

		/**
		 * Remove application
		 *
		 * @param string $hostname
		 * @param string $path
		 * @param string $delete
		 * @return bool
		 */
		public function uninstall(string $hostname, string $path = '', $delete = 'all'): bool
		{
			// parent does a good job of removing all traces, you can do any last minute touch-ups, such as
			// removing Redis services or changing DNS after uninstallation
			return parent::uninstall($hostname, $path, $delete);
		}

		/**
		 * Get available versions
		 *
		 * Used to determine whether an app is eligible for updates
		 *
		 * @return array|string[]
		 */
		public function get_versions(): array
		{
			return ['1.0'];
		}

		public function get_version(string $hostname, string $path = ''): ?string
		{
			$path = $this->getDocumentRoot($hostname, $path) . '/foo';

			// install file missing?
			if (!$this->file_exists($this->domain_fs_path($path))) {
				return null;
			}

			return strtok($this->domain_fs_path($path), ' ');
		}

		/**
		 * Get Web App handler name
		 *
		 * @return string
		 * @throws \ReflectionException
		 */
		public function getModule(): string
		{
			// fallback Web App handler name when "type" isn't specified in install
			return parent::getModule();
		}
	}
