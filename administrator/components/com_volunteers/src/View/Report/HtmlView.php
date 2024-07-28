<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Report;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Volunteers\Administrator\Model\ReportModel;

/**
 * View to edit a report.
 *
 * @since 4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected mixed $state;
    protected mixed $item;
    protected mixed $form;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function display($tpl = null): void
    {
        /** @var ReportModel $model */
        $model       = $this->getModel();
        $this->state = $model->getState();
        $this->item  = $model->getItem();
        $this->form  = $model->getForm();

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     */
    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);
        $user       = $this->getCurrentUser();
        $userId     = $user->id;
        $isNew      = ($this->item->id == 0);
        $checkedOut = !(is_null($this->item->checked_out) || $this->item->checked_out == $userId);
        $toolbar    = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar();
        $canDo      = ContentHelper::getActions('com_volunteers');

        ToolbarHelper::title($isNew ? Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_REPORTS_NEW') : Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_REPORTS_EDIT'), 'joomla');

        // For new records, check the create permission.
        if ($isNew) {
            $toolbar->apply('report.apply');

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($user) {
                    $childBar->save('report.save');
                    $childBar->save2new('report.save2new');
                }
            );

            $toolbar->cancel('report.cancel', 'JTOOLBAR_CANCEL');
        } else {
            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);

            if (!$checkedOut && $itemEditable) {
                $toolbar->apply('report.apply');
            }

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($checkedOut, $itemEditable, $canDo, $user) {
                    // Can't save the record if it's checked out and editable
                    if (!$checkedOut && $itemEditable) {
                        $childBar->save('report.save');
                    }
                    // If checked out, we can still save
                    if ($canDo->get('core.create')) {
                        $childBar->save2copy('report.save2copy');
                    }
                }
            );
            if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit')) {
                ToolbarHelper::versions('com_volunteers.report', $this->item->id);
            }
            $toolbar->cancel('report.cancel');
        }

        $toolbar->divider();
        $toolbar->inlinehelp();
        // For future use
        // $toolbar->help('Volunteers_Report:_Edit');
    }
}
