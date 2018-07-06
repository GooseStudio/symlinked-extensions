<?php

namespace GooseStudio\LinkIt;


class Application {
	private $version;
	/**
	 * @var Config|null
	 */
	private $config;
	private $test_run;
	private $has_no_dev;
	private $show_output;
	private $rename;

	/**
	 * Application constructor.
	 *
	 * @param $version
	 * @param array $options
	 * @param Config|null $config
	 */
	public function __construct( $version, array $options = [], Config $config = null ) {
		$this->version     = $version;
		$config_path       = $options['linkit'] ?? $this->getConfigPath();
		$this->config      = $config ?: Config::create( $config_path );
		$this->test_run    = isset( $options['test'] );
		$this->has_no_dev  = isset( $options['no-dev'] );
		$this->show_output = ! isset( $options['hide'] );
		$this->rename      = isset( $options['keep'] );
	}

	/**
	 * @return string
	 */
	private function getConfigPath() {
		if ( is_file( $config_path = getcwd() . '/../linkit.json' ) ) {
			return $config_path;
		}

		if ( is_file( $config_path = getcwd() . '/../../linkit.json' ) ) {
			return $config_path;
		}

		if ( is_file( $config_path = __DIR__ . '/../linkit.json' ) ) {
			return $config_path;
		}

		if ( is_file( $config_path = __DIR__ . '/../../../linkit.json' ) ) {
			return $config_path;
		}

		if ( is_file( $config_path = __DIR__ . '/../../../../linkit.json' ) ) {
			return $config_path;
		}

		return __DIR__ . '/../linkit.json';
	}

	/**
	 *
	 */
	public function run() {
		echo 'Running Symlinked Extensions v' . $this->version, "\n";
		if ( $this->has_no_dev && ( false !== getenv( 'COMPOSER_DEV_MODE' ) && 0 === (int) getenv( 'COMPOSER_DEV_MODE' ) ) ) {
			$this->printStatement( '--no-dev detected, aborting.' );

			return;
		}
		$root = dirname( $this->getConfigPath() );
		$data = $this->config->getPaths();
		foreach ( $data as $folder => $paths ) {
			$this->printStatement( 'Checking ' . $folder );
			if ( $paths['src'] ) {
				$target_folder = $paths['target'];
				$sources       = $paths['src'];
				foreach ( $sources as $source ) {
					$target_path = $root . '/' . $target_folder . basename( $source );
					if ( ! is_link( $target_path ) && file_exists( $target_path ) ) {
						$this->printStatement( 'Deleting ' . basename( $source ) . ' at ' . $target_path );
						if ( $this->test_run ) {
							$this->printStatement( 'Test: rm -r ' . $target_path );
						} else if ( $this->rename ) {
							$folders = glob($target_path.'-*');
							rename($target_path , $target_path . '-' . (count($folders)+1) );
						} else {
							shell_exec( 'rm -r ' . $target_path );
						}
					}
					if ( ! file_exists( $target_path ) ) {
						$this->printStatement( 'Creating symlink ' . $source . ' to ' . $target_path );
						if ( $this->test_run ) {
							$this->printStatement( 'Test: symlink( ' . $source . ', ' . $target_path . ' )' );
						} else {
							symlink( $source, $target_path );
						}
					}
				}

			} else if ( $this->test_run ) {
				$this->printStatement( 'Test: No changes' );
			} else {
				$this->printStatement( 'No changes' );
			}
		}
	}

	private function printStatement( $statement ) {
		if ( $this->show_output ) {
			echo $statement . "\n";
		}
	}
}