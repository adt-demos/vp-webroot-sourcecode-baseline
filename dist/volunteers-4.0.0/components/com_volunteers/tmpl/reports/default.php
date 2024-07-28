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
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var \Joomla\Component\Volunteers\Site\View\Reports\HtmlView $this */

// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.frontend');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}


?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
    id="adminForm">
    <div class="row">
        <div class="filter-bar">
            <div class="btn-group pull-right">

                <?php echo HtmlHelper::_(
                    'select.groupedlist',
                    VolunteersHelper::reportcategories(),
                    'filter_category',
                    ['list.attr' => [
                                    'class' => 'input-xlarge form-select',
                                    'onchange' => 'document.adminForm.submit();'
                                    ]]
                ); ?>
            </div>
        </div>
        <div class="page-header">
            <h1 class="vol_h1">
                <?php echo Text::_('COM_VOLUNTEERS_TITLE_REPORTS') ?>
                <?php if ($this->category) :
                    ?>:
                    <?php echo $this->category; ?>
                    <?php
                endif; ?>
            </h1>
        </div>
    </div>

    <?php if (!empty($this->items)) : ?>
        <?php foreach ($this->items as $i => $item) : ?>
            <div class="row report">
                <div class="col-2">
                    <a
                        href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $item->volunteer_id) ?>">
                        <?php echo VolunteersHelper::image($item->volunteer_image, 'large', false, $item->volunteer_name); ?>
                    </a>
                </div>
                <div class="col-10">
                    <?php if ($item->acl->edit || ($this->user->id == $item->created_by)) : ?>
                        <a class="volunteers_btn pull-right"
                            href="<?php echo Route::_('index.php?option=com_volunteers&task=report.edit&id=' . $item->id) ?>">
                            <span class="icon-edit" aria-hidden="true"></span>
                            <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                        </a>
                    <?php endif; ?>
                    <h2 class="vol_h2">
                        <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $item->id) ?>">
                            <?php echo ($item->title); ?>
                        </a>
                    </h2>
                    <p class="muted">
                        <?php echo Text::_('COM_VOLUNTEERS_BY') ?>
                        <a
                            href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $item->volunteer_id) ?>"><?php echo $item->volunteer_name; ?></a>
                        <?php echo Text::_('COM_VOLUNTEERS_ON') ?>
                        <?php echo VolunteersHelper::date($item->created, 'Y-m-d H:i'); ?>
                        <?php echo Text::_('COM_VOLUNTEERS_IN') ?>
                        <a href="<?php echo $item->link; ?>"><?php echo $item->name; ?></a>
                    </p>
                    <p>
                        <?php echo HtmlHelper::_('string.truncate', strip_tags(trim($item->description)), 500); ?>
                    </p>
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $item->id) ?>" class="volunteers_btn">
                        <span class="icon-chevron-right" aria-hidden="true"></span>
                        <?php echo Text::_('COM_VOLUNTEERS_READ_MORE') ?>
                        &nbsp;
                        <?php echo $item->title; ?>
                    </a>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="row">
            <p class="alert alert-info">
                <?php echo Text::_('COM_VOLUNTEERS_NOTE_NO_REPORTS') ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="row">
        <a class="volunteers_btn pull-right btn-warning"
            href="<?php echo Route::_('index.php?option=com_volunteers&view=reports&filter_category=' . $this->state->get('filter.category') . '&format=feed&type=rss') ?>">
            <span class="icon-feed" aria-hidden="true"></span>
            <?php echo Text::_('COM_VOLUNTEERS_RSSFEED') ?>
        </a>
    </div>

    <div class="pagination">
        <p class="counter pull-right">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>

        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
</form>
