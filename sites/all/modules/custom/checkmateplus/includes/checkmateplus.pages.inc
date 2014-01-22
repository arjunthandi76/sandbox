<?php
// $Id$


/**
 * @file
 * Checklist pages.
 */

/**
 * The Checklist page, which displays a user's checklist, and allows the user to interact
 */
function insourced_main_page($account = NULL,  $project_id, $current = TRUE) {
  if (!$account || !is_object($account)) {
    global $user;
    $account = $user;
    $current = TRUE;
  }


  if (empty($lists)) {
    // Check if the user was trying to get somewhere else before completing
    // their checklists.
    if (isset($_SESSION['checklist_final_destination'])) {
      $destination = $_SESSION['checklist_final_destination'];
      unset($_SESSION['checklist_final_destination']);
      _checklist_goto($destination);
    }
    $output = '<p>' . t('Congratulations. You have completed all your checklists.') . '</p>' . checklist_continue_link();
  }
  else {
    $output = theme('checklist_lists', array('lists' => $lists, 'account' => $account));
    // Set the title if the administrator has set it.
    if ($title = variable_get('checklist_page_title', 'Checklists')) {
      drupal_set_title($title);
    }
  }
  return $output;
}

/**
 * Theme several user checklists.
 */
function theme_insourced_lists($variables) {
  $lists = $variables['lists'];
  $account = $variables['account'];
  drupal_add_css(drupal_get_path('module', 'checklist') . '/resources/checklist.css');

  // Output the general description for checklists.
  if ($output = variable_get('checklist_page_description', '')) {
    $output = '<p>' . _checklist_clean($output['value'], $output['format']) . '</p>';
  }
  $incomplete = FALSE;
  foreach ($lists as $list) {
    if (!$list->completed) $incomplete = TRUE;
    $output .= theme('checklist_list', array('lists' => $list, 'account' => $account));
  }
  if ($incomplete && empty($_SESSION['checklist_required'])) {
    $output .= checklist_continue_link();
  }
  return $output;
}

/**
 * Theme a single checklist.
 */
function theme_insourced_list($variables) {
  $list = $variables['lists'];
  $account = $variables['account'];
  $output = '';
  // If a list is completed, the items shouldn't be clickable. Also if this is a
  // base checklist (not a checklist instance) then it shouldn't be clickable.
  $link = empty($list->completed) && !empty($list->instance_id);
  foreach ($list->items as $item) {
//print_r($item);echo "<P><P>";
    $output .= theme('checklist_item', array('link' => $link, 'item' => $item, 'account' => $account, 'list' => $list));
    // Don't link to items the user can't complete yet.
    if (!empty($list->force_order) && empty($item->checked_on)) $link = FALSE;
  }
  $fieldset = array(
    '#title' => _checklist_clean($list->title),
    '#description' => _checklist_clean($list->description, $list->format),   // EVU CODE - this is where we over ride with Tutorial fields
    '#children' => $output,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#attributes' => array('class' => array('collapsible')),
    '#attached' => array('library' => array('system', 'drupal.collapse')),
  );
  drupal_add_js('misc/form.js');
  drupal_add_js('misc/collapse.js');
  if (!empty($list->completed)) {
    $fieldset['#attributes']['class'][] = 'clListCompleted';
    $fieldset['#attributes']['class'][] = 'collapsed';
    $fieldset['#collapsed'] = TRUE;
    $fieldset['#children'] .= '<div>'. t('List completed !date.', array('!date' => format_date($list->completed, 'small'))) .'</div>';
  }
  return theme('fieldset', array('element' => $fieldset));
}

/**
 * Theme a checklist item.
 */
function theme_insourced_item($variables) {
  $link = $variables['link'];
  $item = $variables['item'];
  $list = $variables['list'];
  $account = $variables['account'];
  global $user;
  static $js_added = FALSE;
  $output = '<div class="checklist"><div id="checklistItem' . (!empty($list->instance_id) ? $list->instance_id : $list->clid) . '-' . $item->cliid . '" class="';

  // Only get links and so on if the item is enabled.
  if (($item->status == CHECKLIST_ENABLED) && !empty($list->instance_id)) {
    $item->link = $link;
    $options = (array) _checklist_item_callback_handler('display_options', $list, $item, $account);
  }
  else {
    $options = array();
  }

  $options += array(
    'div_class' => array(),
    'query' => array(),
    'show_admin' => FALSE, // This would show the link to an administrator if TRUE,
  );
  $options['div_class'][] = 'clMain';
  if (!$link || ($item->status != CHECKLIST_ENABLED)) $options['div_class'][] = 'clDisabled';
  $output .= implode(' ', $options['div_class']) . (!empty($item->checked_on) ? ' clChecked' : '') . '"></div>';

  if ((!$link && !$list->completed) || !isset($options['path']) || (($user->uid != $account->uid) && !$options['show_admin'])) {
    $output .= _checklist_clean($item->title);
  }
  else {
    $path = $options['path'];
    unset($options['path']);

    // Try to bring the user back to this page if possible.
    if (empty($options['query'])) {
      $options['query'] = drupal_get_destination();
    }
    $output .= l($item->title, $path, $options);
  }

  if (!empty($item->checked_by) && $item->checked_by != $account->uid) {
    $output .= '<span class="checked-by">' . t('Compled by !name', array('!name' => theme('username', array('account' => user_load($item->checked_by))))) . '</span>';
  }

  if ($item->description) {
    if (!$js_added) {
      drupal_add_js('(function ($) {
      Drupal.behaviors.showChecklistDetails = {
        attach: function(context) {
          $(".clDetails").show().click(function() {
            var ids = $(this).attr("id").substr(9);
            $("#checklistDetail"+ ids).toggle("fast");
          });
          $(".checklistDetails").css("display", "none");;
        }
      };
    }(jQuery));', array('type' => 'inline', 'scope' => JS_DEFAULT));
      $js_added = TRUE;
    }
    $output .= ' <span class="clDetails" id="clDetails' . (!empty($list->instance_id) ? $list->instance_id : $list->clid) . '-' . $item->cliid . '">' . t('more') . '</span></div><div id="checklistDetail' . (!empty($list->instance_id) ? $list->instance_id : $list->clid) . '-' . $item->cliid . '" class="checklistDetails">' . _checklist_clean($item->description, $item->format);
  }

  return $output . '</div>';
}

/**
 * Menu callback for the user checklist tab.
 */
function insourced_user_page($account = NULL) {
  if ($account && (user_access('manage user checklists'))) {
    return drupal_get_form('checklist_manage_user_form', $account);
  }
  global $user;
  return checklist_main_page($user, FALSE);
}

/**
 * AJAX page for checking a box
 * The actual checking happens in the handler for the 'user' item type. However,
 * this may be called when AJAX calls are happening. Just handle it if so.
 */
function insourced_user_check_page($instance, $item) {
  if (isset($_REQUEST['ajax'])) {
    // The user clicked a link for an item that can't be unchecked, thus checked is 1.
    print(drupal_json_encode(array('instance_id' => $instance->instance_id, 'cliid' => $item->cliid, 'checked' => 1, 'uncheck' => 0)));
    exit();
  }
  drupal_goto('checklist');
}

/**
 * Menu callback for checking an admin box.
 */
function insourced_admin_check_page($account, $instance, $item) {
  $result = 1;

  // User already checked the item.
  $uid = db_query('SELECT uid FROM {checklist_user_checks} cuc JOIN {checklist_instances} clin ON cuc.instance_id=clin.instance_id WHERE cuc.instance_id=:instance_id AND cuc.cliid = :cliid AND (cuc.uid = :uid OR clin.group_complete=1) ', array(':uid' => $account->uid, ':cliid' => $item->cliid, ':instance_id' => $instance->instance_id))->fetchField();
  if ($uid) {
    if ($item->allow_uncheck) {
      db_delete('checklist_user_checks')
        ->condition('uid', $uid)
        ->condition('instance_id', $instance->instance_id)
        ->condition('cliid', $item->cliid)
        ->execute();
      $result = 0;
    }
  }
  else {
    _checklist_complete_user_item($instance->instance_id, $item, $account, TRUE, !isset($_REQUEST['ajax']));
  }

  if (isset($_REQUEST['ajax'])) {
    print(drupal_json_encode(array('instance_id' => $instance->instance_id, 'cliid' => $item->cliid, 'checked' => $result, 'uncheck' => $item->allow_uncheck)));
    exit();
  }
  drupal_goto('checklist');
}

/**
 * Form to manage a User's Checklists
 */
function insourced_manage_user_form($form, &$form_state, $account) {
  $lists = _checklist_get_user_lists($account, FALSE);
  //drupal_set_title(t('!name\'s Checklists', array('!name' => theme('username', $account))));
  if (empty($lists)) {
    $form['checklist_empty'] = array(
      '#type' => 'markup',
      '#value' => t('This user has no assigned checklist.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    );
  }
  else {
    foreach ($lists as $list) {
      $form['checklist_' . $list->instance_id] = array(
        '#type' => 'fieldset',
        '#title' => _checklist_clean($list->title),
        '#description' => _checklist_clean($list->description, $list->format),
        '#collapsible' => TRUE,
        '#collapsed' => !empty($list->completed),
      );
      $i_weight = 0;
      foreach ($list->items as $item) {
        $form['checklist_' . $list->instance_id]['clItem_' . $list->instance_id . '_' . $item->cliid] = _checklist_checkbox_form_item(_checklist_clean($item->title), $item->checked_on, $i_weight++, _checklist_clean($item->description, $item->format), ($item->cltid == 'admin'));
      }
    }
    $form['#checklist_account'] = $account;
    $form['#checklist_lists'] = $lists;
    $form['checklist_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save Checklists'),
    );
  }
  return $form;
}

/**
 * Submit function for the checklist_manage_user_form form
 * Saves admin submitted checks
 */
function insourced_manage_user_form_submit($form, &$form_state) {
  $account = $form['#checklist_account'];
  $lists = array();
  // Go through and and add or remove checklist items.
  foreach ($form_state['values'] as $key => $checked) {
    if (substr($key, 0, 7) == 'clItem_') {
      list($junk, $instance_id, $cliid) = explode('_', $key);
      // Assume a list is completed to start.
      if (!isset($lists[$instance_id])) {
        $lists[$instance_id] = TRUE;
      }
      if ($form['checklist_' . $instance_id][$key]['#default_value'] && !$checked) {
        db_delete('checklist_user_checks')
          ->condition('uid', $account->uid)
          ->condition('instance_id', $instance_id)
          ->condition('cliid', $cliid)
          ->execute();
        if ($form['#checklist_lists'][$instance_id]->items[$cliid]->status == CHECKLIST_ENABLED) {
          // Just one item unchecked makes a list not complete.
          $lists[$instance_id] = FALSE;
        }
      }
      elseif ($checked && !$form['checklist_' . $instance_id][$key]['#default_value']) {
        _checklist_complete_user_item ($instance_id, $form['#checklist_lists'][$instance_id]->items[$cliid], $account, FALSE, FALSE);
      } 
      elseif (!$form['checklist_' . $instance_id][$key]['#default_value']) {
        // No change in status, but the item was not checked to start.
        $lists[$instance_id] = FALSE;
      }
    }
  }

  // Go through and update which checklists are completed or not completed.
  foreach ($form['#checklist_lists'] as $list) {
    if (empty($lists[$list->instance_id]) && !empty($list->completed)) {
      $query = db_delete('checklist_complete')
        ->condition('instance_id', $list->instance_id);
      // If this instance can be completed by a group, don't limit to
      // this user.
      if (!$list->group_complete) {
        $query->condition('uid', $account->uid);
      }
      $query->execute();
    }
    elseif (!empty($lists[$list->instance_id]) && empty($list->completed)) {
      _checklist_complete_user_list($list->instance_id, $account);
    }
  }
  drupal_set_message(t('!name\'s checklists updated successfully.', array('!name' => theme('username', array('account' => $account)))));
}
