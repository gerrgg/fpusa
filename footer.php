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

<nav id="menu">
	<div id="mobile-menu-top-wrapper" class="d-flex align-items-end p-3">
		<i class="fas fa-user-circle pr-3"></i>
		<span>
			Hello, Greg
		</span>
	</div>
	<div class="">
		<h5 class="p-3 text-muted">Shop by Category</h5>
		<?php
			wp_nav_menu( array(
				'theme_location'    => 'category_menu',
				'container_id'			=> 'mobile-nav-container',
				'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
				'walker'            => new WP_Bootstrap_Navwalker(),
			) );
			?>
			<div class="dropdown-divider"></div>
			<h5 class="p-3 text-muted">HELP & SETTINGS</h5>
			<ul class="navbar-nav m-0 pl-3">
				<li class="nav-item">
					<a class="nav-link" href="#"><i class="fas fa-user-circle pr-3"></i>Your Account</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#"><i class="fas fa-question pr-3"></i>Help</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#"><i class="fas fa-sign-out-alt pr-3"></i>Sign Out</a>
				</li>
			</ul>
	</div>
</nav>

</body>
</html>
