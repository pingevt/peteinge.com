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
/*
global $base_path, $base_url;
print $base_path;
print '<br />';
print $base_url;
print '<pre>';
print_r($_GET);
print '</pre>';
*/

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
  drupal_add_js( drupal_get_path('theme' , 'peteinge') . '/bootstrap/js/carousel.js');
  drupal_add_js( drupal_get_path('theme' , 'peteinge') . '/bootstrap/js/transition.js');

  $indicator_str = '';
  $slide_str = '';
  $view = views_get_view_result('portfolio', 'block_1');

  foreach ($view as $i => $result) {

    $indicator_str .= '<li data-target="#project-carousel" data-slide-to="' . $i . '" class="';
    if ($i == 0) $indicator_str .= 'active';
    $indicator_str .= '"></li>';

    $string = $result->node_title;
    $string = strtolower($string);
    $string = str_replace(' ', '_', $string);
    $string = preg_replace("[^A-Za-z0-9]", "", $string);

    $image = $result->field_field_front_page_image[0];
    $img_url = file_create_url($image['raw']['uri']);

    //print '<a href="portfolio#'.$string.'" ><img src="'. $img_url .'" alt="" title="" /></a>';

    //print '</div>';

    $slide_str .= '<div class="item ';
    if ($i == 0) $slide_str .= 'active';
    $slide_str .= '">
      <img src="' . $img_url . '" alt="' . $result->node_title . '">';

    //$slide_str .= '<div class="carousel-caption">';
    //$slide_str .= $result->node_title;
    //$slide_str .= '</div>';

    $slide_str .= '
    </div>';
  }

$portfoliio_view = views_get_view('portfolio');
$portfoliio_view->init();
$portfoliio_view->set_display('page');

$portfoliio_view->init_pager();
$portfolio_items_per_page = $portfoliio_view->get_items_per_page();

$portfoliio_view->set_items_per_page(0);
$portfoliio_view->pre_execute();
$portfoliio_view->execute();

$portfolio = $portfoliio_view->result;

?>

<div id="project-carousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <?php print $indicator_str; ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <?php print $slide_str; ?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#project-carousel" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#project-carousel" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>


<div id="home-lower-content">
  <div id="col1">
    <h2>About Me</h2>
    <img src="/<?php print drupal_get_path('theme' , 'peteinge') . '/assets/images/peteinge.png';?>" alt="Pete Inge" title="Pete Inge" />
    <p>I'm Pete Inge. I was born and raised in Nazareth, PA and currently reside
    right outside of Philadelphia. I studied Math and Visual Communication Design at
    Virginia Tech and received a BS in Math and a BFA in Art.</p>

    <p>I enjoy working in digital art and multimedia and exploring new avenues to express
    my creativity digitally. The past few years I have seen my experiences, travel and
    family combine to help develop my work. I fully believe in these things to help
    develop me as a person, as a designer and as a developer.</p>

    <!--<p>I am currently working with Autodesk Maya 2008, Mel and Unity3D gaming engine. I
    am excited about the possibilities of combining my math degree with my artistic, creative
    side. Along with Maya, I am proficient with Actionscript 3, Adobe CS, HTML, CSS,
    PHP, mySQL, drupal, and JavaScript and the list goes on.</p>-->

		<!--<p>I am a designer/programmer with expertise in multiple forms of media. My mission is
		to provide a creative and powerful solution for the needs of my clients by combining
		my expertise in multiple forms of media. Solutions that not only function but are
		also visually pleasing.</p>-->

		<p>My expertise in multiple forms of media provide a creative and powerful solution
		for the needs of my clients. Solutions that not only function but are
		also visually pleasing.</p>

  </div>

  <div id="col2">
    <h2>Latest News</h2>
<?php
    $entity_type = 'node';
    $efq = new EntityFieldQuery();
    $efq->entityCondition('entity_type', $entity_type);
    $efq->propertyCondition('type', 'blog');
    $efq->propertyCondition('status', '1');
    $efq->propertyCondition('promote', '1');
    $efq->propertyCondition('created', time(), '<=');
    $efq->propertyOrderBy('created', 'DESC');
    $efq->range(0, 5);

    $results = $efq->execute();

    foreach ($results['node'] AS $nid => $result) {
      $node = node_load($nid);

      print '<div class="blog-teaser-wrapper">';
      print '<span class="teaser-date">' . format_date($node->created, 'small') . '</span>';
      print '<h3>' . l($node->title, 'node/' . $node->nid) . '</h3>';

      print render(field_view_field('node', $node, 'body', array('label' => 'hidden', 'type' => 'text_trimmed', 'settings' => array('trim_length' => '200'))));

      print '</div>';
    }

?>
  </div>

  <div id="col3">
    <h2>Recent Projects</h2>
<?php
    $entity_type = 'node';
    $efq = new EntityFieldQuery();
    $efq->entityCondition('entity_type', $entity_type);
    $efq->propertyCondition('type', 'projects');
    $efq->propertyCondition('status', '1');
    $efq->propertyCondition('promote', '1');
    $efq->propertyCondition('created', time(), '<=');
    $efq->fieldOrderBy('field_project_date', 'value', 'DESC');
    $efq->range(0, 5);

    $results = $efq->execute();

    foreach ($results['node'] AS $nid => $result) {
      $node = node_load($nid);

      print '<div class="project-teaser-wrapper">';

      print render(field_view_field('node', $node, 'field_thumbnail_banner', array('label' => 'hidden', 'type' => 'image', 'settings' => array('image_style' => 'front_project_banner'))));

      $string = $node->title;
      $string = strtolower($string);
      $string = str_replace(' ', '_', $string);
      $string = preg_replace("[^A-Za-z0-9]", "", $string);

      $page_num = 0;
      foreach ($portfolio AS $i => $p) {
        if ($p->nid == $node->nid) {
          $page_num = floor(($i / $portfolio_items_per_page));
          break;
        }
      }
      print '<h3>' . l($node->title, 'portfolio', array('query' => array('page' => $page_num), 'fragment' => $string)) . '</h3>';

      //print render(field_view_field('node', $node, 'body', array('label' => 'hidden', 'type' => 'text_trimmed', 'settings' => array('trim_length' => '200'))));

      print '</div>';
    }
?>
  </div>
</div>

<?php
}


?>
