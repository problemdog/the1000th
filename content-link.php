<?php
/**
 * @package the1000th LinkBlog
 */
?>

<?php
$imgs = post_screenshot_generate(get_content_link( get_the_content() ), 1024, 768, 640, 480, false);
echo "<!--";
print_r($imgs);
echo "-->";

?>
<div class="screenshot_div " id="ssdiv-<?php the_ID(); ?> " 
style="float: left; position: relative; width: 640px; height: 480px; padding: 0; background: url('/screenshot/<?php echo $imgs['gs'];?>');">
	<a <?php post_class(); ?> href="<?php echo get_content_link( get_the_content());?>"
	 style="position: absolute; width: 640px; height: 480px; padding: 0; margin: 0; text-decoration: none;">
		<img style="padding: 0; margin: 0;" src="/screenshot/<?php echo $imgs['ss']?>" alt="<?php echo get_content_link( get_the_content());?>" />
    </a>
    <!-- <div id="contdiv-<?php the_ID();?> class="contentdiv"
     style="position: absolute; margin: 0; padding: 0; display: none; width: 640px; height: 480px;">
     <h1 class="entry-title"><?php the_title() ?></h1>
    	<p><?php the_content(); ?></p>
    </div> -->
</div>




<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'the1000th-link-blog' ),
				'after'  => '</div>',
			) );
?>


