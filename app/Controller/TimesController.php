<?php
/**
 *
 * TimesController Controller for the DevTrack system
 * Provides the hard-graft control of the time segments
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	 DevTrack Development Team 2012
 * @link			http://github.com/CodeKettle
 * @package		CodeKettle.Controller
 * @since		 CodeKettle v 0.1
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppProjectController', 'Controller');

class TimesController extends AppProjectController {

/**
 * helpers for additional rendering support
 */
	public $helpers = array(
		'Time',
		'GoogleChart.GoogleChart'
	);

/**
 * add
 * allows users to log time
 *
 * @access public
 * @param mixed $project
 * @return void
 */
	public function add($project) {
		$project = $this->_projectCheck($project, true);

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->Time->create();

			$this->request->data['Time']['user_id'] = $this->Auth->user('id');
			$this->request->data['Time']['project_id'] = $project['Project']['id'];

			if ($this->Time->save($this->request->data)) {
				echo '<div class="alert alert-success"><a class="close" data-dismiss="alert">x</a>Time successfully logged.</div>';
			} else {
				echo '<div class="alert alert-error"><a class="close" data-dismiss="alert">x</a>Could not log time to the project. Please, try again.</div>';
			}
		} else if ($this->request->is('post')) {
			$this->Time->create();
			$origTime = $this->request->data['Time']['mins'];

			$this->request->data['Time']['user_id'] = $this->Auth->user('id');
			$this->request->data['Time']['project_id'] = $project['Project']['id'];

			if ($this->Flash->c($this->Time->save($this->request->data))) {
				$this->redirect(array('project' => $project['Project']['name'], 'action' => 'index'));
			} else {
				// Show the user what they put in, its just nice
				$this->request->data['Time']['mins'] = $origTime;
			}
		}
		$this->set('tasks', $this->Time->Project->Task->fetchLoggableTasks());
	}

/**
 * delete function.
 *
 * @access public
 * @param mixed $project
 * @param mixed $id (default: null)
 * @return void
 */
	public function delete($project, $id = null) {
		$project = $this->_projectCheck($project, true);
		$time = $this->Time->open($id, true);

		$this->Flash->setUp();
		$this->Flash->d($this->Time->delete());
		$this->redirect(array('project' => $project['Project']['name'], 'action' => 'index'));
	}

/**
 * edit function.
 *
 * @access public
 * @param mixed $project
 * @param mixed $id (default: null)
 * @return void
 */
	public function edit($project, $id = null) {
		$project = $this->_projectCheck($project, true);
		$time = $this->Time->open($id, true);
		$this->set('time', $time);

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Time']['user_id'] = $this->Time->_auth_user_id;
			$this->request->data['Time']['project_id'] = $project['Project']['id'];

			if ($this->Flash->u($this->Time->save($this->request->data))) {
				$this->redirect(array('project' => $project['Project']['name'], 'action' => 'index'));
			}
		} else {
			$this->request->data = $time;
			$this->request->data['Time']['mins'] = $this->request->data['Time']['minutes']['s'];
			$this->set('tasks', $this->Time->Project->Task->fetchLoggableTasks());
		}
	}

/**
 * history
 * list the amount of time logged
 *
 * @access public
 * @param mixed $project (default: null)
 * @param mixed $week (default: null)
 * @return void
 */
	public function history($project = null, $year = null, $week = null) {
		$project = $this->_projectCheck($project);

		// Validate the Year
		if (($_year = $this->Time->validateYear($year)) != $year) {
			$this->redirect(array('project' => $project['Project']['name'], 'year' => $_year, 'week' => $week));
		}
		// Validate the week
		if (($_week = $this->Time->validateWeek($week, $year)) != $week) {
			$this->redirect(array('project' => $project['Project']['name'], 'year' => $year, 'week' => $_week));
		}

		$weekTasks = $this->Time->Project->Task->find('all', array(
			'conditions'	=> array(
				'Task.id' => $this->Time->tasksForWeek($year, $week),
			)
		));
		$weekTasks[] = array('Task' => array('id' => 0, 'subject' => 'No associated task'));

		$this->set('week', $this->Time->timesForWeek($year, $week));
		$this->set('weekTasks', $weekTasks);

		$this->set('thisWeek', $week);
		$this->set('thisYear', $year);

		if ($week == $this->Time->lastWeekOfYear($year)) {
			$this->set('nextWeek', 1);
			$this->set('nextYear', $year + 1);
		} else {
			$this->set('nextWeek', $week + 1);
			$this->set('nextYear', $year);
		}

		if ($week == 1) {
			$this->set('prevWeek', $this->Time->lastWeekOfYear($year - 1));
			$this->set('prevYear', $year - 1);
		} else {
			$this->set('prevWeek', $week - 1);
			$this->set('prevYear', $year);
		}
		$this->set('tasks', $this->Time->Project->Task->fetchLoggableTasks());
	}

/**
 * index function.
 *
 * @access public
 * @param mixed $project
 * @return void
 */
	public function index($project) {
		$this->redirect(array('project' => $project, 'controller' => 'times', 'action' => 'users'));
	}

/**
 * users
 * list the amount of time each user has logged
 *
 * @access public
 * @param mixed $project
 * @return void
 */
	public function users($project) {
		$project = $this->_projectCheck($project);

		$this->set('total_time', $this->Time->fetchTotalTimeForProject());
		$this->set('users', $this->Time->fetchUserTimesForProject());
	}

/**
 * view function.
 *
 * @access public
 * @param mixed $project
 * @param mixed $id (default: null)
 * @return void
 */
	public function view($project, $id = null) {
		$project = $this->_projectCheck($project);
		$time	= $this->Time->open($id);

		$this->set('time', $time);
		$this->set('task', $this->Time->Project->Task->findById($time['Time']['task_id']));
	}
}
