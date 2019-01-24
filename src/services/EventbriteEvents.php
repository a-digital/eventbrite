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
    public function getOrganisationEvents()
    {
	    $settings = Eventbrite::$plugin->getSettings();
	    $organisationId = $settings->organisationId;
	    $method = "/v3/organizations/" . $organisationId . "/events/";
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
    public function getOtherEvents()
    {
	    $settings = Eventbrite::$plugin->getSettings();
	    $otherEventIds = $settings->otherEventIds;
	    $otherEvents = array();
	    
	    foreach($otherEventIds AS $otherEventId) {
		    $data = $this->getEvent($otherEventId[0]);
		    $otherEvents[] = $data;
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
    public function getEvent($eventId)
    {
	    $method = "/v3/events/" . $eventId . "/";
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
    public function getAllEvents()
    {
	    $organisationEvents = $this->getOrganisationEvents();
	    $otherEvents = $this->getOtherEvents();
	    $combinedEvents = array_merge($organisationEvents->events, $otherEvents);
	    
	    if (count($organisationEvents->events) > 0 && count($otherEvents) > 0)
	    {
		    usort($combinedEvents, array($this, "compareOtherEventDates"));
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
    static function compareOtherEventDates($event1, $event2)
    {
	    $event1DateTime = new \DateTime($event1->start->utc);
	    $event2DateTime = new \DateTime($event2->start->utc);
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
    private function curlWrap($method, $request = null)
    {
        $settings = Eventbrite::$plugin->getSettings();
        $authToken = $settings->authToken;
        $authToken = "fsdfsdf";
        $host = 'www.eventbriteapi.com';
	    $headers = [
            'Host: '.$host,
            'Authorization: Bearer '.$authToken,
            'Content-Type: application/json',
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($request !== null) {
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        }
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
