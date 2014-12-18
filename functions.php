<?php
/**
 * the1000th LinkBlog functions and definitions
 *
 * @package the1000th LinkBlog
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'the1000th_link_blog_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function the1000th_link_blog_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on the1000th LinkBlog, use a find and replace
	 * to change 'the1000th-link-blog' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'the1000th-link-blog', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'the1000th-link-blog' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'the1000th_link_blog_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // the1000th_link_blog_setup
add_action( 'after_setup_theme', 'the1000th_link_blog_setup' );


function get_content_link( $content = false, $echo = false ){
    if ( $content === false )
        $content = get_the_content(); 

    $content = preg_match_all( '/hrefs*=s*[\"\']([^\"\']+)/', $content, $links );
    $content = $links[1][0];

    if ( empty($content) ) {
    	$content = false;
    }

    return $content;
}




/* php used in content-link.php - we define them here: 
echo $gs_version
echo $file_to_fetch;	
get_content_link( get_the_content())
*/

function post_screenshot_generate($url, $w=1024, $h=768, $clipw=400, $cliph=400, $debug=false) {

    $result = array();

	// $url = get_content_link( get_the_content() );

   
    $url_segs = parse_url($url); 

    if (!isset($url_segs['host'])) {
        $result['error'] = "no host in URL: $url";
        return $result;
    }

    $here = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    $phantomjobsdir = $here . 'phantomjobsdir' . DIRECTORY_SEPARATOR;
    $imagesdir = $here . 'imagesdir' . DIRECTORY_SEPARATOR;

    $result['here'] = $here;
    $result['phantomjobsdir'] = $phantomjobsdir;
    $result['imagesdir'] = $imagesdir;
    
    if (!is_dir($phantomjobsdir)) {
        mkdir($phantomjobsdir);
        file_put_contents($phantomjobsdir . 'index.php', '<?php exit(); ?>');
    }
    
    if (!is_dir($imagesdir)) {
        mkdir($imagesdir);
        file_put_contents($imagesdir . 'index.php', '<?php exit(); ?>');
    }

    $url = strip_tags($url);
    $url = str_replace(';', '', $url);
    $url = str_replace('"', '', $url);
    $url = str_replace('\'', '/', $url);
    $url = str_replace('<?', '', $url);
    $url = str_replace('<?', '', $url);
    $url = str_replace('\077', ' ', $url);

    $ssimagefile = $url_segs['host'] . md5($url) . '_' . $w . '_' . $h . '.png';
    $ssimage_fullpath_file = $imagesdir . $ssimagefile;
    
    $url = escapeshellcmd($url);
    
    $result['url-escaped'] = $url;
    $result['ssimagefile'] = $ssimagefile;
    $result['ssimage_fullpath_file'] = $ssimage_fullpath_file;


    if (!is_file($ssimage_fullpath_file) ) {
    
            $result['note'][] = "the full pathed file: $ssimage_fullpath_file does not exist";

            $src = "

            var page = require('webpage').create();
            page.viewportSize = { width: {$w}, height: {$h} };

            ";

            if (isset($clipw) && isset($cliph)) {
                $src .= "page.clipRect = { top: 0, left: 0, width: {$clipw}, height: {$cliph} };";
            }

            $src .= "

            page.open('{$url}', function () {
                page.render('{$ssimagefile}');
                phantom.exit();
            });


            ";

            $phantomjob_file = $phantomjobsdir . $url_segs['host'] . md5($src) . '.js';
            
            $result['phantomjob_file'] = $phantomjob_file;

            file_put_contents($phantomjob_file, $src);
                       

            $exec = "/usr/bin/phantomjs " . $phantomjob_file;
            $escaped_command = escapeshellcmd($exec);
            
            $result['note'][] = "Executing: $exec";

            exec($escaped_command);
            
            // if (is_file($here . $ssimagefile)) {
            if (is_file($ssimagefile)) {

                $result['note'][] = "the file [here . ssimagefile]: ". $here . $ssimagefile . " does now exist";
                $result['note'][] = "Trying to rename " . $ssimagefile . " to  $ssimage_fullpath_file";
                // rename($here . $ssimagefile, $ssimage_fullpath_file);            }
                rename($ssimagefile, $ssimage_fullpath_file);            }
        }

       

        if (is_file($ssimage_fullpath_file)) {

                $result['note'][] = "The file in ssimage_fullpath_file - $ssimage_fullpath_file - now does exist";
                $result['note'][] = "Will return its basename: " . basename($ssimage_fullpath_file); 

        		$result['ss'] = basename($ssimage_fullpath_file);

                $im = imagecreatefrompng($ssimage_fullpath_file);
                if($im && imagefilter($im, IMG_FILTER_GRAYSCALE))
                {
                  imagepng($im, $ssimage_fullpath_file.'.gs.png');

                  $result['note'][] = "The [gs] parameter is: " . basename($ssimage_fullpath_file). ".gs.png";
                  $result['gs']= basename($ssimage_fullpath_file). ".gs.png";
                }
                else
                {
                    $result['gs'] = "";
                }

                imagedestroy($im);


                return $result;

        } else {
            $result['note'][] = 'The file ' . $ssimage_fullpath_file . 'does NOT exist';
            return $result;
        }
}





// post_screenshot_generate("http://444.hu", 1024, 768, 640, 480, true);





/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function the1000th_link_blog_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'the1000th-link-blog' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'the1000th_link_blog_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function the1000th_link_blog_scripts() {
	wp_enqueue_style( 'the1000th-link-blog-style', get_stylesheet_uri() );

	wp_enqueue_script( 'the1000th-link-blog-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'the1000th-link-blog-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'the1000th_link_blog_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
