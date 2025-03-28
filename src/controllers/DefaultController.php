<?php
/**
 * Eventbrite plugin for Craft CMS 4.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 A Digital
 */

namespace adigital\eventbrite\controllers;

use adigital\eventbrite\Eventbrite;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    A Digital
 * @package   Eventbrite
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's settings URL,
     * e.g.: actions/eventbrite/settings
     *
     * @return Response
     */
    public function actionNonAdminSettings(): Response
    {
        $nonAdminSettings = Eventbrite::$plugin->nonAdminSettings->get()->one();
        
        return $this->renderTemplate('eventbrite/non-admin-settings', [
	      'nonAdminSettings' => $nonAdminSettings
        ]);
    }
    
    public function actionSaveNonAdminSettings() : Response
    {
	  $request = Craft::$app->getRequest();
      Eventbrite::$plugin->nonAdminSettings->edit($request);
      return $this->redirectToPostedUrl();
	}
}
