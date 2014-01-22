<?php

/**
 * @file
 * Implimentation of hook_form_system_theme_settings_alter()
 *
 * To use replace "evucan" with your themeName and uncomment by
 * deleting the comment line to enable.
 *
 * @param $form: Nested array of form elements that comprise the form.
 * @param $form_state: A keyed array containing the current state of the form.
 */
/* -- Delete this line to enable.
function evucan_form_system_theme_settings_alter(&$form, &$form_state)  {
  // Your knarly custom theme settings go here...
}
// */

function evucan_form_system_theme_settings_alter(&$form, $form_state) { 
/** alter theme settings form **/
drupal_add_css(drupal_get_path('theme', 'evucan') . '/css/admin.custom.styles.css', array('group' => CSS_THEME, 'weight' => 100)); 
}
