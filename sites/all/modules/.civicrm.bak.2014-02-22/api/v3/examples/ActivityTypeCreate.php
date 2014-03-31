<?php
/**
 * Test Generated example of using activity_type create API
 * *
 */
function activity_type_create_example(){
$params = array(
  'weight' => '2',
  'label' => 'send out letters',
  'filter' => 0,
  'is_active' => 1,
  'is_optgroup' => 1,
  'is_default' => 0,
);

try{
  $result = civicrm_api3('activity_type', 'create', $params);
}
catch (CiviCRM_API3_Exception $e) {
  // handle error here
  $errorMessage = $e->getMessage();
  $errorCode = $e->getErrorCode();
  $errorData = $e->getExtraParams();
  return array('error' => $errorMessage, 'error_code' => $errorCode, 'error_data' => $errorData);
}

return $result;
}

/**
 * Function returns array of result expected from previous function
 */
function activity_type_create_expectedresult(){

  $expectedResult = array(
  'is_error' => 0,
  'version' => 3,
  'count' => 1,
  'id' => 722,
  'values' => array(
      '722' => array(
          'id' => '722',
          'option_group_id' => '2',
          'label' => 'send out letters',
          'value' => '46',
          'name' => 'send out letters',
          'grouping' => '',
          'filter' => 0,
          'is_default' => 0,
          'weight' => '2',
          'description' => '',
          'is_optgroup' => '1',
          'is_reserved' => '',
          'is_active' => '1',
          'component_id' => '',
          'domain_id' => '',
          'visibility_id' => '',
        ),
    ),
);

  return $expectedResult;
}


/*
* This example has been generated from the API test suite. The test that created it is called
*
* testActivityTypeCreate and can be found in
* http://svn.civicrm.org/civicrm/trunk/tests/phpunit/CiviTest/api/v3/ActivityTypeTest.php
*
* You can see the outcome of the API tests at
* http://tests.dev.civicrm.org/trunk/results-api_v3
*
* To Learn about the API read
* http://book.civicrm.org/developer/current/techniques/api/
*
* and review the wiki at
* http://wiki.civicrm.org/confluence/display/CRMDOC/CiviCRM+Public+APIs
*
* Read more about testing here
* http://wiki.civicrm.org/confluence/display/CRM/Testing
*
* API Standards documentation:
* http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
*/