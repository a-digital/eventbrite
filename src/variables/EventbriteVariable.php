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
  public function allEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false)
  {
    return Eventbrite::$plugin->eventbriteEvents->getAllEvents($expansions, $sort, $time_filter, $unlistedEvents);
  }

  public function organisationEvents($expansions = null, $time_filter = "current_future", $unlistedEvents = false)
  {
    return Eventbrite::$plugin->eventbriteEvents->getOrganisationEvents($expansions, $time_filter, $unlistedEvents);
  }

  public function otherEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false)
  {
    return Eventbrite::$plugin->eventbriteEvents->getOtherEvents($expansions, $sort, $time_filter, $unlistedEvents);
  }

  public function eventById($eventId, $expansions = null, $fullDescription = true, $unlistedEvent = false)
  {
    return Eventbrite::$plugin->eventbriteEvents->getEvent($eventId, $expansions, $fullDescription, $unlistedEvent);
  }

  public function organisationVenues()
  {
    return Eventbrite::$plugin->eventbriteEvents->getOrganizationVenues();
  }

  public function venueById($venueId)
  {
    return Eventbrite::$plugin->eventbriteEvents->getVenue($venueId);
  }

  public function venueEvents($venueId, $expansions = null, $unlistedEvents = false)
  {
    return Eventbrite::$plugin->eventbriteEvents->getEventsByVenue($venueId, $expansions, $unlistedEvents);
  }
}
