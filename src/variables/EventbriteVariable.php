<?php
/**
 * Eventbrite plugin for Craft CMS 3.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 Mark @ A Digital
 */

namespace adigital\eventbrite\variables;

use adigital\eventbrite\Eventbrite;
use adigital\eventbrite\EventbriteEvents;

use Craft;

/**
 * Eventbrite Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.eventbrite }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Mark @ A Digital
 * @package   Eventbrite
 * @since     1.0.0
 */
class EventbriteVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.eventbrite.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.eventbrite.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function allEvents()
    {
        return Eventbrite::$plugin->eventbriteEvents->getAllEvents();
    }
    
    public function eventById($eventId)
    {
        return Eventbrite::$plugin->eventbriteEvents->getEvent($eventId);
    }
}
