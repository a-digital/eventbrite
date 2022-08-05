<?php
/**
 * Eventbrite plugin for Craft CMS 3.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 Mark @ A Digital
 */

namespace adigital\eventbrite\widgets;

use adigital\eventbrite\Eventbrite;
use adigital\eventbrite\assetbundles\eventbritewidgetwidget\EventbriteWidgetWidgetAsset;

use Craft;
use craft\base\Widget;

/**
 * Eventbrite Widget
 *
 * Dashboard widgets allow you to display information in the Admin CP Dashboard.
 * Adding new types of widgets to the dashboard couldn’t be easier in Craft
 *
 * https://craftcms.com/docs/plugins/widgets
 *
 * @author    Mark @ A Digital
 * @package   Eventbrite
 * @since     1.0.0
 */
class EventbriteWidget extends Widget
{

  // Static Methods
  // =========================================================================

  /**
   * Returns the display name of this class.
   *
   * @return string The display name of this class.
   */
  public static function displayName(): string
  {
    return Craft::t('eventbrite', 'Upcoming Events');
  }

  /**
   * Returns the path to the widget’s SVG icon.
   *
   * @return string|null The path to the widget’s SVG icon
   */
  public static function iconPath() : string
  {
    return Craft::getAlias("@adigital/eventbrite/assetbundles/eventbritewidgetwidget/dist/img/EventbriteWidget-icon.svg");
  }

  /**
   * Returns the widget’s maximum colspan.
   *
   * @return int|null The widget’s maximum colspan, if it has one
   */
  public static function maxColspan() : int|null
  {
    return null;
  }

  // Public Methods
  // =========================================================================

  /**
   * Returns the widget's body HTML.
   *
   * @return string|false The widget’s body HTML, or `false` if the widget
   *                      should not be visible. (If you don’t want the widget
   *                      to be selectable in the first place, use {@link isSelectable()}.)
   */
  public function getBodyHtml() : string
  {
    Craft::$app->getView()->registerAssetBundle(EventbriteWidgetWidgetAsset::class);

    return Craft::$app->getView()->renderTemplate(
      'eventbrite/_components/widgets/EventbriteWidget_body'
    );
  }
}
