<?php

/**
 * Override or insert variables into the maintenance page template.
 */
function peteinge_preprocess_maintenance_page(&$vars) {
  // While markup for normal pages is split into page.tpl.php and html.tpl.php,
  // the markup for the maintenance page is all in the single
  // maintenance-page.tpl.php template. So, to have what's done in
  // peteinge_preprocess_html() also happen on the maintenance page, it has to be
  // called here.
  peteinge_preprocess_html($vars);
}

/**
 * Override or insert variables into the html template.
 */
function peteinge_preprocess_html(&$vars) {
  // Add conditional CSS for IE8 and below.
  drupal_add_css(path_to_theme() . '/assets/css/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 8', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE7 and below.
  drupal_add_css(path_to_theme() . '/assets/css/ie7.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE6.
  drupal_add_css(path_to_theme() . '/assets/css/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 6', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
}

/**
 * Override or insert variables into the page template.
 */
function peteinge_preprocess_page(&$vars) {
  $vars['primary_local_tasks'] = $vars['tabs'];
  unset($vars['primary_local_tasks']['#secondary']);
  $vars['secondary_local_tasks'] = array(
    '#theme' => 'menu_local_tasks',
    '#secondary' => $vars['tabs']['#secondary'],
  );

  $count = 1;
  foreach($vars['main_menu'] as $i=>$item) {

    if($count != count($vars['main_menu'])) $vars['main_menu'][$i]['title'] .= ' /';

    $count++;
  }
}

/**
 * Display the list of available node types for node creation.
 */
function peteinge_node_add_list($variables) {
  $content = $variables['content'];
  $output = '';
  if ($content) {
    $output = '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="clearfix">';
      $output .= '<span class="label">' . l($item['title'], $item['href'], $item['localized_options']) . '</span>';
      $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  else {
    $output = '<p>' . t('You have not created any content types yet. Go to the <a href="@create-content">content type creation page</a> to add a new content type.', array('@create-content' => url('admin/structure/types/add'))) . '</p>';
  }
  return $output;
}

/**
 * Overrides theme_admin_block_content().
 *
 * Use unordered list markup in both compact and extended mode.
 */
function peteinge_admin_block_content($variables) {
  $content = $variables['content'];
  $output = '';
  if (!empty($content)) {
    $output = system_admin_compact_mode() ? '<ul class="admin-list compact">' : '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="leaf">';
      $output .= l($item['title'], $item['href'], $item['localized_options']);
      if (isset($item['description']) && !system_admin_compact_mode()) {
        $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      }
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  return $output;
}

/**
 * Override of theme_tablesort_indicator().
 *
 * Use our own image versions, so they show up as black and not gray on gray.
 */
function peteinge_tablesort_indicator($variables) {
  $style = $variables['style'];
  $theme_path = drupal_get_path('theme', 'seven');
  if ($style == 'asc') {
    return theme('image', array('path' => $theme_path . '/images/arrow-asc.png', 'alt' => t('sort ascending'), 'width' => 13, 'height' => 13, 'title' => t('sort ascending')));
  }
  else {
    return theme('image', array('path' => $theme_path . '/images/arrow-desc.png', 'alt' => t('sort descending'), 'width' => 13, 'height' => 13, 'title' => t('sort descending')));
  }
}

/**
 * Implements hook_css_alter().
 */
function peteinge_css_alter(&$css) {
  // Use Seven's vertical tabs style instead of the default one.
  if (isset($css['misc/vertical-tabs.css'])) {
    $css['misc/vertical-tabs.css']['data'] = drupal_get_path('theme', 'seven') . '/vertical-tabs.css';
  }
  if (isset($css['misc/vertical-tabs-rtl.css'])) {
    $css['misc/vertical-tabs-rtl.css']['data'] = drupal_get_path('theme', 'seven') . '/vertical-tabs-rtl.css';
  }
  // Use Seven's jQuery UI theme style instead of the default one.
  if (isset($css['misc/ui/jquery.ui.theme.css'])) {
    $css['misc/ui/jquery.ui.theme.css']['data'] = drupal_get_path('theme', 'seven') . '/jquery.ui.theme.css';
  }
}





function peteinge_front_page_content() {

?>
  <div id="slides">
<div class="slides_container">
<?php
// add the library and add the js
drupal_add_js('sites/all/libraries/slides/source/slides.min.jquery.js');
drupal_add_js("(function($) {
                      $(function(){
			// Set starting slide to 1
			var startSlide = 1;
			// Get slide number if it exists
			if (window.location.hash) {
				startSlide = window.location.hash.replace('#','');
			}
			// Initialize Slides
			$('#slides').slides({
				preload: true,
				preloadImage: 'sites/all/libraries/slides/examples/Standard/img/loading.gif',
				generatePagination: true,
				play: 5000,
				pause: 2500,
				hoverPause: true,
				// Get the starting slide
				start: startSlide,
				animationComplete: function(current){
					// Set the slide number as a hash
					window.location.hash = '#' + current;
				}
			});
		});})(jQuery);", 'inline');

$view = views_get_view_result('portfolio', 'block_1');
//print_r($view);
foreach($view as $i=> $result) {

  print '<div class="slide">';
$string = $result->node_title;
$string = strtolower($string);
$string = str_replace(' ', '_', $string);
$string = preg_replace("[^A-Za-z0-9]", "", $string);

  $image = $result->field_field_front_page_image[0];
  $img_url = file_create_url($image['raw']['uri']);

  print '<a href="portfolio#'.$string.'" ><img src="'. $img_url .'" alt="" title="" /></a>';

  print '</div>';
}
?>
</div>
<a href="#" class="prev"><i class="icon-left" ></i></a>
<a href="#" class="next"><i class="icon-right" ></i></a>

</div>

<div id="home-lower-content">
  <div id="col1">
    <h2>About Me</h2>
  </div>

  <div id="col2">
    <h2>Latest News</h2>
  </div>

  <div id="col3">
    <h2>Recent Projects</h2>
  </div>
</div>

<?php
}


?>
