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
	<header id="header-wrapper">
		<!-- <div class=""> -->
			<nav id="header-top" class="navbar navbar-light">
					<div class="nav-left">
						<button id="cat-btn" class="btn cat-btn mr-3"><i class="fas fa-bars fa-2x"></i></button>
					  <?php the_custom_logo(); ?>
					</div>

					<form id="search-wrap" method="GET" class="nav-center">
						<input class="form-control" type="search" name="s"/>
						<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
					</form>

					<div id="top-right" class="ml-auto">
						<img src="http://fpusa.drunk.kiwi/wp-content/uploads/2019/02/Amazon_GW_DesktopSWM_AVD10238_PrimeVideo_400x39._CB487601461_.jpg"/>
					</div>
			</nav>

			<nav id="header-bottom" class="navbar navbar-expand-lg navbar-light">
				<div class="nav-left">
					<?php fpusa_choose_location_btn(); ?>
				</div>

				<ul id="header-mid" class="navbar-nav m-0 nav-center">
					<li class="nav-item">
						<a class="nav-link" href="/your-store?fpusa_action=buy_it_again">Buy Again</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Browsing History
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="#">Action</a>
							<a class="dropdown-item" href="#">Another action</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="#">Something else here</a>
						</div>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Greg's FallProtectionUSA.com</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="/promo-codes/">Promo Codes</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Help</a>
					</li>
				</ul>

				<div class="ml-auto">
					<?php fpusa_get_header_right(); ?>
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
