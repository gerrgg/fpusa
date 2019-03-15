<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package FallProtectionUSA
 */
$hide_header = get_post_meta( get_the_ID(), 'hide_header', true );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'fpusa' ); ?></a>
	<header id="header-wrapper" class="<?php if( $hide_header ===  'yes' ) echo 'd-none' ?>">
		<!-- <div class=""> -->
			<nav id="header-top" class="d-flex align-items-end">
				<div class="nav-left d-flex align-items-end">
					<button id="cat-btn" class="btn cat-btn mr-2"><i class="fas fa-bars fa-2x text-white"></i></button>
					<?php the_custom_logo(); ?>
				</div>
				<div class="flex-grow-1 mr-5">
					<form id="search-form" role="search" method="get" class="d-flex" action="<?php echo home_url( '/' ); ?>">
				    <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span>
				    <input id="search-bar" type="search" class="form-control" placeholder="Search <?php echo bloginfo('sitename') ?>.com" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
				    <button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
				    <input type="hidden" value="product" name="post_type" id="post_type" />
					</form>
				</div>

				<?php if ( is_active_sidebar( 'header_top_right' ) ) : ?>
					<div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
						<?php dynamic_sidebar( 'header_top_right' ); ?>
					</div><!-- #primary-sidebar -->
				<?php endif; ?>

			</nav>

			<nav id="header-bottom" class="navbar navbar-expand-lg navbar-light">
				<div class="nav-left">
					<?php fpusa_choose_location_btn(); ?>
				</div>

				<?php
					wp_nav_menu( array(
						'menu_id'						=> 'header-mid',
						'menu_class'				=> 'navbar-nav m-0 nav-center',
						'theme_location'    => 'header_bottom_mid',
						'depth'							=> 2,
						'container_id'			=> 'mobile-nav-container',
						'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
						'walker'            => new WP_Bootstrap_Navwalker(),
					) );
				?>

				<div class="ml-auto">
					<?php
					wp_nav_menu( array(
						'menu_id'						=> 'header-bottom-right',
						'menu_class'				=> 'navbar-nav d-flex align-items-end',
						'theme_location'    => 'header_bottom_right',
						'depth'							=> 2,
						'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
						'walker'            => new WP_Bootstrap_Navwalker(),
					) );
					?>
				</div>
			</nav>
		<!-- </div> -->
	</header>

	<div id="content" class="site-content">
		<div id="hm-menu">
			<div id="mySidenav" class="sidenav d-flex">
				<div id="sidenav-inner" class="w-25">
					<div id="mobile-menu-top-wrapper" class="d-flex align-items-end p-3">
						<i class="fas fa-user-circle pr-3"></i>
						<span>
							Hello, Greg
						</span>
						<a class="ml-auto closebtn" href="javascript:void(0)"><i class="fas fa-times"></i></a>
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
							<ul class="navbar-nav m-0 p-0">
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
				</div>
			</div>
		</div>
		<!-- Location Modal -->
		<?php fpusa_edit_location_modal();
