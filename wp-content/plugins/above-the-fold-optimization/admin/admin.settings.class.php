
<?php require_once('admin.author.class.php'); ?>
<style>
</style>
<form method="post" action="<?php echo admin_url('admin-post.php?action=abovethefold_update'); ?>" class="clearfix" style="margin-top:0px;">
	<?php wp_nonce_field('abovethefold'); ?>
	<div class="wrap abovethefold-wrapper">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<div class="postbox">
						<div class="inside">

							<table class="form-table">
								<tr valign="top">
									<th scope="row">Critical Path CSS
										<?php if (trim($inlinecss) !== '') { print '<div style="font-size:11px;font-weight:normal;">'.size_format(strlen($inlinecss),2).'</div>'; } ?>
									</th>
									<td>
										<p class="description" style="margin-bottom:5px;"><?php _e('Enter the CSS-code to be inserted <strong>inline</strong> into the <code>&lt;head&gt;</code> of the page. <a href="https://developers.google.com/speed/docs/insights/OptimizeCSSDelivery" target="_blank">Click here</a> for the recommendations by Google.', 'abovethefold'); ?></p>
										<textarea style="width: 100%;height:250px;font-size:11px;margin-bottom:5px;" id="abtfcss"<?php if (!isset($options['csseditor']) || intval($options['csseditor']) === 1) { print 'data-advanced="1"'; } ?> name="abovethefold[css]"><?php echo htmlentities($inlinecss); ?></textarea>

										<span style="float:right;<?php if (!isset($options['csseditor']) || intval($options['csseditor']) === 1) { } else { print 'display:none;'; } ?>"><a href="javascript:void(0);" onclick="window.abtfcssToggle(this);">[+] Large Editor</a></span>

										<a href="https://www.google.com/search?q=beautify+css+online" target="_blank" class="button button-secondary button-small">Beautify</a>
										<a href="https://www.google.com/search?q=minify+css+online" target="_blank" class="button button-secondary button-small">Minify</a>
										<a href="https://jigsaw.w3.org/css-validator/#validate_by_input+with_options" target="_blank" class="button button-secondary button-small">Validate</a>

									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Advanced CSS editor</th>
									<td>
										<label><input type="checkbox" name="abovethefold[csseditor]" value="1"<?php if (!isset($options['csseditor']) || intval($options['csseditor']) === 1) { print ' checked'; } ?>> Enabled</label>
										<p class="description">Use a CSS editor with error reporting (<a href="http://csslint.net/" target="_blank">CSS lint</a> using <a href="https://codemirror.net/" target="_blank">CodeMirror</a>).</p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Optimize CSS delivery</th>
									<td>
										<label><input type="checkbox" name="abovethefold[cssdelivery]" value="1"<?php if (!isset($options['cssdelivery']) || intval($options['cssdelivery']) === 1) { print ' checked'; } ?> onchange="if (jQuery(this).is(':checked')) { jQuery('.cssdeliveryoptions').show(); } else { jQuery('.cssdeliveryoptions').hide(); }"> Enabled</label>
										<p class="description">When enabled, CSS files are loaded asynchronously.</p>
									</td>
								</tr>
								<tr valign="top" class="cssdeliveryoptions" style="<?php if (isset($options['cssdelivery']) && intval($options['cssdelivery']) !== 1) { print 'display:none;'; } ?>">
									<td colspan="2">

										<div style="background:#f1f1f1;border:solid 1px #e5e5e5;">

										<h3 class="hndle" style="border-bottom:solid 1px #e5e5e5;"><span>CSS Delivery Optimization</span></h3>

										<div class="inside">
											<table class="form-table">
												<tr valign="top">
													<th scope="row">Enhanced loadCSS</th>
													<td>
														<label><input type="checkbox" name="abovethefold[loadcss_enhanced]" value="1" onchange="if (jQuery(this).is(':checked')) { jQuery('.enhancedloadcssoptions').show(); } else { jQuery('.enhancedloadcssoptions').hide(); }"<?php if (!isset($options['loadcss_enhanced']) || intval($options['loadcss_enhanced']) === 1) { print ' checked'; } ?>> Enabled</label>
														<p class="description">When enabled, a modified version of <a href="https://github.com/filamentgroup/loadCSS" target="_blank">loadCSS</a> is used to make use of the <code>requestAnimationFrame</code> API following the <a href="https://developers.google.com/speed/docs/insights/OptimizeCSSDelivery" target="_blank">recommendations by Google</a>.</p>
													</td>
												</tr>
												<tr valign="top" class="enhancedloadcssoptions" style="<?php if (isset($options['loadcss_enhanced']) && intval($options['loadcss_enhanced']) !== 1) { print 'display:none;'; } ?>">
													<th scope="row">CSS render delay</th>
													<td>
														<table cellpadding="0" cellspacing="0" border="0">
															<tr>
																<td valign="top" style="padding:0px;vertical-align:top;"><input type="number" min="0" max="3000" step="1" name="abovethefold[cssdelivery_renderdelay]" size="10" value="<?php print ((empty($options['cssdelivery_renderdelay']) || $options['cssdelivery_renderdelay'] === 0) ? '' : htmlentities($options['cssdelivery_renderdelay'],ENT_COMPAT,'utf-8')); ?>" onkeyup="if (jQuery(this).val() !== '' && jQuery(this).val() !== '0') { jQuery('#warnrenderdelay').show(); } else { jQuery('#warnrenderdelay').hide(); }" onchange="if (jQuery(this).val() === '0') { jQuery(this).val(''); } if (jQuery(this).val() !== '' && jQuery(this).val() !== '0') { jQuery('#warnrenderdelay').show(); } else { jQuery('#warnrenderdelay').hide(); }" placeholder="0 ms" /></td>
																<td valign="top" style="padding:0px;vertical-align:top;padding-left:10px;font-size:11px;"><div id="warnrenderdelay" style="padding:0px;margin:0px;<?php print ((empty($options['cssdelivery_renderdelay']) || $options['cssdelivery_renderdelay'] === 0 || trim($options['cssdelivery_renderdelay']) === '') ? 'display:none;' : ''); ?>"><span style="color:red;font-weight:bold;">Warning:</span> Although a higher PageSpeed score can be achieved using this option, it may not be beneficial to the page rendering experience of your users. Often it is best to seek an alternative solution to pass the rule.</div></td>
															</tr>
														</table>
														<p class="description" style="clear:both;">Optionally, enter a time in microseconds to delay the rendering of CSS files. This option allows for fine tuning to the break point of the <code>Eliminate render-blocking JavaScript and CSS in above-the-fold content</code>-rule.</p>

													</td>
												</tr>
												<tr valign="top">
													<th scope="row">Position</th>
													<td>
														<select name="abovethefold[cssdelivery_position]">
															<option value="header"<?php if ($options['cssdelivery_position'] === 'header') { print ' selected'; } ?>>Header</option>
															<option value="footer"<?php if (empty($options['cssdelivery_position']) || $options['cssdelivery_position'] === 'footer') { print ' selected'; } ?>>Footer</option>
														</select>
														<p class="description">Select the position where the async loading of CSS will start.</p>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row">Ignore List</th>
													<td>
														<textarea style="width: 100%;height:50px;font-size:11px;" name="abovethefold[cssdelivery_ignore]"><?php echo htmlentities($options['cssdelivery_ignore']); ?></textarea>
														<p class="description">CSS-files to ignore in CSS delivery optimization. The files will be left untouched in the HTML.</p>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row">Remove List</th>
													<td>
														<textarea style="width: 100%;height:50px;font-size:11px;" name="abovethefold[cssdelivery_remove]"><?php echo htmlentities($options['cssdelivery_remove']); ?></textarea>
														<p class="description">CSS-files to remove. This feature enables to include small plugin-CSS files inline.</p>
													</td>
												</tr>
											</table>
										</div>

										</div>
									</td>
								</tr>

<?php

	$autoptimize_active = is_plugin_active('autoptimize/autoptimize.php');
	$gwfo_active = is_plugin_active('google-webfont-optimizer/google-webfont-optimizer.php');
?>

								<tr valign="top">
									<th scope="row">Optimize Google Fonts</th>
									<td>
										<label><input type="checkbox" name="abovethefold[gwfo]" value="1"<?php if (!isset($options['gwfo']) || intval($options['gwfo']) === 1) { print ' checked'; } ?> onchange="if (jQuery(this).is(':checked')) { jQuery('.gwfooptions').show(); } else { jQuery('.gwfooptions').hide(); }"> Enabled
											<span class="gwfooptions" style="<?php if (isset($options['gwfo']) && intval($options['gwfo']) !== 1) { print 'display:none;'; } ?>">
											<?php
												if ($autoptimize_active && $gwfo_active) {

													if (!get_option('autoptimize_css')) {
														?>
															<span style="color:red;font-weight:bold;">ERROR - Autoptimize CSS optimization is disabled. <a href="./options-general.php?page=autoptimize">Enable it</a> to use this feature.</span>
														<?php
													} else {
														?>
															<span style="color:green;font-weight:bold;">OK - Autoptimize and Google Webfont Optimizer are installed and active.</span>
														<?php
													}
												} else if (!$autoptimize_active) {
													?>
														<span style="color:red;font-weight:bold;">ERROR - Autoptimize not installed or not activated.</span>
													<?php
												} else if (!$gwfo_active) {
													?>
														<span style="color:red;font-weight:bold;">ERROR - Google Webfont Optimizer not installed or not activated.</span>
													<?php
												}
											?>
											</span>
										</label>
										<p class="description">When enabled, Google fonts found in <code>@import</code> within the CSS-code output of the plugin <a href="https://wordpress.org/plugins/autoptimize/" target="_blank">Autoptimize</a> are included in the optimized delivery by the plugin <a href="https://wordpress.org/plugins/google-webfont-optimizer/">Google Webfont Optimizer</a>. Both plugins need to be installed and active to use this feature.</p>
									</td>
								</tr>
<?php
	/**
	 * Old localizejs enabled setting conversion
	 *
	 * @since 2.3.5
	 */
	if (!isset($options['localizejs_enabled']) && isset($options['localizejs']) && intval($options['localizejs']['enabled']) === 1) {
		$options['localizejs_enabled'] = 1;
	}
?>
								<tr valign="top">
									<th scope="row">
										Localize Javascript (BETA)<a name="localizejs">&nbsp;</a>
									</th>
									<td>
										<label><input type="checkbox" name="abovethefold[localizejs_enabled]" value="1"<?php if (intval($options['localizejs_enabled']) === 1) { print ' checked'; } ?> onchange="if (jQuery(this).is(':checked')) { jQuery('.localizejsoptions').show(); } else { jQuery('.localizejsoptions').hide(); }"> Enabled &nbsp;<a href="<?php echo admin_url('admin.php?page=abovethefold&tab=localizejs'); ?>" class="localizejsoptions button button-secondary button-small">Settings</a></label>
										<p class="description">When enabled, recognized external javascript files are stored locally to pass the <code>Leverage browser caching</code>-rule from Google PageSpeed. </p>

										</div>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">Admin Bar Test Menu</th>
									<td>
                                        <label><input type="checkbox" name="abovethefold[adminbar]" value="1"<?php if (!isset($options['adminbar']) || intval($options['adminbar']) === 1) { print ' checked'; } ?>> Enabled</label>
                                        <p class="description">Show a <code>PageSpeed</code>-menu in the top admin bar with links to website speed and security tests such as Google PageSpeed Insights. The menu is intended for quick testing of individual pages.</p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Debug Modus</th>
									<td>
                                        <label><input type="checkbox" name="abovethefold[debug]" value="1"<?php if (!isset($options['debug']) || intval($options['debug']) === 1) { print ' checked'; } ?>> Enabled</label>
                                        <p class="description">Show debug info in the browser console for logged in admin-users.</p>
									</td>
								</tr>
							</table>
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