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
    public function allEvents($include_category = false, $include_venue = false, $sort = true, $time_filter = "current_future")
    {
        return Eventbrite::$plugin->eventbriteEvents->getAllEvents($sort, $time_filter);
    }
    
    public function organisationEvents($include_category = false, $include_venue = false, $time_filter = "current_future")
    {
        return Eventbrite::$plugin->eventbriteEvents->getOrganisationEvents($include_category, $include_venue, $time_filter)['events'];
    }
    
    public function otherEvents($include_category = false, $include_venue = false, $sort = true, $time_filter = "current_future")
    {
        return Eventbrite::$plugin->eventbriteEvents->getOtherEvents($include_category, $include_venue, $sort, $time_filter);
    }
    
    public function eventById($eventId, $include_category = false, $include_venue = false)
    {
        return Eventbrite::$plugin->eventbriteEvents->getEvent($eventId, $include_category, $include_venue);
    }
}
