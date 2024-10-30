<?php
if (theme_comments_restrict_access()) {

	theme_comments_render_list('theme_render_comment');

	theme_comments_render_form(array(
		'title_reply'=> '',
		'comment_notes_before'=> '',
		'comment_notes_after'=> '',
		'fields' => array(
			'author' => '<div class="fieldbox"><label for="author">' . __( 'Your Name', 'ttp' ) . '</label> ' .
	            '<input id="author" class="field" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></div>',
			'email'  => '<div class="fieldbox"><label for="email">' . __( 'Your Email', 'ttp' ) . '</label> ' . 
	            '<input id="email" class="field" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></div>',
			'url'    => '<div class="fieldbox last"><label for="url">' . __( 'URL (optional)', 'ttp' ) . '</label>' .
	            '<input id="url" class="field" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div>'
		),
		'comment_field' => '<div class="comment-form-comment"><label for="comment">' . _x( 'Your Comments', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></div>'

	));
}
?>