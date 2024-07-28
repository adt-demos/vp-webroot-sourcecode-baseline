<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var \Joomla\Component\Volunteers\Site\View\Role\HtmlView $this */
// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.frontend');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
$wa->useScript('keepalive');
$wa->useScript('form.validate');
HtmlHelper::_('formbehavior.chosen', 'select');
?>

<div class="role-edit">

    <form id="role"
        action="<?php echo Route::_('index.php?option=com_volunteers&task=role.save&id=' . (int) $this->item->id); ?>"
        method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <div class="row">
            <div class="top-toolbar">
                <div class="btn-bottom-toolbar pull-right">
                    <div id="toolbar-cancel" class="btn-group">
                        <button class="volunteers_btn btn-danger"  type="button" onclick="history.back();return false;">
                            <span class="icon-cancel" aria-hidden="true"></span>
                            <?php echo Text::_('JCANCEL') ?>
                        </button>
                    </div>
                    <div id="toolbar-apply" class="btn-group">
                        <button class="vol-button-admin-success" type="submit">
                            <span class="icon-pencil" aria-hidden="true"></span>
                            <?php echo Text::_('JSAVE') ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="page-header">
                <h1 class="vol_h1">
                    <?php echo Text::_('COM_VOLUNTEERS_TITLE_ROLES_EDIT') ?>
                </h1>
            </div>
        </div>

        <?php echo $this->form->renderField('team'); ?>

        <hr>

        <?php echo $this->form->renderField('title'); ?>
        <?php echo $this->form->renderField('open'); ?>
        <?php echo $this->form->renderField('description'); ?>

        <hr>

        <div class="bottom-toolbar">
            <div class="btn-bottom-toolbar pull-right">
                <div id="toolbar-cancel" class="btn-group">
                    <button class="volunteers_btn btn-danger"  type="button" onclick="history.back();return false;">
                        <span class="icon-cancel" aria-hidden="true"></span>
                        <?php echo Text::_('JCANCEL') ?>
                    </button>
                </div>
                <div id="toolbar-apply" class="btn-group">
                    <button class="vol-button-admin-success" type="submit">
                        <span class="icon-pencil" aria-hidden="true"></span>
                        <?php echo Text::_('JSAVE') ?>
                    </button>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="com_volunteers" />
        <input type="hidden" name="task" value="role.save" />
        <?php echo HtmlHelper::_('form.token'); ?>
    </form>
</div>
