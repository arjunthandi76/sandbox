<?php

/**
 * A unique autocomplete form which looks up nodes by title in the node table, but must keep track of the nid, because
 * titles are certainly not guaranteed to be unique.
 *
 * @param $form
 * @param $form_state
 * @return array
 */
function ajax_example_unique_autocomplete($form, &$form_state) {

  $form['info'] = array(
    '#markup' => '<div>' . t("This example does a node autocomplete by title. The difference between this and a username autocomplete is that the node title may not be unique, so we have to use the nid for uniqueness, placing it in a parseable location in the textfield.") . '</div>',
  );

  $form['node'] = array(
    '#type' => 'textfield',
    '#title' => t('Choose a node by title'),
    // The autocomplete path is provided in hook_menu in ajax_example.module.
    '#autocomplete_path' => 'examples/ajax_example/unique_node_autocomplete_callback',
  );

  $form['actions'] = array(
    '#type' => 'actions'
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

/**
 * Validate handler to convert our string like "Some node title [3325]" into a nid.
 *
 * In case the user did not actually use the autocomplete or have a valid string there, we'll try to look
 * up a result anyway giving it our best guess.
 *
 * Since the user chose a unique node, we must now use the same one in our submit handler, which means we need to look
 * in the string for the nid.
 *
 * @param $form
 * @param $form_state
 */
function ajax_example_unique_autocomplete_validate($form, &$form_state) {
  $title = $form_state['values']['node'];
  $matches = array();
  $nid = 0;

  // This preg_match() looks for the last pattern like [33334] and if found extracts the numeric portion.
  $result = preg_match('/\[([0-9]+)\]$/', $title, $matches);
  if ($result > 0) {
    // If $result is nonzero, we found a match and can use it as the index into $matches.
    $nid = $matches[$result];
    // Verify that it's a valid nid.
    $node = node_load($nid);
    if (empty($node)) {
      form_error($form['node'], t('Sorry, no node with nid %nid can be found', array('%nid' => $nid)));
      return;
    }
  }
  // BUT: Not everybody will have javascript turned on, or they might hit ESC and not use the autocomplete values
  // offered. In that case, we can attempt to come up with a useful value. This is not absolutely necessary,
  // and we *could* just emit a form_error() as below.
  else {
    $nid = db_select('node')
      ->fields('node', array('nid'))
      ->condition('title', db_like($title) . '%', 'LIKE')
      ->range(0, 1)
      ->execute()
      ->fetchField();
  }

  // Now, if we somehow found a nid, assign it to the node. If we failed, emit an error.
  if (!empty($nid)) {
    $form_state['values']['node'] = $nid;
  }
  else {
    form_error($form['node'], t('Sorry, no node starting with %title can be found', array('%title' => $title)));
  }
}

/**
 * Submit handler for node lookup unique autocomplete example.
 *
 * Here the nid has already been placed in $form_state['values']['node'] by the validation handler.
 *
 * @param $form
 * @param $form_state
 */
function ajax_example_unique_autocomplete_submit($form, &$form_state) {
  $node = node_load($form_state['values']['node']);
  drupal_set_message(t('You found node %nid with title %title', array('%nid' => $node->nid, '%title' => $node->title)));
}

/**
 * Autocomplete callback for nodes by title.
 *
 * Searches for a node by title, but then identifies it by nid, so the actual returned value can be used later by
 * the form.
 *
 * The returned $matches array has
 * * key: The title, with the identifying nid in brackets, like "Some node title [3325]"
 * * value: the title which will is displayed in the autocomplete pulldown.
 *
 * Note that we must use a key style that can be parsed successfully and unambiguously. For example, if we might
 * have node titles that could have [3325] in them, then we'd have to use a more restrictive token.
 *
 * @param $string
 *   The string that will be searched.
 */
function ajax_example_unique_node_autocomplete_callback($string = "") {
  $matches = array();
  if ($string) {
    $result = db_select('node')
      ->fields('node', array('nid', 'title'))
      ->condition('title', db_like($string) . '%', 'LIKE')
      ->range(0, 10)
      ->execute();
    foreach ($result as $node) {
      $matches[$node->title . " [$node->nid]"] = check_plain($node->title);
    }
  }

  drupal_json_output($matches);
}









/**
 *
 * AJAX callback for author form element.
 *
 * @param $form
 * @param $form_state
 * @return array
 */
function ajax_example_node_by_author_ajax_callback($form, $form_state) {
  return $form['node'];
}

/**
 * Validate handler to convert our string like "Some node title [3325]" into a nid.
 *
 * In case the user did not actually use the autocomplete or have a valid string there, we'll try to look
 * up a result anyway giving it our best guess.
 *
 * Since the user chose a unique node, we must now use the same one in our submit handler, which means we need to look
 * in the string for the nid.
 *
 * This handler looks complex because it's ambitious (and tries to punt and find a node if they've entered a valid
 * username and part of a title), but you *could* just do a form_error() if nothing were found,
 * forcing people to use the autocomplete to look up the relevant items.
 *
 * @param $form
 * @param $form_state
 */
function ajax_example_node_by_author_autocomplete_validate($form, &$form_state) {
  $title = $form_state['values']['node'];
  $author = $form_state['values']['author'];
  $matches = array();
  $nid = 0;

  $account = user_load_by_name($author); // We must have a valid user.
  if (empty($account)) {
    form_error($form['author'], t('You must choose a valid author username'));
    return;
  }
  // This preg_match() looks for the last pattern like [33334] and if found extracts the numeric portion.
  $result = preg_match('/\[([0-9]+)\]$/', $title, $matches);
  if ($result > 0) {
    // If $result is nonzero, we found a match and can use it as the index into $matches.
    $nid = $matches[$result];
    // Verify that it's a valid nid.
    $node = node_load($nid);
    if (empty($node)) {
      form_error($form['node'], t('Sorry, no node with nid %nid can be found', array('%nid' => $nid)));
      return;
    }
  }
  // BUT: Not everybody will have javascript turned on, or they might hit ESC and not use the autocomplete values
  // offered. In that case, we can attempt to come up with a useful value. This is not absolutely necessary,
  // and we *could* just emit a form_error() as below. Here we'll find the *first* matching title and assume
  // that is adequate.
  else {
    $nid = db_select('node')
      ->fields('node', array('nid'))
      ->condition('uid', $account->uid)
      ->condition('title', db_like($title) . '%', 'LIKE')
      ->range(0, 1)
      ->execute()
      ->fetchField();
  }

  // Now, if we somehow found a nid, assign it to the node. If we failed, emit an error.
  if (!empty($nid)) {
    $form_state['values']['node'] = $nid;
  }
  else {
    form_error($form['node'], t('Sorry, no node starting with %title can be found', array('%title' => $title)));
  }
}

/**
 * Submit handler for node lookup unique autocomplete example.
 *
 * Here the nid has already been placed in $form_state['values']['node'] by the validation handler.
 *
 * @param $form
 * @param $form_state
 */
function ajax_example_node_by_author_autocomplete_submit($form, &$form_state) {
  $node = node_load($form_state['values']['node']);
  $account = user_load($node->uid);
  drupal_set_message(t('You found node %nid with title !title_link, authored by !user_link',
    array('%nid' => $node->nid, '!title_link' => l($node->title, 'node/' . $node->nid), '!user_link' => theme('username', array('account' => $account)))));
}

/**
 * Autocomplete callback for nodes by title but limited by author.
 *
 * Searches for a node by title given the passed-in author username.
 *
 * The returned $matches array has
 * * key: The title, with the identifying nid in brackets, like "Some node title [3325]"
 * * value: the title which will is displayed in the autocomplete pulldown.
 *
 * Note that we must use a key style that can be parsed successfully and unambiguously. For example, if we might
 * have node titles that could have [3325] in them, then we'd have to use a more restrictive token.
 *
 * @param $author_uid
 *   The author username to limit the search.
 * @param $string
 *   The string that will be searched.
 */
function ajax_example_node_by_author_node_autocomplete_callback($author_uid, $string = "") {
  $matches = array();
  if ($author_uid > 0 && trim($string)) {
    $result = db_select('node')
      ->fields('node', array('nid', 'title'))
      ->condition('uid', $author_uid)
      ->condition('title', db_like($string) . '%', 'LIKE')
      ->range(0, 10)
      ->execute();
    foreach ($result as $node) {
      $matches[$node->title . " [$node->nid]"] = check_plain($node->title);
    }
  }

  drupal_json_output($matches);
}
