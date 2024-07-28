<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Reports;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Volunteers\Administrator\Model\ReportsModel;
use Joomla\Registry\Registry;

/**
 * View class for a list of reports.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * An array of items
     *
     * @var  array
     *
     * @since 4.0.0
     */
    protected array $items;

    /**
     * The pagination object
     *
     * @var Pagination
     *
     * @since 4.0.0
     */
    protected Pagination $pagination;

    /**
     * The model state
     *
     * @var   Registry
     *
     * @since 4.0.0
     */
    protected mixed $state;

    /**
     * Form object for search filters
     *
     * @var  Form
     *
     * @since 4.0.0
     */
    public Form $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     *
     * @since 4.0.0
     */
    public array $activeFilters;

    /**
     * Is this view an Empty State
     *
     * @var   boolean
     * @since 4.0.0
     */
    private bool $isEmptyState = false;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return  void
     *
     * @throws Exception
     *
     * @since 4.0.0
     */
    public function display($tpl = null): void
    {
        /** @var ReportsModel $model */
        $model               = $this->getModel();
        $this->state         = $model->getState();
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   4.0.0
     * @throws Exception
     */
    private function addToolbar(): void
    {
        $canDo = ContentHelper::getActions('com_volunteers');
        $user  = Factory::getApplication()->getIdentity();

        // Get the toolbar object instance
        $toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar();

        ToolbarHelper::title(Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_REPORTS'), 'joomla');

        if ($canDo->get('core.create')) {
            $toolbar->addNew('report.add');
        }

        if (!$this->isEmptyState && $canDo->get('core.edit.state')) {
            /** @var  DropdownButton $dropdown */
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            $childBar->publish('reports.publish')->listCheck(true);
            $childBar->unpublish('reports.unpublish')->listCheck(true);
            $childBar->archive('reports.archive')->listCheck(true);

            if ($user->authorise('core.admin')) {
                $childBar->checkin('reports.checkin');
            }

            if ($this->state->get('filter.published') != -2) {
                $childBar->trash('reports.trash')->listCheck(true);
            }
        }

        if (!$this->isEmptyState && $this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
            $toolbar->delete('reports.delete', 'JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($user->authorise('core.admin', 'com_volunteers') || $user->authorise('core.options', 'com_volunteers')) {
            $toolbar->preferences('com_volunteers');
        }
    }
}
