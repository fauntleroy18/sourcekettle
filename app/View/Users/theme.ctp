<?php
/**
 *
 * View class for APP/users/theme for the SourceKettle system
 * Shows a list of themes for a user to pick from
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     SourceKettle Development Team 2012
 * @link          https://github.com/SourceKettle/sourcekettle
 * @package       SourceKettle.View.Users
 * @since         SourceKettle v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

echo $this->Bootstrap->page_header($this->request->data['User']['name']); ?>

<div class="row">
    <div class="span2">
        <?= $this->element('Sidebar/users') ?>
    </div>
    <div class="span6">
        <?= $this->Form->create('User', array('class' => 'well form-horizontal', 'type' => 'post')) ?>
        <h3>Select your theme</h3>
        <?php
        $options = array('default' => 'SourceKettle default');
        foreach ($this->TwitterBootswatch->getThemes() as $a => $theme) {

            $options[$a] = $this->Popover->popover(
                $theme['name'],
                $theme['name'].' Preview',
                '<ul class="thumbnails">
                    <li>
                        <a href="#" class="thumbnail">
                            <img src="'.$theme['thumbnail'].'" alt="">
                        </a>
                    </li>
                </ul>
                '.$theme['description']
            );

        } ?>
        <?= $this->Bootstrap->radio("theme", array("options" => $options)) ?>
        <?= $this->Bootstrap->button("Update", array("style" => "primary", "size" => "large", 'class' => 'controls')) ?>
        <?= $this->Form->end() ?>
    </div>
    <div class="span4">
        <h3>Where do these magical themes originate?</h3>
        <p>
            Here at SourceKettle, we like making our own decisions.
            Like, should I put the milk in my tea before the water?
            Thankfully, some lovely folks over at <?= $this->Html->link('Bootswatch', 'http://bootswatch.com/') ?> host some themes that we can use to make SourceKettle <strong>Super Pretty</strong>.
        </p>
        <h3>So what's the catch?</h3>
        <p>
            Well, as we didn't design them, we can't guarentee they will actually look perfect.
            Some of the gadgets, gizmos and thingymabobs may not look quite right.<br>
            Want to help us make SourceKettle prettier? Tell us whats wrong, over on <?= $this->Html->link('GitHub', 'https://github.com/SourceKettle/sourcekettle') ?>.
        </p>
        <br>
        <h3>What do our developers think?</h3>
        <blockquote>
            <p>Awesome!</p>
            <small>@chriswbulmer</small>
        </blockquote>
        <blockquote class="pull-right">
            <p>I'm actually in love with these!</p>
            <small>@pwhittlesea</small>
        </blockquote>
    </div>
</div>
