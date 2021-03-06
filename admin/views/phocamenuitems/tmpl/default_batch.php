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

$published = $this->state->get('filter.state');

?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_($this->t['l'] . '_BATCH_OPTIONS_ITEMS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php /* echo JText::_('COM_CONTENT_BATCH_TIP');*/ ?></p>
		<?php /*<div class="control-group">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.access', []);?>
			</div>
		</div> */ ?>
		<div class="control-group">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
			</div>
		</div>
		<?php if ($published >= 0) : ?>
		<div class="control-group">
			<div class="controls">
				<?php

				$class	=	$this->t['n'].'Batch';

				echo $class::item($published, 0);
				?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('<?php echo $this->t['task'] ?>.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
