<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */
class CRM_Upgrade_Incremental_php_FourFour {
  const BATCH_SIZE = 5000;

  function verifyPreDBstate(&$errors) {
    return TRUE;
  }

  /**
   * Compute any messages which should be displayed beforeupgrade
   *
   * Note: This function is called iteratively for each upcoming
   * revision to the database.
   *
   * @param $postUpgradeMessage string, alterable
   * @param $rev string, a version number, e.g. '4.4.alpha1', '4.4.beta3', '4.4.0'
   * @return void
   */
  function setPreUpgradeMessage(&$preUpgradeMessage, $rev, $currentVer = NULL) {
  }

  /**
   * Compute any messages which should be displayed after upgrade
   *
   * @param $postUpgradeMessage string, alterable
   * @param $rev string, an intermediate version; note that setPostUpgradeMessage is called repeatedly with different $revs
   * @return void
   */
  function setPostUpgradeMessage(&$postUpgradeMessage, $rev) {
    if ($rev == '4.4.1') {
      $config = CRM_Core_Config::singleton();
      if (!empty($config->useIDS)) {
        $postUpgradeMessage .= '<br />' . ts("The setting to skip IDS check has been removed. Your site has this configured in civicrm.settings.php but it will no longer work. Instead, use the new permission 'skip IDS check' to bypass the IDS system.");
      }
    }
  }

  function upgrade_4_4_alpha1($rev) {
    // task to process sql
    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => '4.4.alpha1')), 'task_4_4_x_runSql', $rev);

    // Consolidate activity contacts CRM-12274.
    $this->addTask('Consolidate activity contacts', 'activityContacts');

    return TRUE;
  }

  function upgrade_4_4_beta1($rev) {
    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => '4.4.beta1')), 'task_4_4_x_runSql', $rev);

    // add new 'data' column in civicrm_batch
    $query = 'ALTER TABLE civicrm_batch ADD data LONGTEXT NULL COMMENT "cache entered data"';
    CRM_Core_DAO::executeQuery($query, array(), TRUE, NULL, FALSE, FALSE);

    // check if batch entry data exists in civicrm_cache table
    $query = 'SELECT path, data FROM civicrm_cache WHERE group_name = "batch entry"';
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      // get batch id $batchId[2]
      $batchId = explode('-', $dao->path);
      $data = unserialize($dao->data);

      // move the data to civicrm_batch table
      CRM_Core_DAO::setFieldValue('CRM_Batch_DAO_Batch', $batchId[2], 'data', json_encode(array('values' => $data)));
    }

    // delete entries from civicrm_cache table
    $query = 'DELETE FROM civicrm_cache WHERE group_name = "batch entry"';
    CRM_Core_DAO::executeQuery($query);

    $this->addTask('Migrate custom word-replacements', 'wordReplacements');
  }

  function upgrade_4_4_1($rev) {
    $config = CRM_Core_Config::singleton();
    // CRM-13327 upgrade handling for the newly added name badges
    $ogID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'name_badge', 'id', 'name');
    $nameBadges = array_flip(array_values(CRM_Core_BAO_OptionValue::getOptionValuesAssocArrayFromName('name_badge')));
    unset($nameBadges['Avery 5395']);
    if (!empty($nameBadges)) {
      $dimension = '{"paper-size":"a4","orientation":"portrait","font-name":"times","font-size":6,"font-style":"","NX":2,"NY":4,"metric":"mm","lMargin":6,"tMargin":19,"SpaceX":0,"SpaceY":0,"width":100,"height":65,"lPadding":0,"tPadding":0}';
      $query = "UPDATE civicrm_option_value
        SET value = '{$dimension}'
        WHERE option_group_id = %1 AND name = 'Fattorini Name Badge 100x65'";

      CRM_Core_DAO::executeQuery($query, array(1 => array($ogID, 'Integer')));
    }
    else {
      $dimensions = array(
        1 => '{"paper-size":"a4","orientation":"landscape","font-name":"times","font-size":6,"font-style":"","NX":2,"NY":1,"metric":"mm","lMargin":25,"tMargin":27,"SpaceX":0,"SpaceY":35,"width":106,"height":150,"lPadding":5,"tPadding":5}',
        2 => '{"paper-size":"a4","orientation":"portrait","font-name":"times","font-size":6,"font-style":"","NX":2,"NY":4,"metric":"mm","lMargin":6,"tMargin":19,"SpaceX":0,"SpaceY":0,"width":100,"height":65,"lPadding":0,"tPadding":0}',
        3 => '{"paper-size":"a4","orientation":"portrait","font-name":"times","font-size":6,"font-style":"","NX":2,"NY":2,"metric":"mm","lMargin":10,"tMargin":28,"SpaceX":0,"SpaceY":0,"width":96,"height":121,"lPadding":5,"tPadding":5}',
      );
      $insertStatements = array(
        1 => "($ogID, %1, '{$dimensions[1]}', %1, NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL)",
        2 => "($ogID, %2, '{$dimensions[2]}', %2, NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL)",
        3 => "($ogID, %3, '{$dimensions[3]}', %3, NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL)",
      );

      $queryParams = array(
        1 => array('A6 Badge Portrait 150x106', 'String'),
        2 => array('Fattorini Name Badge 100x65', 'String'),
        3 => array('Hanging Badge 3-3/4" x 4-3"/4', 'String'),
      );

      foreach ($insertStatements as $values) {
        $query = 'INSERT INTO civicrm_option_value (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`, `component_id`, `visibility_id`) VALUES' . $values;
        CRM_Core_DAO::executeQuery($query, $queryParams);
      }
    }

    // CRM-12578 - Prior to this version a CSS file under drupal would disable core css
    if (!empty($config->customCSSURL) && strpos($config->userFramework, 'Drupal') === 0) {
      // The new setting doesn't exist yet - need to create it first
      CRM_Core_BAO_Setting::updateSettingsFromMetaData();
      CRM_Core_BAO_Setting::setItem('1', CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME, 'disable_core_css');
    }

    // CRM-13701 - Fix $config->timeInputFormat
    $sql = "
      SELECT time_format
      FROM   civicrm_preferences_date
      WHERE  time_format IS NOT NULL
      AND    time_format <> ''
      LIMIT  1
    ";
    $timeInputFormat = CRM_Core_DAO::singleValueQuery($sql);
    if ($timeInputFormat && $timeInputFormat != $config->timeInputFormat) {
      $params = array('timeInputFormat' => $timeInputFormat);
      CRM_Core_BAO_ConfigSetting::add($params);
    }

    // CRM-13698 - add 'Available' and 'No-show' activity statuses
    $insertStatus = array();
    $nsinc = $avinc = $inc = 0;
    if (!CRM_Core_OptionGroup::getValue('activity_status', 'Available', 'name')) {
      $insertStatus[] = "(%1, 'Available', %2, 'Available',  NULL, 0, NULL, %3, 0, 0, 1, NULL, NULL)";
      $avinc = $inc = 1;
    }
    if (!CRM_Core_OptionGroup::getValue('activity_status', 'No_show', 'name')) {
      $insertStatus[] = "(%1, 'No-show', %4, 'No_show',  NULL, 0, NULL, %5, 0, 0, 1, NULL, NULL)";
      $nsinc = $inc + 1;
    }
    if (!empty($insertStatus)) {
      $acOptionGroupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'activity_status', 'id', 'name');
      $maxVal = CRM_Core_DAO::singleValueQuery("SELECT MAX(ROUND(op.value)) FROM civicrm_option_value op WHERE op.option_group_id  = $acOptionGroupID");
      $maxWeight = CRM_Core_DAO::singleValueQuery("SELECT MAX(weight) FROM civicrm_option_value WHERE option_group_id = $acOptionGroupID");

      $p[1] = array($acOptionGroupID, 'Integer');
      if ($avinc) {
        $p[2] = array($avinc+$maxVal, 'Integer');
        $p[3] = array($avinc+$maxWeight, 'Integer');
      }
      if ($nsinc) {
        $p[4] = array($nsinc+$maxVal, 'Integer');
        $p[5] = array($nsinc+$maxWeight, 'Integer');
      }
      $insertStatus = implode(',', $insertStatus);

      $sql = "
INSERT INTO
   civicrm_option_value (`option_group_id`, label, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `is_optgroup`, `is_reserved`, `is_active`, `component_id`, `visibility_id`)
VALUES {$insertStatus}";
      CRM_Core_DAO::executeQuery($sql, $p);
    }

    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => '4.4.1')), 'task_4_4_x_runSql', $rev);
    $this->addTask('Patch word-replacement schema', 'wordReplacements_patch', $rev);
  }

  /**
   * Update activity contacts CRM-12274
   *
   * @return bool TRUE for success
   */
  static function activityContacts(CRM_Queue_TaskContext $ctx) {
    $upgrade = new CRM_Upgrade_Form();

    $activityContacts = CRM_Core_OptionGroup::values('activity_contacts', FALSE, FALSE, FALSE, NULL, 'name');
    $ovValue[] = $sourceID = CRM_Utils_Array::key('Activity Source', $activityContacts);
    $ovValue[] = $assigneeID = CRM_Utils_Array::key('Activity Assignees', $activityContacts);
    $ovValue[] = $targetID = CRM_Utils_Array::key('Activity Targets', $activityContacts);

    $optionGroupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'activity_contacts', 'id', 'name');
    if (!empty($ovValue)) {
      $ovValues = implode(', ', $ovValue);
      $query = "
UPDATE civicrm_option_value
SET    is_reserved = 1
WHERE  option_group_id = {$optionGroupID} AND value IN ($ovValues)";

      $dao = CRM_Core_DAO::executeQuery($query);
    }

    if (!$assigneeID) {
      $assigneeID = 1;
      $value[] = "({$optionGroupID}, 'Activity Assignees', 1, 'Activity Assignees', 1, 1, 1)";
    }
    if (!$sourceID) {
      $sourceID = 2;
      $value[] = "({$optionGroupID}, 'Activity Source', 2, 'Activity Source', 2, 1, 1)";
    }
    if (!$targetID) {
      $targetID = 3;
      $value[] = "({$optionGroupID}, 'Activity Targets', 3, 'Activity Targets', 3, 1, 1)";
    }

    if (!$assigneeID || !$sourceID || !$targetID ) {
      $insert =  "
INSERT INTO civicrm_option_value
(option_group_id, label, value, name, weight, is_reserved, is_active)
VALUES

";
      $values = implode(', ', $value);
      $query = $insert . $values;
      $dao = CRM_Core_DAO::executeQuery($query);
    }

    // sometimes an user does not make a clean backup and the above table
    // already exists, so lets delete this table - CRM-13665
    $query = "DROP TABLE IF EXISTS civicrm_activity_contact";
    $dao = CRM_Core_DAO::executeQuery($query);

    $query = "
CREATE TABLE IF NOT EXISTS civicrm_activity_contact (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Activity contact id',
  activity_id int(10) unsigned NOT NULL COMMENT 'Foreign key to the activity for this record.',
  contact_id int(10) unsigned NOT NULL COMMENT 'Foreign key to the contact for this record.',
  record_type_id int(10) unsigned DEFAULT NULL COMMENT 'The record type id for this row',
  PRIMARY KEY (id),
  UNIQUE KEY UI_activity_contact (contact_id,activity_id,record_type_id),
  KEY FK_civicrm_activity_contact_activity_id (activity_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";

    $dao = CRM_Core_DAO::executeQuery($query);


    $query = "
INSERT INTO civicrm_activity_contact (activity_id, contact_id, record_type_id)
SELECT      activity_id, target_contact_id, {$targetID} as record_type_id
FROM        civicrm_activity_target";

    $dao = CRM_Core_DAO::executeQuery($query);

    $query = "
INSERT INTO civicrm_activity_contact (activity_id, contact_id, record_type_id)
SELECT      activity_id, assignee_contact_id, {$assigneeID} as record_type_id
FROM        civicrm_activity_assignment";
    $dao = CRM_Core_DAO::executeQuery($query);

    $query = "
  INSERT INTO civicrm_activity_contact (activity_id, contact_id, record_type_id)
SELECT      id, source_contact_id, {$sourceID} as record_type_id
FROM        civicrm_activity
WHERE       source_contact_id IS NOT NULL";

    $dao = CRM_Core_DAO::executeQuery($query);

   $query = "DROP TABLE civicrm_activity_target";
   $dao = CRM_Core_DAO::executeQuery($query);

   $query = "DROP TABLE civicrm_activity_assignment";
   $dao = CRM_Core_DAO::executeQuery($query);

   $query = "ALTER  TABLE civicrm_activity
     DROP FOREIGN KEY FK_civicrm_activity_source_contact_id";

   $dao = CRM_Core_DAO::executeQuery($query);

   $query = "ALTER  TABLE civicrm_activity DROP COLUMN source_contact_id";
   $dao = CRM_Core_DAO::executeQuery($query);

   return TRUE;
  }

  /**
   * Migrate word-replacements from $config to civicrm_word_replacement
   *
   * @return bool TRUE for success
   * @see http://issues.civicrm.org/jira/browse/CRM-13187
   */
  static function wordReplacements(CRM_Queue_TaskContext $ctx) {
    $query = "
CREATE TABLE IF NOT EXISTS `civicrm_word_replacement` (
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Word replacement ID',
     `find_word` varchar(255) COLLATE utf8_bin    COMMENT 'Word which need to be replaced',
     `replace_word` varchar(255) COLLATE utf8_bin    COMMENT 'Word which will replace the word in find',
     `is_active` tinyint    COMMENT 'Is this entry active?',
     `match_type` enum('wildcardMatch', 'exactMatch')   DEFAULT 'wildcardMatch',
     `domain_id` int unsigned    COMMENT 'FK to Domain ID. This is for Domain specific word replacement',
    PRIMARY KEY ( `id` ),
    UNIQUE INDEX `UI_domain_find` (domain_id, find_word),
    CONSTRAINT FK_civicrm_word_replacement_domain_id FOREIGN KEY (`domain_id`) REFERENCES `civicrm_domain`(`id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;
    ";
    $dao = CRM_Core_DAO::executeQuery($query);

    self::rebuildWordReplacementTable();
    return TRUE;
  }

  /**
   * Fix misconfigured constraints created in 4.4.0. To distinguish the good
   * and bad configurations, we change the constraint name from "UI_find"
   * (the original name in 4.4.0) to "UI_domain_find" (the new name in
   * 4.4.1).
   *
   * @return bool TRUE for success
   * @see http://issues.civicrm.org/jira/browse/CRM-13655
   */
  static function wordReplacements_patch(CRM_Queue_TaskContext $ctx, $rev) {
    if (CRM_Core_DAO::checkConstraintExists('civicrm_word_replacement', 'UI_find')) {
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement DROP FOREIGN KEY FK_civicrm_word_replacement_domain_id;");
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement DROP KEY FK_civicrm_word_replacement_domain_id;");
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement DROP KEY UI_find;");
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement MODIFY COLUMN `find_word` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Word which need to be replaced';");
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement MODIFY COLUMN `replace_word` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Word which will replace the word in find';");
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement ADD CONSTRAINT UI_domain_find UNIQUE KEY `UI_domain_find` (`domain_id`,`find_word`);");
      CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_word_replacement ADD CONSTRAINT FK_civicrm_word_replacement_domain_id FOREIGN KEY (`domain_id`) REFERENCES `civicrm_domain` (`id`);");
    }
    return TRUE;
  }

  /**
   * (Queue Task Callback)
   */
  static function task_4_4_x_runSql(CRM_Queue_TaskContext $ctx, $rev) {
    $upgrade = new CRM_Upgrade_Form();
    $upgrade->processSQL($rev);

    return TRUE;
  }

  /**
   * Syntatic sugar for adding a task which (a) is in this class and (b) has
   * a high priority.
   *
   * After passing the $funcName, you can also pass parameters that will go to
   * the function. Note that all params must be serializable.
   */
  protected function addTask($title, $funcName) {
    $queue = CRM_Queue_Service::singleton()->load(array(
      'type' => 'Sql',
      'name' => CRM_Upgrade_Form::QUEUE_NAME,
    ));

    $args = func_get_args();
    $title = array_shift($args);
    $funcName = array_shift($args);
    $task = new CRM_Queue_Task(
      array(get_class($this), $funcName),
      $args,
      $title
    );
    $queue->createItem($task, array('weight' => -1));
  }

  /**
   * Get all the word-replacements stored in config-arrays
   * and convert them to params for the WordReplacement.create API.
   *
   * Note: This function is duplicated in CRM_Core_BAO_WordReplacement and
   * CRM_Upgrade_Incremental_php_FourFour to ensure that the incremental upgrade
   * step behaves consistently even as the BAO evolves in future versions.
   * However, if there's a bug in here prior to 4.4.0, we should apply the
   * bugfix in both places.
   *
   * @param bool $rebuildEach whether to perform rebuild after each individual API call
   * @return array Each item is $params for WordReplacement.create
   * @see CRM_Core_BAO_WordReplacement::convertConfigArraysToAPIParams
   */
  static function getConfigArraysAsAPIParams($rebuildEach) {
    $wordReplacementCreateParams = array();
    // get all domains
    $result = civicrm_api3('domain', 'get', array(
      'return' => array('locale_custom_strings'),
    ));
    if (!empty($result["values"])) {
      foreach ($result["values"] as $value) {
        $params = array();
        $params["domain_id"] = $value["id"];
        $params["options"] = array('wp-rebuild' => $rebuildEach);
        // unserialize word match string
        $localeCustomArray = unserialize($value["locale_custom_strings"]);
        if (!empty($localeCustomArray)) {
          $wordMatchArray = array();
          // Traverse Language array
          foreach ($localeCustomArray as $localCustomData) {
            // Traverse status array "enabled" "disabled"
            foreach ($localCustomData as $status => $matchTypes) {
              $params["is_active"] = ($status == "enabled")?TRUE:FALSE;
              // Traverse Match Type array "wildcardMatch" "exactMatch"
              foreach ($matchTypes as $matchType => $words) {
                $params["match_type"] = $matchType;
                foreach ($words as $word => $replace) {
                  $params["find_word"] = $word;
                  $params["replace_word"] = $replace;
                  $wordReplacementCreateParams[] = $params;
                }
              }
            }
          }
        }
      }
    }
    return $wordReplacementCreateParams;
  }

  /**
   * Get all the word-replacements stored in config-arrays
   * and write them out as records in civicrm_word_replacement.
   *
   * Note: This function is duplicated in CRM_Core_BAO_WordReplacement and
   * CRM_Upgrade_Incremental_php_FourFour to ensure that the incremental upgrade
   * step behaves consistently even as the BAO evolves in future versions.
   * However, if there's a bug in here prior to 4.4.0, we should apply the
   * bugfix in both places.
   */
  public static function rebuildWordReplacementTable() {
    civicrm_api3('word_replacement', 'replace', array(
      'options' => array('match' => array('domain_id', 'find_word')),
      'values' => self::getConfigArraysAsAPIParams(FALSE),
    ));
    CRM_Core_BAO_WordReplacement::rebuild();
  }
}
