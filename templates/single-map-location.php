<?php
if (isset($_GET['cm-ajax'])) {
	the_post();
	$video_embed_code = get_post_meta(get_the_ID(), '_map_location_video_embed', true);
	$video_thumb = cm_get_video_thumb($video_embed_code);
	
	// 
	// $images = array_slice($images, 0, 6);
	$images = get_post_meta(get_the_ID(), '_map_location_gallery_image', false);
	if (!$images) {
		$fallback = get_posts(array('post_type'=>'attachment', 'post_parent'=>get_the_ID(), 'posts_per_page'=>-1));
		$images = array();
		foreach ($fallback as $p) {
			$img = wp_get_attachment_image_src($p->ID, 'full');
			$images[] = $img[0];
		}
	}
	
	$trees_planted = get_post_meta(get_the_ID(), '_post_trees_planted', true);
	$location = get_post_meta(get_the_ID(), '_post_location', true);
	$trees_type = get_post_meta(get_the_ID(), '_post_trees_type', true);
	$seasonal_information = get_post_meta(get_the_ID(), '_post_seasonal_info', true);
	$post_description = get_post_meta(get_the_ID(), '_post_description', true);
	$post_website = get_post_meta(get_the_ID(), '_post_website', true);
	?>
		<div class="cm-pin-popup">
		<div class="side-a">
			<div class="gallery">
				<div class="holder">
					<?php if ($video_embed_code) : ?>
						<div class="item" style="display: none;">
							<?php echo cm_filter_video($video_embed_code, false, 472, 316); ?>
						</div>
					<?php endif; ?>
					<?php
					if (!empty($images)) {
						foreach ($images as $image) {
							echo '<div class="item" style="display: none;">';
							echo '<img src="' . cm_get_thumb($image, 472, 316) . '" alt="" />';
							echo '</div>';
						}
					}
					?>
				</div>
				<div class="popup-slider">
					<ul class="list">
						<?php if ($video_embed_code) : ?>
							<li>
								<a href="#" class="plant_thumbnail video_thumbnail"><img src="<?php echo cm_get_thumb($video_thumb, 64, 64); ?>" alt="" /></a>
								<div class="description"><?php echo get_post_meta(get_the_ID(), '_map_location_video_description', true); ?></div>
							</li>
						<?php endif; ?>
						<?php
						if (!empty($images)) {
							foreach ($images as $i => $image) {
								// echo '<li ' . (($i == 4) ? 'class="last"' : '') . '>';
								echo '<li>';
								echo '<a href="#" class="plant_thumbnail">';
								echo '<img src="' . cm_get_thumb($image, 64, 64) . '" alt="" />';
								echo '</a>';
								// echo '<div class="description">' . apply_filters('the_content', $image->post_content) . '</div>';
								echo '</li>';
							}
						}
						?>
					</ul>
				</div>
				<div class="cl">&nbsp;</div>
			</div>
			<p class="caption"></p>
		</div>
		<div class="side-b">
			<div class="nfo">
				<h3><?php the_title(); ?></h3>
				<?php the_content(); ?>
			</div>
		</div>
		<div class="cl">&nbsp;</div>
		</div>
	<?php
} else {
	wp_redirect(get_option('home'), 301);
	exit;
}
?>