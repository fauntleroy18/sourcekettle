<?php

/**
 *
 * Element for displaying a generic sidebar for the DevTrack system
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @copyright     DevTrack Development Team 2012
 * @link          http://github.com/chrisbulmer/devtrack
 * @package       DevTrack.View.Elements
 * @since         DevTrack v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>

<ul class="well nav nav-list" style="padding: 8px 14px;">
<?php
    $c1 = $this->request['controller'];
    $a1 = $this->request['action'];

    $help = $options['help'];
    unset($options['help']);

    foreach ($options as $title => $section) {

        echo '<li class="nav-header">'.$title.'</li>';

        // Iterate over the sidebar options in $sshkey
        foreach ( $section as $feature => $options ){

            // Logic to figure out if we are in the right place
            $c2 = $options['url']['controller'];
            $a2 = $options['url']['action'];
            $isFeat = ($c1==$c2 && ($a1==$a2 || ($a1=='index' && $a2=='.')));

            echo "<li ";
            if ($isFeat) echo 'class="active"';
            echo ">";

            echo $this->Html->link(
                $this->Bootstrap->icon($options['icon'], ($isFeat) ? 'white' : 'black').' '.ucwords($feature),
                $options['url'],
                array('escape' => false)
            );

            echo "</li>";
        }
    }

    echo '<li class="divider"></li>';
    echo '<li>';
    echo $this->Html->link(
        $this->Bootstrap->icon('flag').' Help',
        array('controller' => 'help', 'action' => $help['action']),
        array('escape' => false)
    );

    echo '</li>';
?>
</ul>