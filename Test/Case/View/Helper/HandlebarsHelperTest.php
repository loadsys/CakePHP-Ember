<?php

App::uses('Controller', 'Controller');
App::uses('Helper', 'View');
App::uses('AppHelper', 'View/Helper');
App::uses('HandlebarsHelper', 'Ember.View/Helper');

class TestController extends Controller {
	public $name = 'Test';
	public $uses = null;
}

class TestHandlebarsHelper extends HandlebarsHelper {
	public function fullPath($path) {
		return parent::_fullPath($path);
	}

	public function fileName($path) {
		return parent::_fileName($path);
	}

	public function validTemplate($path) {
		return parent::_validTemplate($path);
	}

	public function validDirectory($path) {
		return parent::_validDirectory($path);
	}

	public function templateName($path) {
		return parent::_templateName($path);
	}

	public function loadTemplate($path) {
		return parent::_loadTemplate($path);
	}

	public function loadTemplates($path) {
		return parent::_loadTemplates($path);
	}
}

class HandlebarsHelperTest extends CakeTestCase {

	public $Handlebars;
	public $View;

	public function setUp() {
		parent::setUp();
		$this->basePath = APP.'Plugin'.DS.'Ember'.DS.'Test';
		$this->View = $this->getMock('View', array('append'), array(new TestController()));
		$settings = array('basePath' => $this->basePath);
		$this->Handlebars = new TestHandlebarsHelper($this->View, $settings);
		Configure::write('Asset.timestamp', false);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Handlebars, $this->View);
	}

	public function testBasePath() {
		$this->assertEqual($this->Handlebars->basePath, $this->basePath);
	}

	public function testFullPath() {
		$this->assertEqual($this->Handlebars->fullPath('tmp'), $this->basePath . DS . 'tmp');
	}

	public function testFileName() {
		$file = 'test.handlebars';
		$path = $this->basePath . DS . $file;
		$this->assertEqual($this->Handlebars->fileName($path), $file);
	}

	public function testValidTemplateWithHandlebarsExtension() {
		$path = $this->basePath . DS . 'test.handlebars';
		$this->assertTrue($this->Handlebars->validTemplate($path));
	}

	public function testValidTemplateWithHbsExtension() {
		$path = $this->basePath . DS . 'test.hbs';
		$this->assertTrue($this->Handlebars->validTemplate($path));
	}

	public function testValidTemplateWithJsExtension() {
		$path = $this->basePath . DS . 'test.js';
		$this->assertFalse($this->Handlebars->validTemplate($path));
	}

	public function testValidDirectoryThatExists() {
		$path = $this->basePath . DS . 'tmp' . DS . 'first_dir';
		$this->assertTrue($this->Handlebars->validDirectory($path));
	}

	public function testValidDirectoryThatDoesntExists() {
		$path = $this->basePath . DS . 'not_there';
		$this->assertFalse($this->Handlebars->validDirectory($path));
	}

	public function testValidDirectoryOnAFile() {
		$path = $this->basePath . DS . 'tmp' . DS . 'first_template.handlebars';
		$this->assertFalse($this->Handlebars->validDirectory($path));
	}

	public function testTemplateNameWithHyphens() {
		$path = $this->basePath . DS . 'template-name.handlebars';
		$this->assertEqual($this->Handlebars->templateName($path), 'templateName');
	}

	public function testTemplateNameWithUnderscores() {
		$path = $this->basePath . DS . 'template_name.handlebars';
		$this->assertEqual($this->Handlebars->templateName($path), 'templateName');
	}

	public function testTemplateNameWithoutAnything() {
		$path = $this->basePath . DS . 'templatename.handlebars';
		$this->assertEqual($this->Handlebars->templateName($path), 'templatename');
	}

	public function testLoadTemplate() {
		$path = $this->basePath . DS . 'tmp' . DS . 'first_template.handlebars';
		$expected = "<script type=\"text/x-handlebars\" data-template-name=\"firstTemplate\">\nFirst Template\n</script>";
		$content = $this->Handlebars->loadTemplate($path);
		$this->assertEqual($content, $expected);
	}

	public function testLoadTemplateFileDoesntExist() {
		$path = $this->basePath . DS . 'tmp' . DS . 'not_found.handlebars';
		try {
			$this->Handlebars->loadTemplate($path);
		} catch (Exception $e) {
			$this->assertTrue(true);
			return;
		}
		$this->assertTrue(false, 'Should have thrown exception');
	}

	public function testLoadTemplates() {
		$path = $this->basePath . DS . 'tmp';
		$results = $this->Handlebars->loadTemplates($path);
		$this->assertEqual(2, count($results));
	}

	public function testTemplates() {
		$content = $this->Handlebars->templates('tmp');
		$expected  = "<script type=\"text/x-handlebars\" data-template-name=\"secondTemplate\">\nSecond Template\n</script>\n";
		$expected .= "<script type=\"text/x-handlebars\" data-template-name=\"firstTemplate\">\nFirst Template\n</script>";
		$this->assertEqual($content, $expected);
	}
}
