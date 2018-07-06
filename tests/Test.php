<?php
namespace Tests;
use GooseStudio\LinkIt\Application;
use GooseStudio\LinkIt\Config;
use PHPUnit\Framework\TestCase;

class Test extends TestCase {

	public function setUp() {
		mkdir( __DIR__ . '/test-data/web/app/plugins/test-plugin/',0755, true);
		mkdir(__DIR__. '/test-data/test-plugin/', 0755, true);
		$file_data = file_get_contents(__DIR__ . '/linkit.json');
		$data = json_decode($file_data, true);
		$data['plugins']['src'][] = __DIR__ . '/test-data/test-plugin';
		file_put_contents(__DIR__ . '/test-data/linkit.json', json_encode($data));
		putenv('COMPOSER_DEV_MODE=1');
	}

	public function testRunOptionTest() {
		$config = Config::create(__DIR__ . '/test-data/linkit.json');
		$application = new Application( '0.1', ['test'=>false, 'hide'=>false], $config );
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin' );
		$application->run();
		$this->assertFalse(is_link(__DIR__ . '/test-data/web/app/plugins/test-plugin'));
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin' );
	}

	public function testRunOptionNoDev() {
		$config = Config::create(__DIR__ . '/test-data/linkit.json');
		putenv('COMPOSER_DEV_MODE=0');
		$application = new Application( '0.1', ['no-dev' => false,'hide'=>false], $config );
		$application->run();
		$this->assertFalse(is_link(__DIR__ . '/test-data/web/app/plugins/test-plugin'));
	}

	public function testRunOptionLinkItFile() {
		mkdir(__DIR__. '/test-data/test-plugin2/', 0755, true);
		$file_data = file_get_contents(__DIR__ . '/linkit-test.json');
		$data = json_decode($file_data, true);
		$data['plugins']['src'][] = __DIR__ . '/test-data/test-plugin2';
		file_put_contents(__DIR__ . '/test-data/linkit-test.json', json_encode($data));
		$application = new Application( '0.1', ['linkit'=>__DIR__ . '/test-data/linkit-test.json','hide'=>false]);
		$this->assertFileNotExists( __DIR__ . '/test-data/web/app/plugins/test-plugin2' );
		$this->assertFalse(is_link( __DIR__ . '/test-data/web/app/plugins/test-plugin2' ));
		$application->run();
		$this->assertTrue(is_link(__DIR__ . '/test-data/web/app/plugins/test-plugin2'));
	}


	public function testRun() {
		$config = Config::create(__DIR__ . '/test-data/linkit.json');
		$application = new Application( '0.1', ['hide'=>false], $config );
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin' );
		$application->run();
		$this->assertTrue(is_link(__DIR__ . '/test-data/web/app/plugins/test-plugin'));
	}

	public function testRunKeep() {
		$config = Config::create(__DIR__ . '/test-data/linkit.json');
		$application = new Application( '0.1', ['hide'=>false, 'keep' => false], $config );
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin' );
		$application->run();
		$this->assertTrue(is_link(__DIR__ . '/test-data/web/app/plugins/test-plugin'));
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin-1' );
	}

	public function testRunKeepPreExisting() {
		$config = Config::create(__DIR__ . '/test-data/linkit.json');
		$application = new Application( '0.1', ['hide'=>false, 'keep' => false], $config );
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin' );
		mkdir(__DIR__ . '/test-data/web/app/plugins/test-plugin-1', 0755, true);
		mkdir(__DIR__ . '/test-data/web/app/plugins/test-plugin-2', 0755, true);
		$application->run();
		$this->assertTrue(is_link(__DIR__ . '/test-data/web/app/plugins/test-plugin'));
		$this->assertFileExists( __DIR__ . '/test-data/web/app/plugins/test-plugin-3' );
	}

	public function tearDown() {
		shell_exec('rm -R ' . __DIR__ . '/test-data/');
	}
}
