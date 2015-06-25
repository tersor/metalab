<?php
add_action( 'init', array('Backend', 'init24sevenSession' ) );
add_action( 'admin_menu', array('Backend', 'createSubmenu' ) );
add_action( 'admin_enqueue_scripts', array('Backend', 'registerScripts' ) );


class Backend{

	public static function init24sevenSession(){
		if ( is_admin() ){
			$Auth = new _247_Authenticate();
		}
	}

	public static function registerScripts(){
		if ( is_admin() ) {
			$scripts = array();
    	$styles = array();

			$path = plugin_dir_url(null).'woocommerce-247-office/assets/';

  	  // $scripts = array( 'bootstrap-datepicker.js', 'bootstrap-datepicker.no.js', 'datepicker.js', 'forms.js',  'jquery.validate.min.js', 'messages_no.js' );
			$styles = array( 'admin.css' );

    	// include stylesheets
    	foreach ($styles as $s) {
				echo '<link rel="stylesheet" href="'.$path .'css/'.$s.'" type="text/css" />' . "\n";
			}

			// include scripts
	   	//  	foreach ($scripts as $s) {
			// 	echo '<script src="'.$path.'js/'.$s.'" ></script>' . "\n";
			// }
		}
	}

	public static function createSubmenu() {
		$page = add_submenu_page('woocommerce', __( '24seven office', MB_LANG ), __( '24SevenOffice', MB_LANG ), apply_filters( 'woocommerce_csv_product_role', 'manage_woocommerce' ), _247_ADMIN, array('Backend', 'adminPage') );
	}


	public static function adminPage() {
		global $woocommerce;

		$tab =  ( isset($_GET['tab'] ) && $_GET['tab'] )  ? $_GET['tab'] : 'apiPage';
		?>
		<div class="wrap woocommerce">
			<div class="icon32" id="icon-woocommerce-importer"><br></div>
		    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		        <a href="<?php echo admin_url('admin.php?page='._247_ADMIN.'&tab=apiPage') ?>" class="nav-tab <?php echo ($tab == 'import') ? 'nav-tab-active' : ''; ?>"><?php _e('API', MB_LANG); ?></a>
		        <a href="<?php echo admin_url('admin.php?page='._247_ADMIN.'&tab=syncProductsPage') ?>" class="nav-tab <?php echo ($tab == 'import') ? 'nav-tab-active' : ''; ?>"><?php _e('Import Products', MB_LANG); ?></a>
		        <!-- <a href="<?php echo admin_url('admin.php?page=woocommerce_csv_import_suite&tab=export') ?>" class="nav-tab <?php echo ($tab == 'export') ? 'nav-tab-active' : ''; ?>"><?php _e('Export Products', MB_LANG); ?></a> -->
		    </h2>

			<?php self::$tab(); ?>
		</div>

		<?php
	}


	public static function apiPage(){
	?>
		<?php if ( isset($_POST['update']) ): ?>
			<div id="message" class="updated woocommerce-message wc-connect">
				<div class="squeezer">
					<?php _247_Options::updateApiSettings(); ?>
					<h4><?php _e( '<strong>API settings updated</strong> ', MB_LANG ); ?></h4>
				</div>
			</div>
		<?php endif; ?>

		<div class="tool-box">
			<form action="" method="POST">
				<h3 class="title"><?php _e('API setttings', MB_LANG); ?></h3>
				<input type="hidden" name="update" value="1">

				<?php _247_Options::getApiSettings(); ?>

				<p><input type="submit" class="button" value="<?php _e('update', MB_LANG ); ?>"></p>

			</form>
		</div>
			<?php
	}


	public static function syncProductsPage(){
	?>

		<?php if ( isset($_POST['update']) ): ?>
		<?php
			$Products =  new _247_Products();
			$Products->getProducts();
		?>
			<div id="message" class="updated woocommerce-message wc-connect">
				<div class="squeezer">
					<h4><strong><?php _e( 'Products synchronized', MB_LANG ); ?></strong> </h4>
				</div>
			</div>
		<?php endif; ?>

		<div class="tool-box">
			<form action="" method="POST">
				<h3 class="title"><?php _e('Synchronize products from 24SevenOffice', MB_LANG); ?></h3>
				<input type="hidden" name="update" value="1">

				<p><input type="submit" class="button" value="<?php _e('import', MB_LANG ); ?>"></p>

			</form>
		</div>
			<?php
	}
}