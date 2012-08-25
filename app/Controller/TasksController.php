<?php
/**
 *
 * TasksController Controller for the DevTrack system
 * Provides the hard-graft control of the tasks for users
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     DevTrack Development Team 2012
 * @link          http://github.com/chrisbulmer/devtrack
 * @package       DevTrack.Controller
 * @since         DevTrack v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

class TasksController extends AppController {

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array('Time');

    /*
     * _projectCheck
     * Space saver to ensure user can view content
     * Also sets commonly needed variables related to the project
     *
     * @param $name string Project name
     */
    private function _projectCheck($name) {
        // Check for existent project
        $project = $this->Task->Project->getProject($name);
        if ( empty($project) ) throw new NotFoundException(__('Invalid project'));
        $this->Task->Project->id = $project['Project']['id'];

        $user = $this->Auth->user('id');

        // Lock out those who are not guests
        if ( !$this->Task->Project->hasRead($user) ) throw new ForbiddenException(__('You are not a member of this project'));

        $this->set('project', $project);
        $this->set('isAdmin', $this->Task->Project->isAdmin($user));

        return $project;
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($project = null) {
        $project = $this->_projectCheck($project);

        $user = $this->Task->find('all', array(
            'conditions' => array(
                'Project.id' => $project['Project']['id'],
                'Assignee.id' => $this->Auth->user('id'),
                'TaskStatus.id' => array(1,2)
            ),
            'order' => 'TaskPriority.id DESC'
        ));
        $team = $this->Task->find('all', array(
            'conditions' => array(
                'Project.id' => $project['Project']['id'],
                'Assignee.id !=' => $this->Auth->user('id'),
                'TaskStatus.id' => array(1,2)
            ),
            'order' => 'TaskPriority.id DESC'
        ));
        $others = $this->Task->find('all', array(
            'conditions' => array(
                'Project.id' => $project['Project']['id'],
                'Assignee.id' => null,
                'TaskStatus.id' => array(1,2)
            ),
            'order' => 'TaskPriority.id DESC'
        ));

        // Final value is min size of the board
        $max = max(sizeof($user), sizeof($team), sizeof($others), 5);

        $this->set('user_empty', $max - sizeof($user));
        $this->set('team_empty', $max - sizeof($team));
        $this->set('others_empty', $max - sizeof($others));
        $this->set(compact('user', 'team',  'others'));
    }

    /**
     * sprint method
     *
     * @return void
     */
    public function sprint($project = null) {
        $project = $this->_projectCheck($project);

        $backlog = $this->Task->find('all', array(
            'conditions' => array(
                'Project.id' => $project['Project']['id'],
                'TaskStatus.id' => 1
            ),
            'order' => 'TaskPriority.id DESC'
        ));
        $inProgress = $this->Task->find('all', array(
            'conditions' => array(
                'Project.id' => $project['Project']['id'],
                'TaskStatus.id' => 2
            ),
            'order' => 'TaskPriority.id DESC'
        ));
        $completed = $this->Task->find('all', array(
            'conditions' => array(
                'Project.id' => $project['Project']['id'],
                'TaskStatus.id' => 3
            ),
            'order' => 'TaskPriority.id DESC'
        ));

        // Final value is min size of the board
        $max = max(sizeof($backlog), sizeof($inProgress), sizeof($completed), 5);

        $this->set('backlog_empty', $max - sizeof($backlog));
        $this->set('inProgress_empty', $max - sizeof($inProgress));
        $this->set('completed_empty', $max - sizeof($completed));
        $this->set(compact('backlog', 'inProgress',  'completed'));
    }

    /**
     * view method
     *
     * @param string $id
     * @return void
     */
    public function view($project = null, $id = null) {
        $project = $this->_projectCheck($project);

        $this->Task->id = $id;
        if (!$this->Task->exists()) {
            throw new NotFoundException(__('Invalid task'));
        }
        $this->set('task', $this->Task->read(null, $id));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($project = null) {
        $project = $this->_projectCheck($project);

        if ($this->request->is('ajax')) {
            // Enable once we've figured out how to do two Ajax submit buttons
            // if ($this->request->data['submit'] == 1) {
                 $this->redirect(array('project' => $project['Project']['name'], 'action' => 'add'));
            // }

            $this->autoRender = false;
            $this->Task->create();

            $this->request->data['Task']['project_id'] = $project['Project']['id'];
            $this->request->data['Task']['owner_id'] = $this->Auth->user('id');
            $this->request->data['Task']['assignee_id'] = null;
            $this->request->data['Task']['milestone_id'] = null;
            $this->request->data['Task']['task_status_id'] = 1;
            $this->request->data['Task']['task_type_id'] = 3;
            $this->request->data['Task']['task_priority_id'] = 2;

            if ($this->Task->save($this->request->data)) {
                echo '<div class="alert alert-success"><a class="close" data-dismiss="alert">x</a>Time successfully logged.</div>';
            } else {
                echo '<div class="alert alert-error"><a class="close" data-dismiss="alert">x</a>Could not log time to the project. Please, try again.</div>';
            }
        } else if ($this->request->is('post')) {
            $this->Task->create();

            $this->request->data['Task']['project_id'] = $project['Project']['id'];
            $this->request->data['Task']['owner_id'] = $this->Auth->user('id');
            $this->request->data['Task']['assignee_id'] = null;
            $this->request->data['Task']['milestone_id'] = ($this->request->data['Task']['milestone_id'] == 0) ? null : $this->request->data['Task']['milestone_id'];
            $this->request->data['Task']['task_status_id'] = 1;

            if ($this->Task->save($this->request->data)) {
                $this->Session->setFlash(__('The task has been added successfully'), 'default', array(), 'success');
                $this->redirect(array('project' => $project['Project']['name'], 'action' => 'view', $this->Task->id));
            } else {
                $this->Session->setFlash(__('The task could not be saved. Please, try again.'));
            }
        } else {

            // Fetch all the variables for the view
            $taskPriorities = $this->Task->TaskPriority->find('list', array('order' => 'id DESC'));
            $milestonesOpen = $this->Task->Milestone->find('list');
            $milestonesClosed = $this->Task->Milestone->find('list');
            $milestones = array('No Assigned Milestone');
            if (!empty($milestonesOpen)) {
                $milestones['Open'] = $milestonesOpen;
            }
            if (!empty($milestonesClosed)) {
                $milestones['Closed'] = $milestonesClosed;
            }
            foreach ( $taskPriorities as $id => $p ) {
                $taskPriorities[$id] = ucfirst(strtolower($p));
            }
            $this->set(compact('taskPriorities', 'milestones'));
        }
    }

    /**
     * edit method
     *
     * @param string $id
     * @return void
     */
    public function edit($project = null, $id = null) {
        $project = $this->_projectCheck($project);

        $this->Task->id = $id;
        if (!$this->Task->exists()) {
            throw new NotFoundException(__('Invalid task'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Task->save($this->request->data)) {
                $this->Session->setFlash(__('The task has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The task could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Task->read(null, $id);
        }
        $projects = $this->Task->Project->find('list');
        $owners = $this->Task->Owner->find('list');
        $taskTypes = $this->Task->TaskType->find('list');
        $taskStatuses = $this->Task->TaskStatus->find('list');
        $assignees = $this->Task->Assignee->find('list');
        $milestones = $this->Task->Milestone->find('list');
        $this->set(compact('projects', 'owners', 'taskTypes', 'taskStatuses', 'assignees', 'milestones'));
    }

    /**
     * delete method
     *
     * @param string $id
     * @return void
     */
    public function delete($project = null, $id = null) {
        $project = $this->_projectCheck($project);

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Task->id = $id;
        if (!$this->Task->exists()) {
            throw new NotFoundException(__('Invalid task'));
        }
        if ($this->Task->delete()) {
            $this->Session->setFlash(__('Task deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Task was not deleted'));
        $this->redirect(array('action' => 'index'));
    }
}