<?php

/**
 * @file
 * Process theme data.
 *
 * Use this file to run your theme specific implimentations of theme functions,
 * such preprocess, process, alters, and theme function overrides.
 *
 * Preprocess and process functions are used to modify or create variables for
 * templates and theme functions. They are a common theming tool in Drupal, often
 * used as an alternative to directly editing or adding code to templates. Its
 * worth spending some time to learn more about these functions - they are a
 * powerful way to easily modify the output of any template variable.
 * 
 * Preprocess and Process Functions SEE: http://drupal.org/node/254940#variables-processor
 * 1. Rename each function and instance of "evucan" to match
 *    your subthemes name, e.g. if your theme name is "footheme" then the function
 *    name will be "footheme_preprocess_hook". Tip - you can search/replace
 *    on "evucan".
 * 2. Uncomment the required function to use.
 */


/**
 * Preprocess variables for the html template.
 */
/* -- Delete this line to enable.
function evucan_preprocess_html(&$vars) {
  global $theme_key;

  // Two examples of adding custom classes to the body.
  
  // Add a body class for the active theme name.
  // $vars['classes_array'][] = drupal_html_class($theme_key);

  // Browser/platform sniff - adds body classes such as ipad, webkit, chrome etc.
  // $vars['classes_array'][] = css_browser_selector();

}
// */


/**
 * Process variables for the html template.
 */
/* -- Delete this line if you want to use this function
function evucan_process_html(&$vars) {
}
// */


/**
 * Override or insert variables for the page templates.
 */
/* -- Delete this line if you want to use these functions
function evucan_preprocess_page(&$vars) {
}
function evucan_process_page(&$vars) {
}
// */


/**
 * Override or insert variables into the node templates.
 */
/* -- Delete this line if you want to use these functions
function evucan_preprocess_node(&$vars) {
}
function evucan_process_node(&$vars) {
}
// */


/**
 * Override or insert variables into the comment templates.
 */
/* -- Delete this line if you want to use these functions
function evucan_preprocess_comment(&$vars) {
}
function evucan_process_comment(&$vars) {
}
// */


/**
 * Override or insert variables into the block templates.
 */
/* -- Delete this line if you want to use these functions
function evucan_preprocess_block(&$vars) {
}
function evucan_process_block(&$vars) {
}
// */


function evucan_fields_info_alter(&$fields, $entity_type) {
  print "blonny";
  if (isset($fields['title'])) {
    $fields['title']['title'] = t('My title');
  }
}


function evucan_preprocess_field(&$vars, $hook) {
    //print "evucan_preprocess_field-1";
  $element = $vars['element'];
 // if ($ds_configuration_settings_exist) {
    //print "evucan_preprocess_field-2";
    if (isset($vars['ds-config'])) {
      $ds_suggestions = array(
        'field__ds',
        'field__ds__' . $element['#field_type'],
        'field__ds__' . $element['#bundle'],
        'field__ds__' . $element['#field_name'],      
        'field__ds__' . $element['#field_name'] . '__' . $element['#bundle'],
      );
      $vars['theme_hook_suggestions'] = array_merge($vars['theme_hook_suggestions'], $ds_suggestions);
    }
    //dpm($vars['theme_hook_suggestions']);
 // }
}