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
| Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
|                                                                    |
| CiviCRM is distributed in the hope that it will be useful, but     |
| WITHOUT ANY WARRANTY; without even the implied warranty of         |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
| See the GNU Affero General Public License for more details.        |
|                                                                    |
| You should have received a copy of the GNU Affero General Public   |
| License and the CiviCRM Licensing Exception along                  |
| with this program; if not, contact CiviCRM LLC                     |
| at info[AT]civicrm[DOT]org. If you have questions about the        |
| GNU Affero General Public License or the licensing of CiviCRM,     |
| see the CiviCRM license FAQ at http://civicrm.org/licensing        |
+--------------------------------------------------------------------+
*/
/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * Generated from xml/schema/CRM/Contribute/ContributionPage.xml
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Contribute_DAO_ContributionPage extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_contribution_page';
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   * @static
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   * @static
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   * @static
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   * @static
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   * @static
   */
  static $_log = true;
  /**
   * Contribution Id
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Contribution Page title. For top of page display
   *
   * @var string
   */
  public $title;
  /**
   * Text and html allowed. Displayed below title.
   *
   * @var text
   */
  public $intro_text;
  /**
   * default financial type assigned to contributions submitted via this page, e.g. Contribution, Campaign Contribution
   *
   * @var int unsigned
   */
  public $financial_type_id;
  /**
   * Payment Processors configured for this contribution Page
   *
   * @var string
   */
  public $payment_processor;
  /**
   * if true - processing logic must reject transaction at confirmation stage if pay method != credit card
   *
   * @var boolean
   */
  public $is_credit_card_only;
  /**
   * if true - allows real-time monetary transactions otherwise non-monetary transactions
   *
   * @var boolean
   */
  public $is_monetary;
  /**
   * if true - allows recurring contributions, valid only for PayPal_Standard
   *
   * @var boolean
   */
  public $is_recur;
  /**
   * if false, the confirm page in contribution pages gets skipped
   *
   * @var boolean
   */
  public $is_confirm_enabled;
  /**
   * Supported recurring frequency units.
   *
   * @var string
   */
  public $recur_frequency_unit;
  /**
   * if true - supports recurring intervals
   *
   * @var boolean
   */
  public $is_recur_interval;
  /**
   * if true - asks user for recurring installments
   *
   * @var boolean
   */
  public $is_recur_installments;
  /**
   * if true - allows the user to send payment directly to the org later
   *
   * @var boolean
   */
  public $is_pay_later;
  /**
   * The text displayed to the user in the main form
   *
   * @var text
   */
  public $pay_later_text;
  /**
   * The receipt sent to the user instead of the normal receipt text
   *
   * @var text
   */
  public $pay_later_receipt;
  /**
   * is partial payment enabled for this online contribution page
   *
   * @var boolean
   */
  public $is_partial_payment;
  /**
   * Initial amount label for partial payment
   *
   * @var string
   */
  public $initial_amount_label;
  /**
   * Initial amount help text for partial payment
   *
   * @var text
   */
  public $initial_amount_help_text;
  /**
   * Minimum initial amount for partial payment
   *
   * @var float
   */
  public $min_initial_amount;
  /**
   * if true, page will include an input text field where user can enter their own amount
   *
   * @var boolean
   */
  public $is_allow_other_amount;
  /**
   * FK to civicrm_option_value.
   *
   * @var int unsigned
   */
  public $default_amount_id;
  /**
   * if other amounts allowed, user can configure minimum allowed.
   *
   * @var float
   */
  public $min_amount;
  /**
   * if other amounts allowed, user can configure maximum allowed.
   *
   * @var float
   */
  public $max_amount;
  /**
   * The target goal for this page, allows people to build a goal meter
   *
   * @var float
   */
  public $goal_amount;
  /**
   * Title for Thank-you page (header title tag, and display at the top of the page).
   *
   * @var string
   */
  public $thankyou_title;
  /**
   * text and html allowed. displayed above result on success page
   *
   * @var text
   */
  public $thankyou_text;
  /**
   * Text and html allowed. displayed at the bottom of the success page. Common usage is to include link(s) to other pages such as tell-a-friend, etc.
   *
   * @var text
   */
  public $thankyou_footer;
  /**
   * if true, signup is done on behalf of an organization
   *
   * @var boolean
   */
  public $is_for_organization;
  /**
   * This text field is shown when is_for_organization is checked. For example - I am contributing on behalf on an organization.
   *
   * @var text
   */
  public $for_organization;
  /**
   * if true, receipt is automatically emailed to contact on success
   *
   * @var boolean
   */
  public $is_email_receipt;
  /**
   * FROM email name used for receipts generated by contributions to this contribution page.
   *
   * @var string
   */
  public $receipt_from_name;
  /**
   * FROM email address used for receipts generated by contributions to this contribution page.
   *
   * @var string
   */
  public $receipt_from_email;
  /**
   * comma-separated list of email addresses to cc each time a receipt is sent
   *
   * @var string
   */
  public $cc_receipt;
  /**
   * comma-separated list of email addresses to bcc each time a receipt is sent
   *
   * @var string
   */
  public $bcc_receipt;
  /**
   * text to include above standard receipt info on receipt email. emails are text-only, so do not allow html for now
   *
   * @var text
   */
  public $receipt_text;
  /**
   * Is this property active?
   *
   * @var boolean
   */
  public $is_active;
  /**
   * Text and html allowed. Displayed at the bottom of the first page of the contribution wizard.
   *
   * @var text
   */
  public $footer_text;
  /**
   * Is this property active?
   *
   * @var boolean
   */
  public $amount_block_is_active;
  /**
   * Should this contribution have the honor  block enabled?
   *
   * @var boolean
   */
  public $honor_block_is_active;
  /**
   * Title for honor block.
   *
   * @var string
   */
  public $honor_block_title;
  /**
   * text for honor block.
   *
   * @var text
   */
  public $honor_block_text;
  /**
   * Date and time that this page starts.
   *
   * @var datetime
   */
  public $start_date;
  /**
   * Date and time that this page ends. May be NULL if no defined end date/time
   *
   * @var datetime
   */
  public $end_date;
  /**
   * FK to civicrm_contact, who created this contribution page
   *
   * @var int unsigned
   */
  public $created_id;
  /**
   * Date and time that contribution page was created.
   *
   * @var datetime
   */
  public $created_date;
  /**
   * 3 character string, value from config setting or input via user.
   *
   * @var string
   */
  public $currency;
  /**
   * The campaign for which we are collecting contributions with this page.
   *
   * @var int unsigned
   */
  public $campaign_id;
  /**
   * Can people share the contribution page through social media?
   *
   * @var boolean
   */
  public $is_share;
  /**
   * class constructor
   *
   * @access public
   * @return civicrm_contribution_page
   */
  function __construct()
  {
    $this->__table = 'civicrm_contribution_page';
    parent::__construct();
  }
  /**
   * return foreign keys and entity references
   *
   * @static
   * @access public
   * @return array of CRM_Core_EntityReference
   */
  static function getReferenceColumns()
  {
    if (!self::$_links) {
      self::$_links = array(
        new CRM_Core_EntityReference(self::getTableName() , 'financial_type_id', 'civicrm_financial_type', 'id') ,
        new CRM_Core_EntityReference(self::getTableName() , 'created_id', 'civicrm_contact', 'id') ,
        new CRM_Core_EntityReference(self::getTableName() , 'campaign_id', 'civicrm_campaign', 'id') ,
      );
    }
    return self::$_links;
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'title' => array(
          'name' => 'title',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Title') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'intro_text' => array(
          'name' => 'intro_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Intro Text') ,
          'rows' => 6,
          'cols' => 50,
        ) ,
        'financial_type_id' => array(
          'name' => 'financial_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Financial_DAO_FinancialType',
          'pseudoconstant' => array(
            'table' => 'civicrm_financial_type',
            'keyColumn' => 'id',
            'labelColumn' => 'name',
          )
        ) ,
        'payment_processor' => array(
          'name' => 'payment_processor',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Payment Processor') ,
          'maxlength' => 128,
          'size' => CRM_Utils_Type::HUGE,
          'pseudoconstant' => array(
            'table' => 'civicrm_payment_processor',
            'keyColumn' => 'id',
            'labelColumn' => 'name',
          )
        ) ,
        'is_credit_card_only' => array(
          'name' => 'is_credit_card_only',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'is_monetary' => array(
          'name' => 'is_monetary',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'default' => '1',
        ) ,
        'is_recur' => array(
          'name' => 'is_recur',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'is_confirm_enabled' => array(
          'name' => 'is_confirm_enabled',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'default' => '1',
        ) ,
        'recur_frequency_unit' => array(
          'name' => 'recur_frequency_unit',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Recur Frequency Unit') ,
          'maxlength' => 128,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'is_recur_interval' => array(
          'name' => 'is_recur_interval',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'is_recur_installments' => array(
          'name' => 'is_recur_installments',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'is_pay_later' => array(
          'name' => 'is_pay_later',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'pay_later_text' => array(
          'name' => 'pay_later_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Pay Later Text') ,
        ) ,
        'pay_later_receipt' => array(
          'name' => 'pay_later_receipt',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Pay Later Receipt') ,
        ) ,
        'is_partial_payment' => array(
          'name' => 'is_partial_payment',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'initial_amount_label' => array(
          'name' => 'initial_amount_label',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Initial Amount Label') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'initial_amount_help_text' => array(
          'name' => 'initial_amount_help_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Initial Amount Help Text') ,
        ) ,
        'min_initial_amount' => array(
          'name' => 'min_initial_amount',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Min Initial Amount') ,
        ) ,
        'is_allow_other_amount' => array(
          'name' => 'is_allow_other_amount',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'default_amount_id' => array(
          'name' => 'default_amount_id',
          'type' => CRM_Utils_Type::T_INT,
        ) ,
        'min_amount' => array(
          'name' => 'min_amount',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Min Amount') ,
        ) ,
        'max_amount' => array(
          'name' => 'max_amount',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Max Amount') ,
        ) ,
        'goal_amount' => array(
          'name' => 'goal_amount',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Goal Amount') ,
        ) ,
        'thankyou_title' => array(
          'name' => 'thankyou_title',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Thank-you Title') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'thankyou_text' => array(
          'name' => 'thankyou_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Thank-you Text') ,
          'rows' => 8,
          'cols' => 60,
        ) ,
        'thankyou_footer' => array(
          'name' => 'thankyou_footer',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Thank-you Footer') ,
          'rows' => 8,
          'cols' => 60,
        ) ,
        'is_for_organization' => array(
          'name' => 'is_for_organization',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'for_organization' => array(
          'name' => 'for_organization',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('On Behalf Of Organization') ,
          'rows' => 2,
          'cols' => 50,
        ) ,
        'is_email_receipt' => array(
          'name' => 'is_email_receipt',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'receipt_from_name' => array(
          'name' => 'receipt_from_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Receipt From Name') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'receipt_from_email' => array(
          'name' => 'receipt_from_email',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Receipt From Email') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'cc_receipt' => array(
          'name' => 'cc_receipt',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Cc Receipt') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'bcc_receipt' => array(
          'name' => 'bcc_receipt',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Bcc Receipt') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'receipt_text' => array(
          'name' => 'receipt_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Receipt Text') ,
          'rows' => 6,
          'cols' => 50,
        ) ,
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'footer_text' => array(
          'name' => 'footer_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Footer Text') ,
          'rows' => 6,
          'cols' => 50,
        ) ,
        'amount_block_is_active' => array(
          'name' => 'amount_block_is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'default' => '1',
        ) ,
        'honor_block_is_active' => array(
          'name' => 'honor_block_is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ) ,
        'honor_block_title' => array(
          'name' => 'honor_block_title',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Honor Block Title') ,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'honor_block_text' => array(
          'name' => 'honor_block_text',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Honor Block Text') ,
          'rows' => 2,
          'cols' => 50,
        ) ,
        'start_date' => array(
          'name' => 'start_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => ts('Contribution Page Start Date') ,
        ) ,
        'end_date' => array(
          'name' => 'end_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => ts('Contribution Page End Date') ,
        ) ,
        'created_id' => array(
          'name' => 'created_id',
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'created_date' => array(
          'name' => 'created_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => ts('Contribution Page Created Date') ,
        ) ,
        'currency' => array(
          'name' => 'currency',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Currency') ,
          'maxlength' => 3,
          'size' => CRM_Utils_Type::FOUR,
          'default' => 'NULL',
          'pseudoconstant' => array(
            'table' => 'civicrm_currency',
            'keyColumn' => 'name',
            'labelColumn' => 'full_name',
            'nameColumn' => 'numeric_code',
          )
        ) ,
        'campaign_id' => array(
          'name' => 'campaign_id',
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Campaign_DAO_Campaign',
        ) ,
        'is_share' => array(
          'name' => 'is_share',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'default' => '1',
        ) ,
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'title' => 'title',
        'intro_text' => 'intro_text',
        'financial_type_id' => 'financial_type_id',
        'payment_processor' => 'payment_processor',
        'is_credit_card_only' => 'is_credit_card_only',
        'is_monetary' => 'is_monetary',
        'is_recur' => 'is_recur',
        'is_confirm_enabled' => 'is_confirm_enabled',
        'recur_frequency_unit' => 'recur_frequency_unit',
        'is_recur_interval' => 'is_recur_interval',
        'is_recur_installments' => 'is_recur_installments',
        'is_pay_later' => 'is_pay_later',
        'pay_later_text' => 'pay_later_text',
        'pay_later_receipt' => 'pay_later_receipt',
        'is_partial_payment' => 'is_partial_payment',
        'initial_amount_label' => 'initial_amount_label',
        'initial_amount_help_text' => 'initial_amount_help_text',
        'min_initial_amount' => 'min_initial_amount',
        'is_allow_other_amount' => 'is_allow_other_amount',
        'default_amount_id' => 'default_amount_id',
        'min_amount' => 'min_amount',
        'max_amount' => 'max_amount',
        'goal_amount' => 'goal_amount',
        'thankyou_title' => 'thankyou_title',
        'thankyou_text' => 'thankyou_text',
        'thankyou_footer' => 'thankyou_footer',
        'is_for_organization' => 'is_for_organization',
        'for_organization' => 'for_organization',
        'is_email_receipt' => 'is_email_receipt',
        'receipt_from_name' => 'receipt_from_name',
        'receipt_from_email' => 'receipt_from_email',
        'cc_receipt' => 'cc_receipt',
        'bcc_receipt' => 'bcc_receipt',
        'receipt_text' => 'receipt_text',
        'is_active' => 'is_active',
        'footer_text' => 'footer_text',
        'amount_block_is_active' => 'amount_block_is_active',
        'honor_block_is_active' => 'honor_block_is_active',
        'honor_block_title' => 'honor_block_title',
        'honor_block_text' => 'honor_block_text',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
        'created_id' => 'created_id',
        'created_date' => 'created_date',
        'currency' => 'currency',
        'campaign_id' => 'campaign_id',
        'is_share' => 'is_share',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the names of this table
   *
   * @access public
   * @static
   * @return string
   */
  static function getTableName()
  {
    return CRM_Core_DAO::getLocaleTableName(self::$_tableName);
  }
  /**
   * returns if this table needs to be logged
   *
   * @access public
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * returns the list of fields that can be imported
   *
   * @access public
   * return array
   * @static
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('import', $field)) {
          if ($prefix) {
            self::$_import['contribution_page'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['contribution_page'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}
