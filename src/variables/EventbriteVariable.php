<?php
/**
 * Eventbrite plugin for Craft CMS 4.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 A Digital
 */

namespace adigital\eventbrite\variables;

use adigital\eventbrite\Eventbrite;

/**
 * Eventbrite Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.eventbrite }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    A Digital
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
     *     {{ craft.eventbrite.allEvents }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.eventbrite.allEvents(twigValue) }}
     *
     * @param null $expansions
     * @param bool $sort
     * @param string $time_filter
     * @param bool $unlistedEvents
     * @param string $status
     * @return array
     */
  public function allEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false, $status = "live") : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getAllEvents($expansions, $sort, $time_filter, $unlistedEvents, $status);
  }

  public function organisationEvents($expansions = null, $time_filter = "current_future", $unlistedEvents = false, $status = "live") : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getOrganisationEvents($expansions, $time_filter, $unlistedEvents, $status);
  }

  public function otherEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false) : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getOtherEvents($expansions, $sort, $time_filter, $unlistedEvents);
  }

  public function eventById($eventId, $expansions = null, $fullDescription = true, $unlistedEvent = false) : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getEvent($eventId, $expansions, $fullDescription, $unlistedEvent);
  }

  public function organisationVenues() : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getOrganizationVenues();
  }

  public function venueById($venueId) : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getVenue($venueId);
  }

  public function venueEvents($venueId, $expansions = null, $unlistedEvents = false, $status = "live", $onlyPublic = "true") : array
  {
    return Eventbrite::$plugin->eventbriteEvents->getEventsByVenue($venueId, $expansions, $unlistedEvents, $status, $onlyPublic);
  }
}
