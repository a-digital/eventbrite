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

use adigital\eventbrite\models\NonAdminSettings as NonAdminSettingsModel;
use adigital\eventbrite\records\NonAdminSettings as NonAdminSettingsRecord;
use craft\base\Component;
use craft\web\Request;
use yii\db\ActiveQuery;
use yii\db\Exception;

/**
 * Non-Admin Settings Service
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
class NonAdminSettings extends Component
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
     * @param $request Request
     * @return bool
     * @throws Exception
     */
    public function edit($request) : bool
    {
        $model = new NonAdminSettingsModel();
	    $model->otherEventIds = $request->getParam('otherEventIds');
        $record = NonAdminSettingsRecord::find()->where(['id' => $request->getParam('nonAdminSettingsId')])->one();
        if ($record === null) {
            $record = new NonAdminSettingsRecord();
        }
        $record->setAttributes($model->getAttributes(), false);
        return $record->save();
    }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Maintenance::$plugin->nonAdminSettings->get()
     *
     * @return ActiveQuery
     */
    public function get() : ActiveQuery
    {
        return NonAdminSettingsRecord::find();
    }

}
