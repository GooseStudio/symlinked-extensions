<?php
namespace GooseStudio\LinkIt;


use RuntimeException;

class Config {
	private $data;

	private function __construct() {

	}
	public static function create($path) {
		$config =  new self();
		$config->load($path);
		return $config;
	}

	public function load($path) {
		if(!file_exists($path)) {
			throw new \InvalidArgumentException(basename($path) . ' does not exist.');
		}
		$file_data = file_get_contents($path);
		$this->data = json_decode($file_data, true);
	}

	/**
	 * @return array
	 */
	public function getPaths() {
		if( $this->data === null ) {
			throw new RuntimeException('Data has not been loaded.');
		}
		return $this->data;
	}
}
