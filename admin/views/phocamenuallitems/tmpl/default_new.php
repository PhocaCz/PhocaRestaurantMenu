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
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;



?>
<div class="modal hide fade" id="collapseModalNew">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_($this->t['l'] . '_ADD_NEW_ITEM');?></h3>
	</div>
	<div class="modal-body">


		<div class="control-group">
			<div class="controls">
				<?php


				$item = PhocaMenuNew::item();
				echo $item['output'];
				//if (isset($item['status']) && $item['status'] == 1) {
				//	echo $item['output'];
				//}
				?>
			</div>
		</div>

	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<?php if (isset($item['status']) && $item['status'] == 1) { ?>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('phocamenuitem.add');">
			<?php echo JText::_('COM_PHOCAMENU_ADD_NEW_ITEM'); ?>
		</button>
		<?php } ?>
	</div>
</div>
