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
    public function getOrganisationEvents($include_category = false, $include_venue = false, $time_filter = "current_future")
    {
	    $settings = Eventbrite::$plugin->getSettings();
	    $organisationId = $settings->organisationId;
	    $method = "/v3/organizations/" . $organisationId . "/events/";
	    
	    if ($include_category || $include_venue || $time_filter != "all")
	    {
		  $method .= $this->buildEventMethodQueryString($method, $include_category, $include_venue, $time_filter);
	    }
	    
	    $organisationEvents = $this->curlWrap($method);
	    
	    return $organisationEvents;
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
    public function getOtherEvents($include_category = false, $include_venue = false, $sort = true, $time_filter = "current_future")
    {
	    $settings = Eventbrite::$plugin->getSettings();
	    $otherEventIds = $settings->otherEventIds;
	    $otherEvents = array();
	    
	    foreach($otherEventIds AS $otherEventId) {
		    $data = $this->getEvent($otherEventId[0], $include_category, $include_venue);
		    
		    if ($time_filter != "all")
		    {
			    $eventObj = new \DateTime($data['start']['utc']);
		    }
		    
		    if ($time_filter == "all" || ($time_filter == "current_future" && $eventObj->getTimestamp() >= time()) || ($time_filter == "past" && $eventObj->getTimestamp() < time()))
		    {
			    $otherEvents[] = $data;
		    }
	    }
	    
	    if ($sort)
	    {
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
     *     Eventbrite::$plugin->eventbriteService->exampleService()
     *
     * @return mixed
     */
    public function getEvent($eventId, $include_category = false, $include_venue = false)
    {
	    $method = "/v3/events/" . $eventId . "/";
	    
	    if ($include_category || $include_venue)
	    {
		  $method = $this->buildEventMethodQueryString($method, $include_category, $include_venue);
	    }
	    
		$event = $this->curlWrap($method);
	    
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
    public function getAllEvents($include_category = false, $include_venue = false, $sort = true, $time_filter = "current_future")
    {
	    $organisationEvents = $this->getOrganisationEvents($include_category, $include_venue, $time_filter);
	    $otherEvents = $this->getOtherEvents($include_cateogory, $include_venue, false, $time_filter);
	    $combinedEvents = array_merge($organisationEvents['events'], $otherEvents);
	    
	    if ($sort && (count($organisationEvents['events']) > 0 && count($otherEvents) > 0))
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
    static function sortByEventDates($event1, $event2)
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
    static function buildEventMethodQueryString($method, $include_category, $include_venue, $time_filter = false)
    {
	    $method .= "?";
	    
	    if ($include_category || $include_venue)
	    {
		    $method .= "expand=";
		    
		    if ($include_category)
		    {
			    $method .= "category" . ( $include_venue ? "," : "" );
		    }
		    
		    if ($include_venue)
		    {
			    $method .= "venue";
		    }
		    
		    if ($time_filter)
		    {
			    $method .= "&";
		    }
	    }
	    
	    if ($time_filter)
	    {
		    $method .= "time_filter=" . $time_filter;
	    }
	    
	    return $method;
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
	        print ("<p>Error " . $decoded['status_code'] . " retrieving data from Eventbrite: " . $decoded['error_description'] . "</p>");
	        return;
        }
        return $decoded;
    }

}
