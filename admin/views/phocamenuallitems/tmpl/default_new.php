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
use Joomla\CMS\Language\Text;

use Joomla\CMS\Layout\LayoutHelper;



?>
<div id="collapseModalNew" role="dialog" tabindex="-1" class="joomla-modal modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
	<div class="modal-header">
        <h3 class="modal-title"><?php echo Text::_('COM_PHOCAMENU_ADD_NEW_ITEM');?></h3>
				<button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="<?php Text::_('COM_PHOCAMENU_CLOSE'); ?>">
		</button>

	</div>
	<div class="modal-body">

        <div class="p-3">
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
    </div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''" data-bs-dismiss="modal">
			<?php echo Text::_('JCANCEL'); ?>
		</button>
		<?php if (isset($item['status']) && $item['status'] == 1) { ?>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('phocamenuitem.add');">
			<?php echo Text::_('COM_PHOCAMENU_ADD_NEW_ITEM'); ?>
		</button>
		<?php } ?>
	</div>
</div>
    </div>
</div>
