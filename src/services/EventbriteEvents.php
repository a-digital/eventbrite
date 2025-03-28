<?php
/**
 * Eventbrite plugin for Craft CMS 4.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 A Digital
 */

namespace adigital\eventbrite\services;

use adigital\eventbrite\Eventbrite;

use craft\base\Component;
use DateTime;

/**
 * EventbriteEvents Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    A Digital
 * @package   Eventbrite
 * @since     1.0.0
 */
class EventbriteEvents extends Component
{
  // Public Methods
  // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getOrganisationEvents()
     *
     * @param null $expansions
     * @param string $time_filter
     * @param bool $unlistedEvents
     * @param string $status
     * @return array
     */
  public function getOrganisationEvents($expansions = null, $time_filter = "current_future", $unlistedEvents = false, $status = "live") : array
  {
    $settings = Eventbrite::$plugin->getSettings();
    $organisationId = $settings->organisationId();
    $method = "/v3/organizations/" . $organisationId . "/events/?status=" . $status . "&time_filter=" . $time_filter;
    
    if (!empty($expansions)) {
      $method .= $this->buildEventMethodQueryString(false, $expansions);
    }
    
    $organisationEvents = $this->curlWrap($method);
    
    if (!is_array($organisationEvents['events'])) {
      return array();
    }

    if ($unlistedEvents === false) {
      foreach ($organisationEvents['events'] AS $key => $organisationEvent) {
        if ($organisationEvent['listed'] === false) {
          unset($organisationEvents['events'][$key]);
        }
      }
    }
    
    return $organisationEvents['events'];
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getOtherEvents()
     *
     * @param null $expansions
     * @param bool $sort
     * @param string $time_filter
     * @param bool $unlistedEvents
     * @return array
     */
  public function getOtherEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false) : array
  {
    $nonAdminSettings = Eventbrite::$plugin->nonAdminSettings->get()->one();
    $otherEventIds = json_decode($nonAdminSettings->otherEventIds);
    $otherEvents = array();
    
    if (empty($otherEventIds)) {
        return $otherEvents;
    }

    foreach ($otherEventIds AS $otherEventId) {
	  if ($otherEventId[0] == "") {
        continue;
      }

      $data = $this->getEvent($otherEventId[0], $expansions, false, $unlistedEvents);
      if (!count($data)) {
          continue;
      }

      if ($time_filter == "all") {
        $otherEvents[] = $data;
      } else {
        $eventObj = new DateTime($data['start']['utc']);

        if (($time_filter == "current_future" && $eventObj->getTimestamp() >= time()) || ($time_filter == "past" && $eventObj->getTimestamp() < time())) {
          $otherEvents[] = $data;
        }
      }
    }
      
    if ($sort) {
      usort($otherEvents, array($this, "sortByEventDates"));
    }
    
    return $otherEvents;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getEvent()
     *
     * @param $eventId
     * @param null $expansions
     * @param bool $fullDescription
     * @param bool $unlistedEvent
     * @return array
     */
  public function getEvent($eventId, $expansions = null, $fullDescription = true, $unlistedEvent = false) : array
  {
    $method = "/v3/events/" . $eventId . "/";
    
    if (!empty($expansions)) {
      $method .= $this->buildEventMethodQueryString(true, $expansions);
    }
    
    $event = $this->curlWrap($method);
    
    $otherEventIds = array_column(json_decode(Eventbrite::$plugin->nonAdminSettings->get()->one()->otherEventIds), 0);
    if (($unlistedEvent === false && $event['listed'] === false) || ($event['organization_id'] != Eventbrite::$plugin->getSettings()->organisationId() && !array_search($eventId, $otherEventIds))) {
      $event = array();
    } elseif ($fullDescription) {
      $htmlDescription = $this->getEventDescription($eventId);
	  $event['htmlDescription'] = $htmlDescription;
    }
    if (!is_array($event)) {
        return array();
    }
    
    return $event;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getEventDescription()
     *
     * @param $eventId
     * @return array
     */
  private function getEventDescription($eventId) : array
  {
    $method = "/v3/events/" . $eventId . "/description/";
    
    $eventDescription = $this->curlWrap($method) ?: [];
    
    return $eventDescription;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getAllEvents()
     *
     * @param null $expansions
     * @param bool $sort
     * @param string $time_filter
     * @param bool $unlistedEvents
     * @param string $status
     * @return array
     */
  public function getAllEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false, $status = "live") : array
  {
    $organisationEvents = $this->getOrganisationEvents($expansions, $time_filter, $unlistedEvents, $status);
    $otherEvents = $this->getOtherEvents($expansions, false, $time_filter, $unlistedEvents);
    $combinedEvents = array_merge($organisationEvents, $otherEvents);
    
    if ($sort && ((count($organisationEvents) > 0 && count($otherEvents) > 0) || count($otherEvents) > 1)) {
      usort($combinedEvents, array($this, "sortByEventDates"));
    }

    if (!is_array($combinedEvents)) {
        return array();
    }
    
    return $combinedEvents;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getOrganizationVenues()
     *
     * @return array
     */
  public function getOrganizationVenues() : array
  {
    $settings = Eventbrite::$plugin->getSettings();
    $organisationId = $settings->organisationId();
    $method = "/v3/organizations/" . $organisationId . "/venues/";
    	
    $organisationVenues = $this->curlWrap($method) ?: [];
    
    return $organisationVenues;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getVenue()
     *
     * @param $venueId
     * @return array
     */
  public function getVenue($venueId) : array
  {
    $method = "/v3/venues/" . $venueId . "/";
    
    $venue = $this->curlWrap($method) ?: [];
    
    return $venue;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->getEventsByVenue()
     *
     * @param $venueId
     * @param null $expansions
     * @param bool $unlistedEvents
     * @param string $status
     * @param string $onlyPublic
     * @return array
     */
  public function getEventsByVenue($venueId, $expansions = null, $unlistedEvents = false, $status = "live", $onlyPublic = "true") : array
  {
    $method = "/v3/venues/" . $venueId . "/events/?status=" . $status . "&only_public=" . $onlyPublic;
    
    if (!empty($expansions)) {
      $method .= $this->buildEventMethodQueryString(false, $expansions);
    }
    
    $venueEvents = $this->curlWrap($method) ?: [];
    
    if ($unlistedEvents === false && array_key_exists('events', $venueEvents)) {
      foreach($venueEvents['events'] AS $key => $venueEvent) {
        if ($venueEvent['listed'] === false) {
          unset($venueEvents['events'][$key]);
        }
      }
    }
    
    return $venueEvents;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->sortByEventDates()
     *
     * @param $event1
     * @param $event2
     * @return int
     */
  private function sortByEventDates($event1, $event2) : int
  {
    $event1DateTime = new DateTime($event1['start']['utc']);
    $event2DateTime = new DateTime($event2['start']['utc']);
    return $event1DateTime->getTimestamp() - $event2DateTime->getTimestamp();
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->buildEventMethodQueryString()
     *
     * @param $startOfQueryString
     * @param $expansions
     * @return string
     */
  private function buildEventMethodQueryString($startOfQueryString, $expansions) : string
  {
	$queryString = "";
	
	if ($startOfQueryString) {
	  $queryString .= "?";
	} else {
	  $queryString .= "&";
	}
	
    $queryString .= "expand=";
    
    foreach ($expansions AS $expansion) {
      $queryString .= $expansion.",";
    }
    
    $queryString = rtrim($queryString, ",");
    
    return $queryString;
  }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Eventbrite::$plugin->eventbriteEvents->curlWrap()
     *
     * @param $method
     * @param null $request
     * @return array
     */
  private function curlWrap($method, $request = null) : array
  {
    $settings = Eventbrite::$plugin->getSettings();
    $authToken = $settings->authToken();
    $host = 'www.eventbriteapi.com';
    $headers = [
      'Host: '.$host,
      'Authorization: Bearer '.$authToken,
      'Accept: application/json'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://'.$host.'/'.$method);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    if ($request !== null) {
      curl_setopt($ch, CURLOPT_POST,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
      $headers[] = 'Content-Type: application/json';
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($output, true);
    if (!is_array($decoded)) {
        return array();
    }
    if (array_key_exists("error", $decoded)) {
      if (getenv('ENVIRONMENT') == 'dev' || getenv('ENVIRONMENT') == 'staging') {
        print ("<p>Error " . $decoded['status_code'] . " retrieving data from Eventbrite: " . $decoded['error_description'] . "</p>");
      }
      return array();
    }
    return $decoded;
  }

}
