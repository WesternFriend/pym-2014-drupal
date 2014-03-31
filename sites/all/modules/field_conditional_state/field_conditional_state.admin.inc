<?php
/**
 * @file
 * Containing the backend functions.
 */

/**
 * Returns the available states for a given field type.
 *
 * @param string $type
 *   the field type
 */
function _field_conditional_state_get_field_states($type) {
  $states_available = array();

  switch ($type) {
    case 'list_boolean':
      $states_available = array(
        'enabled' => t('Enabled'),
        'disabled' => t('Disabled'),
        'required' => t('Required'),
        'optional' => t('Optional'),
        'visible' => t('Visible'),
        'invisible' => t('Invisible'),
        'checked' => t('Checked'),
        'unchecked' => t('Unchecked'),
      );
      break;

    default:
      $states_available = array(
        'enabled' => t('Enabled'),
        'disabled' => t('Disabled'),
        'required' => t('Required'),
        'optional' => t('Optional'),
        'visible' => t('Visible'),
        'invisible' => t('Invisible'),
      );
  }

  return $states_available;
}

/**
 * Returns the triggerable states for a given field type.
 *
 * @param string $type
 *   the field type
 */
function _field_conditional_state_get_trigger_states($type) {
  $states_triggerable = array();

  switch ($type) {
    case 'list_boolean':
      $states_triggerable = array('checked', 'unchecked');
      break;

    case 'list_text':
    case 'list_float':
    case 'list_integer':
      $states_triggerable = array('value', '!value');
      break;

    case 'file':
    case 'image':
      $states_triggerable = array('empty', 'filled');
      break;

    default:
      $states_triggerable = array('empty', 'filled', 'value', '!value');
  }

  return $states_triggerable;
}

/**
 * Returns the available group types.
 */
function _field_conditional_state_get_group_types() {
  return array(
    'and' => t('All conditions must be true (AND)'),
    'or' => t('At least one condition must be true (OR)'),
    'xor' => t('Exactly one condition must be true (XOR)'),
  );
}

/**
 * Builds the settings form for a particular field instance.
 *
 * @param array $form
 *   the complete form
 * @param array $form_state
 *   the current form_state
 * @param array $instance
 *   the field instance for which the form is build
 */
function field_conditional_state_settings_form($form, &$form_state, $instance) {
  if (!_field_conditional_state_control_field_whitelist($instance['widget']['type'])) {
    $form['info'] = array('#markup' => '<p>' . t("The currently selected widget for this field isn't supported by Field Conditional States. Please choose a different widget or create a support request in Field Conditional States issue queue on drupal.org.") . '</p>');
    return $form;
  }
  if (!isset($form_state['#field_conditional_state'])) {
    $form_state['#field_conditional_state'] = array();
  }
  $fcs_states = &$form_state['#field_conditional_state'];

  $fields = field_info_instances($instance['entity_type'], $instance['bundle']);
  $current_field = field_info_field($instance['field_name']);

  $dependees = array();
  $dependees_as_options = array();
  foreach ($fields as $name => $data) {
    if ($name != $instance['field_name']) {
      $dependees[$name] = field_info_field($data['field_name']);
      $dependees_as_options[$name] = $data['label'];
    }
  }

  $fcs_states['possible_states'] = _field_conditional_state_get_field_states($current_field['type']);

  $fcs_states['existing_states'] = array();
  $fcs_states['existing_groups'] = array();

  $form['content'] = array(
    '#type' => 'vertical_tabs',
  );

  if (!empty($_SESSION['#field_conditional_state']['last_active_settings_tab'])) {
    $form['content']['#default_tab'] = $_SESSION['#field_conditional_state']['last_active_settings_tab'];
  }

  $subforms = array();
  foreach (array_keys($fcs_states['possible_states']) as $state) {
    $form['content']['tab-' . $state] = array(
      '#type' => 'fieldset',
      '#title' => $fcs_states['possible_states'][$state],
      'content' => array(),
    );

    $subforms[$state] = &$form['content']['tab-' . $state]['content'];

    $subforms[$state]['info'] = array(
      '#markup' => '<p>' . t('The state is activated if and only if all listed groups are true.') . '</p>',
    );

    $subforms[$state]['new_group'] = array(
      '#type' => 'fieldset',
      '#title' => t('Create new group'),
      '#weight' => 100,
    );

    $subforms[$state]['new_group']['new-group-' . $state] = array(
      '#type' => 'radios',
      '#options' => _field_conditional_state_get_group_types(),
    );

  }

  $groups = _field_conditional_state_get_conditional_states($instance['entity_type'], $instance['bundle'], $instance['field_name']);
  foreach ($groups as $group) {
    $fcs_states['existing_groups'][] = $group['group_id'];

    $table_id = drupal_html_id('field_conditional_state-group-table-' . $group['group_id']);
    $new_row = &$subforms[$group['state']][$group['group_id']];
    $new_row = array(
      'states' => array(
        '#prefix' => '<table ' . drupal_attributes(array('id' => $table_id)) . '><tr><th>' . t('Control field') . '</th><th>' . t('Trigger state') . '</th><th>' . t('Trigger value') . '</th><th>' . t('Operation') . '</th></tr>',
        '#suffix' => '</table>',
      ),
    );

    $new_row['states']['group_type-' . $group['group_id']] = array(
      '#prefix' => '<tr class="odd"><td colspan="3">',
      '#type' => 'radios',
      '#options' => _field_conditional_state_get_group_types(),
      '#default_value' => $group['type'],
      '#suffix' => '</td>',
    );

    $new_row['states']['delete-group-' . $group['group_id']] = array(
      '#prefix' => '<td>',
      '#type' => 'submit',
      '#name' => 'field_conditional_state-delete_group-' . $group['group_id'],
      '#value' => t('Delete group'),
      '#field_conditional_state_group_id' => $group['group_id'],
      '#suffix' => '</td></tr>',
      '#submit' => array('_field_conditional_state_delete_condition_group'),
      '#ajax' => array(
        'callback' => '_field_conditional_state_ajax_remove',
        'wrapper' => $table_id,
      ),
    );

    $bg = 'even';
    foreach ($group['states'] as $state) {
      $fcs_states['existing_states'][] = $state['id'];

      $new_state = &$new_row['states'][$state['id']];
      $new_state = _field_conditional_state_get_state_default_row($state['id'], array($bg));

      $control_field = $state['control_field'];
      if (!empty($form_state['values'])) {
        $control_field = $form_state['values']['control_field-' . $state['id']];
      }

      $new_state['control_field-' . $state['id']]['#options'] = $dependees_as_options;
      $new_state['control_field-' . $state['id']]['#default_value'] = $control_field;
      $new_state['control_field-' . $state['id']]['#field_conditional_state_group'] = $group;

      $new_state['trigger_state-' . $state['id']]['#options'] = drupal_map_assoc(_field_conditional_state_get_trigger_states($dependees[$control_field]['type']));
      $new_state['trigger_state-' . $state['id']]['#default_value'] = $state['trigger_state'];

      $new_state['trigger_value-' . $state['id']]['#default_value'] = $state['trigger_value'];

      $bg = ($bg == 'odd' ? 'even' : 'odd');
    }

    $new_id = 'new-' . $group['group_id'];
    $new_state = &$new_row['states'][$new_id];
    $new_state = _field_conditional_state_get_state_default_row($new_id, array('field_conditional_state_settings_new_row'));

    $control_field = NULL;
    $trigger_states = array();
    if (!empty($form_state['values']) && !empty($form_state['values']['control_field-' . $new_id])) {
      $control_field = $form_state['values']['control_field-' . $new_id];
      $trigger_states = drupal_map_assoc(_field_conditional_state_get_trigger_states($dependees[$control_field]['type']));
    }

    $new_state['control_field-' . $new_id]['#options'] = $dependees_as_options;
    $new_state['control_field-' . $new_id]['#field_conditional_state_group'] = $group;
    $new_state['control_field-' . $new_id]['#empty_value'] = '';

    $new_state['trigger_state-' . $new_id]['#options'] = $trigger_states;
    $new_state['trigger_state-' . $new_id]['#empty_value'] = '';

    $new_state['delete-' . $new_id] = array(
      '#prefix' => '<td>',
      '#suffix' => '</td>',
    );
  }

  $form['action'] = array(
    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Save changes'),
    ),
  );
  $form['#attached'] = array(
    'css' => array(
      drupal_get_path('module', 'field_conditional_state') . '/field_conditional_state.admin.css',
    ),
  );
  return $form;
}

/**
 * Validates the submitted data of the settings form.
 *
 * @param array $form
 *   the complete form
 * @param array $form_state
 *   the current form_state
 */
function field_conditional_state_settings_form_validate($form, $form_state) {
  $values = $form_state['values'];

  foreach ($form_state['#field_conditional_state']['existing_groups'] as $id) {
    if (!empty($values['control_field-new-' . $id]) && empty($values['trigger_state-new-' . $id])) {
      form_set_error('trigger_state-new-' . $id, t('Please choose a trigger state for the new condition.'));
    }
    if (empty($values['control_field-new-' . $id]) && !empty($values['trigger_state-new-' . $id])) {
      form_set_error('control_field-new-' . $id, t('Please choose a control field for the new condition.'));
    }
  }
}

/**
 * Handles the submitted data of the settings form.
 *
 * @param array $form
 *   the complete form
 * @param array $form_state
 *   the current form_state
 */
function field_conditional_state_settings_form_submit($form, $form_state) {
  $fcs_states = &$form_state['#field_conditional_state'];
  $instance = $form_state['build_info']['args'][0];
  $values = $form_state['values'];

  // Remember the latest selected tab to set it as new default tab.
  if (isset($values['content__active_tab'])) {
    $_SESSION['#field_conditional_state']['last_active_settings_tab'] = $values['content__active_tab'];
  }

  foreach ($fcs_states['existing_states'] as $id) {
    $fields = array(
      'control_field' => $values['control_field-' . $id],
      'trigger_state' => $values['trigger_state-' . $id],
      'trigger_value' => 1,
    );
    if ($values['trigger_state-' . $id] == 'value' || $values['trigger_state-' . $id] == '!value') {
      $fields['trigger_value'] = $values['trigger_value-' . $id];
    }

    db_update('field_conditional_state')
      ->fields($fields)
      ->condition('id', $id)
      ->execute();
  }

  foreach ($fcs_states['existing_groups'] as $id) {
    if (!empty($values['control_field-new-' . $id]) && !empty($values['trigger_state-new-' . $id])) {
      $trigger_value = 1;
      if ($values['trigger_state-new-' . $id] == 'value' || $values['trigger_state-new-' . $id] == '!value') {
        $trigger_value = $values['trigger_value-new-' . $id];
      }

      db_insert('field_conditional_state')
        ->fields(array(
          'group_id' => $id,
          'control_field' => $values['control_field-new-' . $id],
          'trigger_state' => $values['trigger_state-new-' . $id],
          'trigger_value' => $trigger_value,
        ))
        ->execute();
    }
    db_update('field_conditional_states_group')
      ->fields(array('type' => $values['group_type-' . $id]))
      ->condition('group_id', $id)
      ->execute();
  }

  foreach (array_keys($fcs_states['possible_states']) as $state) {
    if (!empty($values['new-group-' . $state])) {
      db_insert('field_conditional_states_group')
        ->fields(array(
          'state' => $state,
          'entity_type' => $instance['entity_type'],
          'bundle' => $instance['bundle'],
          'field_name' => $instance['field_name'],
          'type' => $values['new-group-' . $state],
        ))
        ->execute();
    }
  }
}

/**
 * Returns a basic / empty row of a single condition.
 */
function _field_conditional_state_get_state_default_row($id, $classes = array()) {
  $state_id = drupal_html_id('field_conditional_state-state-' . $id);
  $new_state = array(
    '#prefix' => '<tr ' . drupal_attributes(array('id' => $state_id, 'class' => $classes)) . '>',
    '#suffix' => '</tr>',
  );

  $new_state['control_field-' . $id] = array(
    '#prefix' => '<td>',
    '#suffix' => '</td>',
    '#type' => 'select',
    '#options' => array(),
    '#field_conditional_state_state_id' => $id,
    '#ajax' => array(
      'callback' => '_field_conditional_state_control_field_change',
      'wrapper' => $state_id,
    ),
  );

  $new_state['trigger_state-' . $id] = array(
    '#prefix' => '<td ' . drupal_attributes(array('id' => drupal_html_id('trigger_state-' . $id))) . '>',
    '#suffix' => '</td>',
    '#type' => 'select',
    '#options' => array(),
  );

  $new_state['trigger_value-' . $id] = array(
    '#prefix' => '<td>',
    '#suffix' => '</td>',
    '#type' => 'textfield',
    '#states' => array(
      'visible' => array(
        array(
          array(':input[name="trigger_state-' . check_plain($id) . '"]' => array('value' => 'value')),
          array(':input[name="trigger_state-' . check_plain($id) . '"]' => array('value' => '!value')),
        ),
      ),
    ),
  );

  $new_state['delete-' . $id] = array(
    '#name' => 'delete-' . $id,
    '#prefix' => '<td>',
    '#suffix' => '</td>',
    '#type' => 'submit',
    '#value' => t('Delete'),
    '#field_conditional_state_state_id' => $id,
    '#submit' => array('_field_conditional_state_delete_condition'),
    '#ajax' => array(
      'callback' => '_field_conditional_state_ajax_remove',
      'wrapper' => $state_id,
    ),
  );
  return $new_state;
}

/**
 * Ajax callback for when a control field has been changed.
 */
function _field_conditional_state_control_field_change($form, $form_state) {
  $elem = &$form_state['triggering_element'];
  $group_state = $elem['#field_conditional_state_group']['state'];
  $group_id = $elem['#field_conditional_state_group']['group_id'];
  $state_id = $elem['#field_conditional_state_state_id'];
  return $form['content']['tab-' . $group_state]['content'][$group_id]['states'][$state_id];
}

/**
 * Ajax callback for removing elements.
 */
function _field_conditional_state_ajax_remove($form, $form_state) {
  return '';
}

/**
 * Submit function for deleting single conditions.
 */
function _field_conditional_state_delete_condition($form, $form_state) {
  $state_id = $form_state['triggering_element']['#field_conditional_state_state_id'];
  if (!empty($state_id)) {
    db_delete('field_conditional_state')
      ->condition('id', $state_id)
      ->execute();
  }
}


/**
 * Submit function for deleting condition groups.
 */
function _field_conditional_state_delete_condition_group($form, $form_state) {
  $group_id = $form_state['triggering_element']['#field_conditional_state_group_id'];
  if (!empty($group_id)) {
    db_delete('field_conditional_state')
      ->condition('group_id', $group_id)
      ->execute();

    db_delete('field_conditional_states_group')
      ->condition('group_id', $group_id)
      ->execute();
  }
}
