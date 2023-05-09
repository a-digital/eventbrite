<?php
/**
 * Eventbrite plugin for Craft CMS 3.x
 *
 * Integration with Eventbrite API
 *
 * @link      https://adigital.agency/
 * @copyright Copyright (c) 2019 Mark @ A Digital
 */

namespace adigital\eventbrite\migrations;

use adigital\eventbrite\records\NonAdminSettings;
use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Eventbrite Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    A Digital
 * @package   Maintenance
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp() : bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown() : bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables() : bool
    {
        $tablesCreated = false;
    // non_admin_settings table
        $tableSchema = Craft::$app->db->schema->getTableSchema(NonAdminSettings::tableName());
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                NonAdminSettings::tableName(),
                [
                    'id' => $this->primaryKey(),
                // Custom columns in the table
                    'otherEventIds' => $this->text()->defaultValue(NULL),
                // Default columns
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
        return $tablesCreated;
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData() : void
    {
	// non_admin_settings table
	    $this->insert(NonAdminSettings::tableName(), array('otherEventIds' => ''), true);
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables() : void
    {
    // non_admin_settings table
        $this->dropTableIfExists(NonAdminSettings::tableName());
    }

}