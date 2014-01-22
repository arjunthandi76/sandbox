<?php
/**
 * This is a copy of cores field.tpl.php modified to support HTML5. The main
 * difference to Drupal core is that field labels are treated like headings. In
 * the context of HTML5 we need to account for sectioning, so this template, like
 * is counterpart adaptivetheme_field() conditionally supplies the top level
 * element as either <section> (field label is showing) or <div> (field label
 * is hidden). "Hidden labels" is a misnomer and implies they are output and
 * hidden via CSS - not so, if a label is hidden it does not print.
 *
 * Additionally field labels are wrapped in h2 elements to improve the outline.
 *
 * Useage:
 * This file is not used by default by Adaptivetheme which is why this is a text
 * file - it is here to serve as an example if you want to override field
 * templates using suggestions.
 *
 * Adaptivetheme overrides theme_field() to achieve the same markup you see
 * here because functions are 5 times faster than templates. theme_field() is
 * called often in Drupal 7 sites so we want to keep things as fast as possible.
 *
 * Taxonomy Fields:
 * Adaptivetheme outputs taxonomy term fields as unordered lists - it
 * does this in adaptivetheme_field__taxonomy_term_reference().
 *
 * Image Fields:
 * Image fields are overridden in adaptivetheme_field__image(), instead of
 * the regular div container for images we use <figure> from HTML5.
 * The template can print captions also, using the <figcaption> element, again
 * from HTML5. You need to enable the "Image" theme settings in "Site Tweaks"
 * to enable and configure the captioning features. Captions use the #title
 * field for the caption text, so you must have titles enabled on image fields
 * for this to work.
 *
 * Adaptivetheme variables:
 * - $field_view_mode: The view mode so we can test for it inside a template.
 * - $image_caption_teaser: Boolean value to test for a theme setting.
 * - $image_caption_full: Boolean value to test for a theme setting.
 * - $is_mobile: Bool, requires the Browscap module to return TRUE for mobile
 *   devices. Use to test for a mobile context.
 *
 * Available variables:
 * - $items: An array of field values. Use render() to output them.
 * - $label: The item label.
 * - $label_hidden: Whether the label display is set to 'hidden'.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - field: The current template type, i.e., "theming hook".
 *   - field-name-[field_name]: The current field name. For example, if the
 *     field name is "field_description" it would result in
 *     "field-name-field-description".
 *   - field-type-[field_type]: The current field type. For example, if the
 *     field type is "text" it would result in "field-type-text".
 *   - field-label-[label_display]: The current label position. For example, if
 *     the label position is "above" it would result in "field-label-above".
 *
 * Other variables:
 * - $element['#object']: The entity to which the field is attached.
 * - $element['#view_mode']: View mode, e.g. 'full', 'teaser'...
 * - $element['#field_name']: The field name.
 * - $element['#field_type']: The field type.
 * - $element['#field_language']: The field language.
 * - $element['#field_translatable']: Whether the field is translatable or not.
 * - $element['#label_display']: Position of label display, inline, above, or
 *   hidden.
 * - $field_name_css: The css-compatible field name.
 * - $field_type_css: The css-compatible field type.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess_field()
 * @see theme_field()
 * @see adaptivetheme_preprocess_field()
 * @see adaptivetheme_field()
 * @see adaptivetheme_field__taxonomy_term_reference()
 * @see adaptivetheme_field__image()
 */
?>


    <?php
      $output_str = "";
      $offer_exists = false;
    ?>

    <?php foreach ($items as $delta => $item) : ?>
    <?php
    //dpm($item);
      $output_str .= "";
      $item_key = key($item['node']);
      $title = $item['node'][$item_key]['field_offer_expiry_date']['#object']->title;
      $description = $item['node'][$item_key]['body']['#items'][0]['value'];
      $description = substr($description,0,120)  . " ...";
      $image_uri = $item['node'][$item_key]['field_image']['#items'][0]['uri'];
      $startdate =  strtotime($item['node'][$item_key]['field_offer_expiry_date']['#object']->field_offer_expiry_date[LANGUAGE_NONE][0]['value']);
      $enddate = strtotime($item['node'][$item_key]['field_offer_expiry_date']['#object']->field_offer_expiry_date[LANGUAGE_NONE][0]['value2']);
      //dpm($item['node'][$item_key]);
      //$output_str .= $label . $value . " | ";
      // Get link to offer node
      $options = array('absolute' => TRUE);
      $nid = $item_key; // Node ID
      $url = url('node/' . $nid, $options);

      $timeago =  format_interval($enddate-time());
      if ($enddate > time()) {
        $offer_exists = true;
        global $base_url;
        $wrapper = file_stream_wrapper_get_instance_by_uri($image_uri);
        $image = $wrapper->getDirectoryPath() . "/" . file_uri_target($image_uri);    
        $image = '<img alt="'. $title .'" src="'. $base_url . "/".$image .'">';
        if (time()>$startdate) {
          $timeago = format_interval(time()-$startdate);
          $timetext = "Offer is starting in " . $timeago ;
        } else {
          $timeago = format_interval($enddate-time());
          $timetext = "Offer expires in " . $timeago ;
        }
        $output_str .= '
          <div class="offer">
            <div class="picture">'.$image.'</div>          
            <div class="main">
              <div class="title"><a href="'.$url.'">'.$title.'</a></div>
              <div class="description">'.$description.'</div>
              <div class="timetogo">'.$timetext.'</div>
            </div>
          </div>';
      }
    ?>
    <?php endforeach; ?>

    <?php if ($offer_exists) { ?>
      <<?php print $tag; ?> class="<?php print $classes; ?>"<?php print $attributes; ?>>
        <?php if (!$label_hidden) : ?>
          <h2><?php print $label ?></h2>
        <?php endif; ?>
        <div id ="embedoffers" class="field-items"<?php print $content_attributes; ?>>
          <?php
            print $output_str;
          ?>    
        </div>
      </<?php print $tag; ?>>
    <?php } ?>



