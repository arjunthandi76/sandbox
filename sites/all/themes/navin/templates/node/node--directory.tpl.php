<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
 
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix container-12"<?php print $attributes; ?>>





  <div class="grid-8 grid_8 content"<?php print $content_attributes; ?>>
		<?php
    dpm($node);
		// We hide the comments and links now so that we can render them later.
		hide($content['comments']);
		hide($content['links']);
		
		if (!empty($node->field_homepage_link) && isset($node->field_homepage_link[LANGUAGE_NONE][0]['value'])) {
		  print '<a class="homepage-link-wrapper" href="' . $node->field_homepage_link[LANGUAGE_NONE][0]['value'] . '">';
		}
		//dpm($node->field_tut_taxonomy_typ[LANGUAGE_NONE][0]['taxonomy_term']->name);
   // dpm($node);
		print '<div class="homepage-content-wrapper">';
		print '<div id="dir-top-header">';?>
    <?php print render($title_prefix); ?>
    <?php if (!$page): ?>
      <h1<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h1>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
    <?php 
			if (!empty($node->field_dir_address[LANGUAGE_NONE][0]['street']) && isset($node->field_dir_address[LANGUAGE_NONE][0]['street'])) {
        $address= $node->field_dir_address[LANGUAGE_NONE][0]['street'] . ", ";
        if ($node->field_dir_address[LANGUAGE_NONE][0]['additional'] !==""){
          $address .= $node->field_dir_address[LANGUAGE_NONE][0]['additional']. ", " ;
        }
        $address .= $node->field_dir_address[LANGUAGE_NONE][0]['city'] . ", " . $node->field_dir_address[LANGUAGE_NONE][0]['postal_code'];
			  print '<div class="field address"><div class="field-title"></div><div class="field-value"> '. $address .'</div></div>';
			}   
      /*
			if (!empty($node->field_tut_taxonomy_typ) && isset($node->field_tut_taxonomy_typ[LANGUAGE_NONE][0]['taxonomy_term']->name)) {
			  print '<div class="field  tut-f-type"><div class="field-title">Category:</div><div class="field-value">'. $node->field_tut_taxonomy_typ[LANGUAGE_NONE][0]['taxonomy_term']->name .'</div></div>';
			}    
      */
			if (!empty($node->field_dir_website) && isset($node->field_dir_website[LANGUAGE_NONE][0]['url'])) {
			  print '<div class="field website"><div class="field-value"><a href="'. $node->field_dir_website[LANGUAGE_NONE][0]['display_url'] .'">'.$node->field_dir_website[LANGUAGE_NONE][0]['title'].'</a></div></div>';
			}     
      
  
		print '</div> '; //dir-top-header

    if (!empty($content['body']) && isset($content['body'])) {
      print '<div class="field"><div class="field-value">'. render($content['body']) .'</div></div>';
    }    
    $termstext = "";
    
    if (!empty($node->field_dir_category[LANGUAGE_NONE][0])) {
        $termids=array();
        foreach ($node->field_dir_category[LANGUAGE_NONE] as $term) {
          $termids[] = $term['tid'];
        }
        $terms = taxonomy_term_load_multiple($termids, array());
        foreach ($terms as $term) {
          $vocname=str_replace ("_", "-", $term->vocabulary_machine_name);
          //$termstext .= '<a href="/'.$vocname .'/'." dd "   . '">'. $term->name . '</a>, ';
          $termstext .= l($term->name, 'taxonomy/term/' . $term->tid) .', ';
        }      
        $termstext = rtrim($termstext, ", ");
			  print '<div class="field"><div class="field-title"><h2>Category</h2> </div><div class="field-value">'. $termstext .'</div></div>';
    }            
    
    $termstext = "";
    if (!empty($node->field_dir_prod_services[LANGUAGE_NONE][0])) {
        $termids=array();
        foreach ($node->field_dir_prod_services[LANGUAGE_NONE] as $term) {
          $termids[] = $term['tid'];
        }
        $terms = taxonomy_term_load_multiple($termids, array());
        foreach ($terms as $term) {
          $vocname=str_replace ("_", "-", $term->vocabulary_machine_name);
          $termstext .= l($term->name, 'taxonomy/term/' . $term->tid) .', ';
        }      
        $termstext = rtrim($termstext, ", ");
			  print '<div class="field"><div class="field-title"><h2>Products and Services</h2> </div><div class="field-value">'. $termstext .'</div></div>';
    }      
    
    
		print '</div>'; 
		

		?>

  </div>

      <div class="grid-4 grid_4 dir-right-col"><?php    print views_embed_view('location_gmap', $display_id = 'loc_gmap');?></div>
</div>