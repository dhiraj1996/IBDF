<?php if ( is_active_sidebar( 'qa-ads-mobile-footer' ) ) : ?>
	<div class="widget-ads">
		<?php dynamic_sidebar( 'qa-ads-mobile-footer' ); ?>
	</div>
<?php endif; ?>
	<div class="copyright">
		<span class="copyright-text">&copy;<?php echo date('Y') ?> <?php echo ae_get_option( 'copyright' ); ?></span>
		<a href="<?php echo et_get_page_link("term"); ?>" class="copyright-tos"><?php _e("Terms & Privacy", ET_DOMAIN) ?></a>
	</div>

	<?php

		if(is_singular( 'question' ) || is_singular( 'answer' )){
			qa_mobile_answer_template();
			qa_mobile_comment_template();
			qa_mobile_report_template();
		}

		if(is_user_logged_in() && is_author()){
			qa_mobile_member_contact_template();
		}


		qa_tag_template();

		echo '<!-- GOOGLE ANALYTICS CODE -->';
        $google = ae_get_option('google_analytics');
        $google = implode("",explode("\\",$google));
        echo stripslashes(trim($google));
		echo '<!-- END GOOGLE ANALYTICS CODE -->';

		echo '<div class="clearfix"></div>';

		qa_social_links();

		wp_footer();
	?>
	</body>
</html>