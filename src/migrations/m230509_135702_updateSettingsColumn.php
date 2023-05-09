<?php

namespace adigital\eventbrite\migrations;

use adigital\eventbrite\Eventbrite;

use adigital\eventbrite\records\NonAdminSettings;
use craft\db\Migration;

/**
 * m230509_135702_updateSettingsColumn migration.
 */
class m230509_135702_updateSettingsColumn extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->alterColumn(NonAdminSettings::tableName(), 'otherEventIds', $this->text()->defaultValue(NULL));
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m230509_135702_updateSettingsColumn cannot be reverted.\n";
        return false;
    }
}