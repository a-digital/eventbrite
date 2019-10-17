# Eventbrite plugin for Craft CMS 3.x

Integration with Eventbrite API to read information and return it in a variable.

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require eventbrite/eventbrite

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Eventbrite.

## Eventbrite Overview



## Configuring Eventbrite

The following screenshot of the plugin settings page gives an indication of how it works - you need to provide an Eventbrite API key and organisation ID, and these settings are only editable by admins on environments where the `allowAdminChanges` setting is enabled.

<img width="1037" alt="Craft CMS Eventbrite Plugin Admin Settings" src="https://user-images.githubusercontent.com/30312669/66996260-557ea780-f0c8-11e9-9d59-f8b2b46adca0.png">

There's also a settings page which can be accessed by non-admin accounts with the right permission in environments regardless of the `allowAdminChanges` setting. This page enables users to optionally specify additional event IDs not set up by their organisation to return along with their organisation's events. See screenshots of this settings page and permission below:

<img width="1042" alt="Craft CMS Eventbrite Plugin User Settings" src="https://user-images.githubusercontent.com/30312669/66996261-557ea780-f0c8-11e9-853d-4434a1bf6e10.png">


<img width="207" alt="Screenshot 2019-10-17 at 10 46 24" src="https://user-images.githubusercontent.com/30312669/66998131-9deb9480-f0cb-11e9-99f4-f53eba9fbc27.png">

## Using Eventbrite

### The Plugin Variable

Once configured, data is returned through the plugin variable using the following methods:

`craft.eventbrite.allEvents([expansions] = null, sort = true, time_filter = "current_future", unlistedEvents = false)`

This method returns all [events] (https://www.eventbrite.com/platform/api#/reference/event/retrieve-an-event) planned by the organisation and specified by the additional event IDs setting. The `expansions` parameter corresponds to [additional models that can be returned when querying data] (https://www.eventbrite.com/platform/api#/introduction/expansions) from the API. You can see the [expansions that are available for events in the documentation] (https://www.eventbrite.com/platform/api#/reference/event). The `sort` parameter will sort events by date by default when organisation and other events are being combined. The `time_filter` parameter defaults to current and future events, but can also be `past` or `all`. The `unlistedEvents` parameter can be set to `true` if you want to return unlisted events.

`craft.eventbrite.organisationEvents([expansions] = null, time_filter = "current_future", unlistedEvents = false)`

This method returns only [events] (https://www.eventbrite.com/platform/api#/reference/event/retrieve-an-event) planned by the organisation, with the parameters working in the same way as the `allEvents` method.

`craft.eventbrite.otherEvents([expansions] = null, sort = true, time_filter = "current_future", unlistedEvents = false)`

This method returns only [events] (https://www.eventbrite.com/platform/api#/reference/event/retrieve-an-event) specified by the 'Other Event Ids', with the parameters working in the same way as the `allEvents` method.

`craft.eventbrite.eventById(eventId, [expansions] = null, unlistedEvent = false)`

This method returned a single [events] (https://www.eventbrite.com/platform/api#/reference/event/retrieve-an-event) by its ID, which should be the numeric ID for the event in Eventbrite.

`organisationVenues()`

This method returns all the [venues] (https://www.eventbrite.com/platform/api#/reference/venue/retrieve-an-event) belonging to the organisation specified in the plugin settings.

`venueById(venueId)`

This method returns a [venue] (https://www.eventbrite.com/platform/api#/reference/venue/retrieve-an-event) by its ID, which should be the numeric ID for the venue in Eventbrite.

`venueEvents(venueId, [expansions] = null, unlistedEvents = false)`

This method returns only [events] (https://www.eventbrite.com/platform/api#/reference/event/retrieve-an-event) held at the [venue] (https://www.eventbrite.com/platform/api#/reference/venue/retrieve-an-event) specified by the `venueId` parameter, with the other parameters working in the same way as the `allEvents` method.

### Caching

As the plugin reads live data from the Eventbrite API, you should wrap any use of the variable in your templates within [Craft `cache` tags] (https://docs.craftcms.com/v3/dev/tags/cache.html) to reduce your calls to the API and improve page load times.

## Eventbrite Roadmap

Things to do, and ideas for potential features:

* Add methods to write back to Eventbrite so purchasing of tickets can be completed without going offsite
* Explore use of other API methods to expand data returned and possibly add more event stats to Craft


Brought to you by [Mark @ A Digital](https://adigital.agency/)
