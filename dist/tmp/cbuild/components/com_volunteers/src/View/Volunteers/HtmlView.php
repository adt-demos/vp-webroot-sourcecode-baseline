<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Volunteers;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Volunteers\Site\Model\VolunteersModel;

/**
 * View class for a list of volunteers.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected array $items;

    /**
     * @var Pagination
     *
     * @since 4.0.0
     */
    protected Pagination $pagination;

    protected mixed $state;


    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function display($tpl = null): void
    {
        /** @var VolunteersModel $model */

        $model       = $this->getModel();
        $this->state = $model->getState();
        $this->items = $model->getItems();

        $this->pagination = $model->getPagination();


        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }


        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document.
     *
     * @return  void.
     * @since 4.0.0
     */
    protected function prepareDocument(): void
    {
        // Prepare variables
        $title   = Text::_('COM_VOLUNTEERS_TITLE_VOLUNTEERS');
        $image   = 'https://cdn.joomla.org/images/joomla-org-og.jpg';
        $itemURL = Route::_('index.php?option=com_volunteers&view=volunteers');
        $url     = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;

        // Set meta
        $this->getDocument()->
        setTitle($title);

        // Twitter Card metadata
        $this->getDocument()->
        setMetaData('twitter:title', $title);
        $this->getDocument()->
        setMetaData('twitter:image', $image);

        // OpenGraph metadata
        $this->getDocument()->
        setMetaData('og:title', $title, 'property');
        $this->getDocument()->
        setMetaData('og:image', $image, 'property');
        $this->getDocument()->
        setMetaData('og:type', 'article', 'property');
        $this->getDocument()->
        setMetaData('og:url', $url, 'property');
    }
}
