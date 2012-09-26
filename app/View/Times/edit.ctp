<?php
/**
 *
 * View class for APP/times/edit for the DevTrack system
 * Allows users to edit time allocated to a project
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     DevTrack Development Team 2012
 * @link          http://github.com/chrisbulmer/devtrack
 * @package       DevTrack.View.Times
 * @since         DevTrack v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
$this->Html->script('bootstrap-datepicker', array('block' => 'scriptBottom'));
$this->Html->scriptBlock("$('.dp1').datepicker()", array('inline' => false));
$this->Html->css('datepicker', null, array ('inline' => false));
?>

<?= $this->DT->pHeader() ?>
<div class="row">
    <div class="span2">
        <?= $this->element('Sidebar/project') ?>
    </div>
    <div class="row">
        <?= $this->element('Time/topbar_edit', array('id' => $this->request->data['Time']['id'])) ?>
        <div class="span10">
            <?= $this->Form->create('Time', array('class' => 'form-horizontal')) ?>
            <div class="well times form">
                <?php
                echo $this->Bootstrap->input("mins", array(
                    "input" => $this->Form->text("mins", array("class" => "input-small", "placeholder" => "1h 30m")),
                    "label" => "Time Taken",
                    "help_block" => "The amount of time youve taken (in the format #h #m)"
                ));

                echo $this->Bootstrap->input("description", array(
                    "input" => $this->Form->textarea("description", array("class" => "input-xlarge")),
                    "label" => "Description",
                    "help_inline" => "(Optional)"
                ));

                echo $this->Bootstrap->input("date", array(
                    "input" => $this->Form->text("date", array("class" => "dp1", "value" => date('Y-m-d', strtotime($this->request->data['Time']['date'])), "data-date-format" => "yyyy-mm-dd")),
                    "label" => "Date"
                ));

                echo $this->Bootstrap->button("Submit", array("style" => "primary", "size" => "normal", 'class' => 'controls'));
                ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
