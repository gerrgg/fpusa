<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package FallProtectionUSA
 */
$hide_footer = get_post_meta( get_the_ID(), 'hide_footer', true );
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer <?php if( $hide_footer === 'yes' ) echo 'd-none'; ?>">
		<div class="back-to-top-wrapper text-center">
			<a class="back-to-top text-mute" href="javascript:void(0)">Back to top</a>
		</div>
		<div class="site-info">

		</div><!-- .site-info -->
		<div class="copyright-wrapper d-flex justify-content-center">
			<a class="pr-2" href="">Conditions of Use</a>
			<a class="pr-2" href="">Privacy Policy</a>
			<p>© 2008-2019, <?php echo bloginfo('sitename') ?>.com</p>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<div class="modal fade" id="fpusa_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<div class="spinner-border text-danger" role="status">
				  <span class="sr-only">Loading...</span>
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary modal_ajax_submit">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
