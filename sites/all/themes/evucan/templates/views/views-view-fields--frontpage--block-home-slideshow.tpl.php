<?php
/**
 * @file views-view-fields.tpl.php
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->wrapper_prefix: A complete wrapper containing the inline_html to use.
 *   - $field->wrapper_suffix: The closing tag for the wrapper.
 *   - $field->separator: an optional separator that may appear before a field.
 *   - $field->label: The wrap label text to use.
 *   - $field->label_html: The full HTML of the label to use including
 *     configured element type.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */
?>

<?php 
//dpm($fields);
//dpm($row);
    $title       = $fields['title']->content;
    $description = $fields['body']->content;

    $step_media = "";


    if (!is_null($row->field_field_image [0]['raw']['filename'])) {
      $step_media = $fields['field_field_image']->content;
        global $base_url;
        $image_uri = $row->field_field_image [0]['raw']['uri'];
        $image_uri = image_style_path ($row->field_field_image [0]['rendered']['#image_style'], $image_uri);
        $wrapper = file_stream_wrapper_get_instance_by_uri($image_uri);
                //dpm($wrapper);
        $image = $wrapper->getDirectoryPath() . "/" . file_uri_target($image_uri);    

        $cck_type = $fields['type']->content;
        $cck_type_name = $row->node_type;
        $created = $fields['created']->content;
        $termname = $row->field_field_shr_tax_menu_category[0]['rendered']['#options']['entity']->name;
        $termid = $row->field_field_shr_tax_menu_category[0]['rendered']['#options']['entity']->tid;
        $mainmenulink = l($termname, 'taxonomy/term/' . $termid);

          $termtitle = $row->field_field_shr_tax_menu_category[0]['rendered']['#options']['entity']->name;
          $catlinkname = strtolower ($row->field_field_shr_tax_menu_category[0]['rendered']['#options']['entity']->name);
          $output_str .=  l($termtitle, 'posts/' . $catlinkname);
          $mainmenulink = $output_str;

          $ccklink = '<a href="/'. strtolower($cck_type_name) .'/posts">'.ucfirst($cck_type_name).'</a>';
    print 
      '<div id="frontpage-slide-container"> 

        <div class="main">
          <div class="title">' . render($title) . '</div>
          <div class="description">'.  $description . '</div>
          <div class="footer">'. $mainmenulink . ' | ' . $ccklink . ' | Posted: ' . $created . ' | <a href="'. drupal_get_path_alias("node/" . $row->nid) . '">Read more</a></div>
        </div>
        <div class="image"><img src="'. $base_url . '/'. $image .'"> <div class="transbox"><p>'.$ccklink.'</p></div></div>';
   print '</div>';
    }
    ?>
