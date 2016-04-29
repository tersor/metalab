<?php

	require_once('admin.author.class.php');
	$modules = $this->CTRL->localizejs->get_modules( );

	/**
	 * Old localizejs enabled setting conversion
	 *
	 * @since 2.3.5
	 */
	if (!isset($options['localizejs_enabled']) && isset($options['localizejs']) && intval($options['localizejs']['enabled']) === 1) {
		$options['localizejs_enabled'] = 1;
	}

?>
<form method="post" action="<?php echo admin_url('admin-post.php?action=abovethefold_localizejs'); ?>" class="clearfix">
	<?php wp_nonce_field('abovethefold'); ?>
	<div class="wrap abovethefold-wrapper">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<div class="postbox">
						<h3 class="hndle">
							<span><?php _e( 'Localize Javascript (BETA)', 'abovethefold' ); ?></span>
						</h3>
						<div class="inside">

							<p><strong>Status: <?php if (intval($options['localizejs_enabled']) === 1) { print '<font color="green">enabled</font>'; } else { print '<font color="red">disabled</font>'; } ?></strong> (enable/disable via <a href="<?php echo admin_url('admin.php?page=abovethefold&tab=settings#localizejs'); ?>">settings</a>)</p>
							<p>This feature stores external javascript files locally to pass the <code>Leverage browser caching</code>-rule from Google PageSpeed Insights (<a href="https://developers.google.com/speed/docs/insights/LeverageBrowserCaching" target="_blank">documentation</a>).</p>

							<p>This is a BETA feature and is not enabled by default. Please <a href="https://wordpress.org/support/plugin/above-the-fold-optimization" target="_blank">report bugs</a>.</p>

							<h3>Custom modules</h3>
							<p>Custom modules can be added in the theme-directory, /THEME<strong>/abovethefold/localizejs/modulename.inc.php</strong>. Take a look at the default modules in /wp-content/plugins/above-the-fold-optimization/modules/localizejs/ for examples. Please submit new modules to <a href="mailto:info@optimalisatie.nl?subject=Submission: Above The Fold Javascript Localization Module">info@optimalisatie.nl</a>.</p>

							<h1>Enable/disable modules</h1>

							<?php
                            	foreach ($modules as $module_file) {

                            		$module = $this->CTRL->localizejs->load_module($module_file);
                            		$module->admin_config();

                            	}
                            ?>

							<br />
                            <hr />
							<?php
								submit_button( __( 'Save' ), 'primary large', 'is_submit', false );
							?>

						</div>
					</div>


	<!-- End of #post_form -->

				</div>
			</div> <!-- End of #post-body -->
		</div> <!-- End of #poststuff -->
	</div> <!-- End of .wrap .nginx-wrapper -->
</form>
