<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' );
?>


<div id="customer_login">
		<?php
		$form = ( isset( $_GET['form'] ) ) ? $_GET['form'] : 'login';
		if( $form == 'login' ) :
		?>
		<h2><?php esc_html_e( 'Sign in', 'woocommerce' ); ?></h2>

		<form class="form" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<div class="form-group">
				<label class="font-weight-bold" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?></label>
				<input type="text" class="form-control" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</div>

			<div class="form-group font-weight-bold">
				<label class="d-flex" for="password">
					<?php esc_html_e( 'Password', 'woocommerce' ); ?>
					<span class="woocommerce-LostPassword lost_password ml-auto">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
					</span>
				</label>
				<input class="form-control" type="password" name="password" id="password" autocomplete="current-password" />
			</div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<div class="form-group">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="btn btn-block btn-warning" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Keep me signed in.', 'woocommerce' ); ?></span>
				</label>
			</div>


			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

		<hr>
		<a href="?<?php echo $_GET['redirect_to'] ?>&form=register"class="btn btn-block btn-primary text-white">Create your <?php echo bloginfo('sitename') ?> account</a>

	<?php elseif( $form == 'register') : ?>

		<h2><?php esc_html_e( 'Create your account', 'woocommerce' ); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<div class="form-group">
				<label class="font-weight-bold" for="username"><?php esc_html_e( 'Your Name', 'woocommerce' ); ?></label>
				<input type="text" class="form-control" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</div>

			<div class="form-group">
				<label class="font-weight-bold" for="email"><?php esc_html_e( 'Email', 'woocommerce' ); ?></label>
				<input type="text" class="form-control" name="email" id="email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</div>

			<div class="form-group">
				<label class="font-weight-bold" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
				<input type="password" class="form-control" name="password" id="password" autocomplete="off" /><?php // @codingStandardsIgnoreLine ?>
			</div>

			<div class="form-group">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="btn btn-primary text-white btn-block" name="register" value="register">Create your <?php echo bloginfo('sitename') ?> account </button>
			</div>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

		<hr>

		<p>Already have an account? <a href="?<?php echo $_GET['redirect_to'] ?>&form=login">Sign in <i class="fas fa-angle-right"></i></a></p>

	</div>

</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
