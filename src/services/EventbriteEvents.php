<?php
/**
 * Eventbrite plugin for Craft CMS 3.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 Mark @ A Digital
 */

namespace adigital\eventbrite\services;

use adigital\eventbrite\Eventbrite;

use Craft;
use craft\base\Component;

/**
 * EventbriteService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Mark @ A Digital
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
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getOrganisationEvents($expansions = null, $time_filter = "current_future", $unlistedEvents = false, $status = "live")
  {
    $settings = Eventbrite::$plugin->getSettings();
    $organisationId = $settings->organisationId;
    $method = "/v3/organizations/" . $organisationId . "/events/?status=" . $status . "&time_filter=" . $time_filter;
    
    if (!empty($expansions))
    {
      $method .= $this->buildEventMethodQueryString(false, $expansions);
    }
    
    $organisationEvents = $this->curlWrap($method);
    
    if (is_array($organisationEvents['events']))
    {
      if($unlistedEvents === false)
      {
        foreach($organisationEvents['events'] AS $key => $organisationEvent) {
          if($organisationEvent['listed'] === false)
          {
            unset($organisationEvents['events'][$key]);
          }
        }
      }
    } else {
	  return array();
    }
    
    return $organisationEvents['events'];
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getOtherEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false)
  {
    $settings = Eventbrite::$plugin->getSettings();
    $nonAdminSettings = Eventbrite::$plugin->nonAdminSettings->get()->one();
    $otherEventIds = json_decode($nonAdminSettings->otherEventIds);
    $otherEvents = array();
    
    if(!empty($otherEventIds)) {
      foreach($otherEventIds AS $otherEventId) {
	    if($otherEventId[0] != "")
	    {
          $data = $this->getEvent($otherEventId[0], $expansions, false, $unlistedEvents);
          
          if(is_array($data))
          {
            if ($time_filter != "all")
            {
              $eventObj = new \DateTime($data['start']['utc']);
            }
            
            if ($time_filter == "all" || ($time_filter == "current_future" && $eventObj->getTimestamp() >= time()) || ($time_filter == "past" && $eventObj->getTimestamp() < time()))
            {
              $otherEvents[] = $data;
            }
          }
        }
      }
      
      if ($sort)
      {
        usort($otherEvents, array($this, "sortByEventDates"));
      }
    }
    
    return $otherEvents;
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getEvent($eventId, $expansions = null, $fullDescription = true, $unlistedEvent = false)
  {
    $method = "/v3/events/" . $eventId . "/";
    
    if (!empty($expansions))
    {
      $method .= $this->buildEventMethodQueryString(true, $expansions);
    }
    
    $event = $this->curlWrap($method);
    
    $otherEventIds = array_column(json_decode(Eventbrite::$plugin->nonAdminSettings->get()->one()->otherEventIds), 0);
    if (($unlistedEvent === false && $event['listed'] === false) || ($event['organization_id'] != Eventbrite::$plugin->getSettings()->organisationId && !array_search($eventId, $otherEventIds)))
    {
      $event = null;
    }
    elseif ($fullDescription)
    {
      $htmlDescription = $this->getEventDescription($eventId);
	    $event['htmlDescription'] = $htmlDescription;
    }
    
    return $event;
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  private function getEventDescription($eventId)
  {
    $method = "/v3/events/" . $eventId . "/description/";
    
    $eventDescription = $this->curlWrap($method);
    
    return $eventDescription;
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getAllEvents($expansions = null, $sort = true, $time_filter = "current_future", $unlistedEvents = false, $status = "live")
  {
    $organisationEvents = $this->getOrganisationEvents($expansions, $time_filter, $unlistedEvents, $status);
    $otherEvents = $this->getOtherEvents($expansions, false, $time_filter, $unlistedEvents);
    $combinedEvents = array_merge($organisationEvents, $otherEvents);
    
    if ($sort && ((count($organisationEvents) > 0 && count($otherEvents) > 0) || count($otherEvents) > 1))
    {
      usort($combinedEvents, array($this, "sortByEventDates"));
    }
    
    return $combinedEvents;
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getOrganizationVenues()
  {
    $settings = Eventbrite::$plugin->getSettings();
    $organisationId = $settings->organisationId;
    $method = "/v3/organizations/" . $organisationId . "/venues/";
    	
    $organisationVenues = $this->curlWrap($method);
    
    return $organisationVenues;
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getVenue($venueId)
  {
    $method = "/v3/venues/" . $venueId . "/";
    
    $venue = $this->curlWrap($method);
    
    return $venue;
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  public function getEventsByVenue($venueId, $expansions = null, $unlistedEvents = false, $status = "live", $onlyPublic = "true")
  {
    $method = "/v3/venues/" . $venueId . "/events/?status=" . $status . "&only_public=" . $onlyPublic;
    
    if (!empty($expansions))
    {
      $method .= $this->buildEventMethodQueryString(false, $expansions);
    }
    
    $venueEvents = $this->curlWrap($method);
    
    if($unlistedEvents === false)
    {
      foreach($venueEvents['events'] AS $key => $venueEvent) {
        if($venueEvent['listed'] === false)
        {
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
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  private function sortByEventDates($event1, $event2)
  {
    $event1DateTime = new \DateTime($event1['start']['utc']);
    $event2DateTime = new \DateTime($event2['start']['utc']);
    return $event1DateTime->getTimestamp() - $event2DateTime->getTimestamp();
  }

  /**
   * This function can literally be anything you want, and you can have as many service
   * functions as you want
   *
   * From any other plugin file, call it like this:
   *
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  private function buildEventMethodQueryString($startOfQueryString, $expansions)
  {
	$queryString = "";
	
	if ($startOfQueryString)
	{
	  $queryString .= "?";
	}
	else
	{
	  $queryString .= "&";
	}
	
    $queryString .= "expand=";
    
    foreach($expansions AS $expansion)
    {
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
   *     Eventbrite::$plugin->eventbriteService->exampleService()
   *
   * @return mixed
   */
  private function curlWrap($method, $request = null)
  {
    $settings = Eventbrite::$plugin->getSettings();
    $authToken = $settings->authToken;
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
    if (array_key_exists("error", $decoded)) {
      if (getenv('ENVIRONMENT') == 'dev' || getenv('ENVIRONMENT') == 'staging') {
        print ("<p>Error " . $decoded['status_code'] . " retrieving data from Eventbrite: " . $decoded['error_description'] . "</p>");
      }
      return;
    }
    return $decoded;
  }

}
