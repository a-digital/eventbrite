<?php
/**
 * Eventbrite plugin for Craft CMS 4.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 A Digital
 */

namespace adigital\eventbrite\models;

use craft\base\Model;

/**
 * Eventbrite Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    A Digital
 * @package   Eventbrite
 * @since     1.0.0
 */
class Settings extends Model
{
  // Public Properties
  // =========================================================================

  /** @var string $authToken */
  public $authToken = '';

  /** @var string $organisationId */
  public $organisationId = '';

  // Public Methods
  // =========================================================================

  /**
   * Returns the validation rules for attributes.
   *
   * Validation rules are used by [[validate()]] to check if attribute values are valid.
   * Child classes may override this method to declare different validation rules.
   *
   * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
   *
   * @return array
   */
  public function rules() : array
  {
    return [
      ['authToken', 'string'],
      ['authToken', 'required'],
      ['authToken', 'default', 'value' => ''],
      ['organisationId', 'string'],
      ['organisationId', 'required'],
      ['organisationId', 'default', 'value' => ''],
    ];
  }

    public function authToken(): string
    {
        if (str_starts_with($this->authToken, '$')) {
            return getenv(substr($this->authToken, 1)) ?: '';
        } else {
            return $this->authToken;
        }
    }

    public function organisationId(): string
    {
        if (str_starts_with($this->organisationId, '$')) {
            return getenv(substr($this->organisationId, 1)) ?: '';
        } else {
            return $this->organisationId;
        }
    }
}
