<?php
/**
 *
 * Email Confirmation Unit Tests for the DevTrack system
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	DevTrack Development Team 2012
 * @link			http://github.com/SourceKettle/devtrack
 * @package		DevTrack.Test.Case.Model
 * @since		DevTrack v 1.0
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('EmailConfirmationKey', 'Model');

class EmailConfirmationKeyTestCase extends CakeTestCase {

/**
 * fixtures - Populate the database with data of the following models
 */
	public $fixtures = array('app.email_confirmation_key', 'app.user');

/**
 * setUp function.
 * Run before each unit test.
 * Corrrecly sets up the test environment.
 *
 * @access public
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EmailConfirmationKey = ClassRegistry::init('EmailConfirmationKey');
	}

/**
 * tearDown function.
 * Tear down all created data after the tests.
 *
 * @access public
 * @return void
 */
	public function tearDown() {
		unset($this->EmailConfirmationKey);

		parent::tearDown();
	}

/**
 * test fixtures function.
 *
 * @access public
 * @return void
 */
	public function testFixture() {
		$this->EmailConfirmationKey->recursive = -1;
		$fixtures = array(
			array(
				'EmailConfirmationKey' => array(
					'id' => "1",
					'user_id' => "1",
					'key' => '306f2dc5c9588616647fe32603fb3991',
					'created' => '2012-06-01 12:33:03',
					'modified' => '2012-06-01 12:33:03'
				),
			),
		);
		$fixturesB = $this->EmailConfirmationKey->find('all');
		$this->assertEquals($fixtures, $fixturesB, "Arrays were not equal");
	}
}
