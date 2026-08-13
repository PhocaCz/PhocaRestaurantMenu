<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access');


if (!empty($this->t['output'])) {


    echo '<?xml version="1.0" encoding="utf-8"?>'. "\n";
    if ($this->p['feed_root'] != '') {
        echo '<'.$this->p['feed_root'].'>'. "\n";
    }


    if (!empty($this->t['output']['groups'])) {

        foreach($this->t['output']['groups'] as $k => $v) {

            if (!empty($v['items'])) {
                foreach($v['items'] as $k2 => $v2) {


                    // --- Normally should be above but tested service works this way
                    if ($this->p['feed_group'] != '') {
                        echo "\t". '<'.$this->p['feed_group'].'>'. "\n";
                    }

                    if ($this->p['feed_date'] != '' && isset($this->t['output']['date']) && $this->t['output']['date'] != ''){
                        echo "\t\t" . '<'.$this->p['feed_date'].'>' . HTMLHelper::_('date', $this->t['output']['date'], Text::_($this->p['feed_date_format'])). '</'.$this->p['feed_date'].'>'. "\n";
                    }


                    if ($this->p['feed_item'] != '') {
                        echo "\t\t". '<'.$this->p['feed_item'].'>'. "\n";
                    }
                    // ---


                    if ($this->p['feed_item_type'] != '' && isset($v['type_group']) && $v['type_group'] != '') {
                        echo "\t\t\t". '<'.$this->p['feed_item_type'].'>';
                        echo $v['type_group'];
                        echo '</'.$this->p['feed_item_type'].'>'. "\n";
                    }

                    if ($this->p['feed_item_title'] != '' && isset($v2['title']) && $v2['title'] != '') {
                        echo "\t\t\t". '<'.$this->p['feed_item_title'].'>';
                        echo $v2['title'];
                        echo '</'.$this->p['feed_item_title'].'>'. "\n";
                    }

                    if ($this->p['feed_item_price'] != '' && isset($v2['price']) && $v2['price'] != '') {
                        echo "\t\t\t". '<'.$this->p['feed_item_price'].'>';
                        echo $v2['price'];
                        echo '</'.$this->p['feed_item_price'].'>'. "\n";
                    }

                    if ($this->p['feed_item_additional_info'] != '' && isset($v2['additional_info']) && $v2['additional_info'] != '') {
                        echo "\t\t\t". '<'.$this->p['feed_item_additional_info'].'>';
                        echo $v2['additional_info'];
                        echo '</'.$this->p['feed_item_additional_info'].'>'. "\n";
                    }


                    // --- Normally should be above but tested service works this way
                    // Each item includes even group (maybe wrong XML format but based on existing service)
                    if ($this->p['feed_item'] != '') {
                        echo "\t\t". '</'.$this->p['feed_item'].'>'. "\n";
                    }

                    if ($this->p['feed_group'] != '') {
                        echo "\t". '</'.$this->p['feed_group'].'>'. "\n";
                    }
                    // ---

                }

                // Note in footer

                if ($this->p['feed_note'] != '' && $this->p['feed_item_title'] != '') {

                    // --- Normally should be above but tested service works this way
                    if ($this->p['feed_group'] != '') {
                        echo "\t" . '<' . $this->p['feed_group'] . '>' . "\n";
                    }

                    if ($this->p['feed_date'] != '' && isset($this->t['output']['date']) && $this->t['output']['date'] != '') {
                        echo "\t\t" . '<' . $this->p['feed_date'] . '>' . HTMLHelper::_('date', $this->t['output']['date'], Text::_($this->p['feed_date_format'])) . '</' . $this->p['feed_date'] . '>' . "\n";
                    }


                    if ($this->p['feed_item'] != '') {
                        echo "\t\t" . '<' . $this->p['feed_item'] . '>' . "\n";
                    }
                    // ---

                    if ($this->p['feed_note_type'] != '' && isset($this->p['feed_item_type']) && $this->p['feed_item_type'] != '') {
                        echo "\t\t\t" . '<' . $this->p['feed_item_type'] . '>' . $this->p['feed_note_type']. '</' . $this->p['feed_item_type'] . '>' . "\n";
                    }
                    echo "\t\t\t" . '<' . $this->p['feed_item_title'] . '>'.$this->p['feed_note'] .'</' . $this->p['feed_item_title'] . '>' . "\n";


                    // --- Normally should be above but tested service works this way
                    // Each item includes even group (maybe wrong XML format but based on existing service)
                    if ($this->p['feed_item'] != '') {
                        echo "\t\t" . '</' . $this->p['feed_item'] . '>' . "\n";
                    }

                    if ($this->p['feed_group'] != '') {
                        echo "\t" . '</' . $this->p['feed_group'] . '>' . "\n";
                    }
                    // ---
                }

            }
        }
    }

    if ($this->p['feed_root'] != '') {
        echo '</'.$this->p['feed_root'].'>';
    }
    echo '' . "\n";
}
?>
