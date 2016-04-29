
<?php require_once('admin.author.class.php'); ?>


<form method="post" action="<?php echo admin_url('admin-post.php?action=abovethefold_generate'); ?>" class="clearfix">
	<?php wp_nonce_field('abovethefold'); ?>
	<div class="wrap abovethefold-wrapper">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<div class="postbox">


					<h3 class="hndle">
							<span><?php _e( 'Free and Paid Online Service', 'abovethefold' ); ?></span>
						</h3>
						<div class="inside">

							<p>The author of Penthouse.js (<a href="https://jonassebastianohlsson.com/" target="_blank">website</a>) has made a free and paid online critical path CSS generator available.

							The <a href="http://jonassebastianohlsson.com/criticalpathcssgenerator/" target="_blank">free version</a> has no configuration options but it results in good quality Critical Path CSS code for most websites.

							The paid version can be found at <strong><big><a href="https://criticalcss.com/#utm_source=wordpress&utm_medium=link&utm_term=optimization&utm_campaign=Above%20the%20fold" target="_blank">https://criticalcss.com/</a></big></strong>.</p>


						</div>

						<h3 class="hndle">
							<span><?php _e( 'Professional via Grunt.js or Gulp.js', 'abovethefold' ); ?></span>
						</h3>
						<div class="inside">
							<p>The best method for creating critical path CSS is via professional Node.js software using an automation tool such as <a href="http://gruntjs.com/" target="_blank">Grunt.js</a> or <a href="http://gulpjs.com/" target="_blank">Gulp.js</a>. The tools can be used on any platform (Windows, Mac and Linux) and are integrated into several IDE's such as <a href="https://www.jetbrains.com/webstorm/" target="_blank">WebStorm IDE</a>.</p>
							<p>Some of the available tools are:</p>
							<ul>
								<li><a href="https://github.com/pocketjoso/penthouse" target="_blank">Penthouse.js</a> - by Jonas Ohlsson generates critical-path CSS (<a href="https://github.com/fatso83/grunt-penthouse" target="_blank">grunt-penthouse</a>)</li>
								<li><a href="https://github.com/addyosmani/critical" target="_blank">Critical</a> - by Addy Osmani generates & inlines critical-path CSS (uses Penthouse, <a href="https://github.com/addyosmani/oust" target="_blank">Oust</a> and inline-styles) (<a href="https://github.com/bezoerb/grunt-critical" target="_blank">grunt-critical</a>)</li>
								<li><a href="https://github.com/filamentgroup/criticalcss" target="_blank">CriticalCSS</a> - by FilamentGroup finds & outputs critical CSS (<a href="https://github.com/filamentgroup/grunt-criticalcss" target="_blank">grunt-criticalcss</a>)</li>
							</ul>
							<p>More info can be found on the following maintained resource: <a href="https://github.com/addyosmani/critical-path-css-tools" target="_blank">https://github.com/addyosmani/critical-path-css-tools</a></p>

							<div><big>Download example <a href="<?php print plugin_dir_url( __FILE__ ); ?>docs/Gruntfile.js" target="_blank">Gruntfile.js</a> | <a href="<?php print plugin_dir_url( __FILE__ ); ?>docs/package.json" target="_blank">package.json</a> - Command: <code>grunt abovethefold</code></big></div>
							<div style="margin-top:5px;"><em>The example uses <a href="https://github.com/pocketjoso/penthouse" target="_blank">Penthouse.js</a> and several other optimization tools. The example code creates critical path CSS code for multiple pages and dimensions, converts inline images to inline data-uri and optimizes the resulting code using custom replacement (regex) and <a href="https://github.com/gruntjs/grunt-contrib-cssmin" target="_blank">cssmin</a> compression. You can easily add other optimization tools to the process to achieve the best possible result for a specific website.</em></div>
						</div>

						<h3 class="hndle">
							<img src="<?php print plugins_url('above-the-fold-optimization/admin/ssh.png'); ?>" style="float:left;" />&nbsp;&nbsp;<span><?php _e( 'Server-side Critical Path CSS generator', 'abovethefold' ); ?></span><a name="server">&nbsp;</a>
						</h3>
						<div class="inside">

							<?php settings_errors(); ?>

							<p>The integrated Critical Path CSS generator is based on <a href="https://github.com/pocketjoso/penthouse" target="_blank">Penthouse.js</a>.<p>

							<strong>How it works</strong>
							<br />The functionality of Penthouse.js is described <a href="https://github.com/pocketjoso/penthouse" target="_blank">here</a>.
							The plugin will execute Penthouse.js to generate Critical Path CSS for multiple responsive dimensions and pages, combines the resulting CSS-code and then compresses the CSS-code via Clean-CSS.
							<br />

							<blockquote style="border:solid 1px #dfdfdf;background:#F8F8F8;padding:10px;padding-bottom:0px;">
								Automated generation from within WordPress requires <a href="https://github.com/ariya/phantomjs" target="_blank">PhantomJS</a> and <a href="https://github.com/jakubpawlowicz/clean-css" target="_blank">Clean-CSS</a> to be executable by PHP. <strong><font color="red">This can be a security risk.</font></strong>
								<p>As an alternative you can select the option <a href="javascript:void(0);" class="button button-small">Generate CLI command</a> which will result in a command-line string that can be executed via SSH.</p>
								<p><strong><font color="red">Be very careful when executing commands via SSH. If you do not know what you are doing, consult a professional or your hosting provider.</font></strong></p>
							</blockquote>

							<table class="form-table">
								<tr valign="top">
									<th scope="row">Responsive CSS Dimensions</th>
									<td>
										<input type="text" name="abovethefold[dimensions]" value="<?php echo esc_attr( ((isset($options['dimensions'])) ? $options['dimensions'] : '1600x1200, 720x1280, 320x480') ); ?>" style="width:100%;" />
										<p class="description"><?php _e('Enter the (responsive) dimensions to generate Critical CSS for, e.g. <code>1600x1200, 720x1280, 320x480</code>', 'abovethefold'); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										URL-paths
										<div style="margin-top:10px;">
											<a href="javascript:void(0);" class="button button-small" onclick="jQuery('#abovethefold_urls').val(jQuery('#defaultpaths').html());">Load random paths</a>
											<div id="defaultpaths" style="display:none;"><?php print implode("\n",$default_paths); ?></div>
										</div>
									</th>
									<td>
										<textarea name="abovethefold[urls]" id="abovethefold_urls" style="width:100%;height:100px;" /><?php echo esc_attr( ((isset($options['urls'])) ? $options['urls'] : '') ); ?></textarea>
										<p class="description"><?php _e('Enter the paths to generate Critical Path CSS for. The resulting CSS-code for each URL is merged and compressed te create Critical Path CSS that is compatible for each page.', 'abovethefold'); ?></p>
										<p class="description">All paths must be located on the siteurl of the blog <code><?php print get_option('siteurl'); ?><strong><font color="blue">/path</font></strong></code> and execute the Above the fold plugin.</p>

									</td>
								</tr>
								<tr valign="top">
									<th scope="row">PhantomJS path</th>
									<td>
										<input type="text" name="abovethefold[phantomjs_path]" value="<?php echo esc_attr( ((isset($options['phantomjs_path'])) ? $options['phantomjs_path'] : '/usr/local/bin/phantomjs') ); ?>" style="width:100%;" />
										<p class="description"><?php _e('Enter the path to <a href="https://github.com/ariya/phantomjs" target="_blank">PhantomJS</a> on the server. Install via the CLI-command <code>npm install -g phantomjs</code>.', 'abovethefold'); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Clean-CSS path</th>
									<td>
										<input type="text" name="abovethefold[cleancss_path]" value="<?php echo esc_attr( ((isset($options['cleancss_path'])) ? $options['cleancss_path'] : '/usr/local/bin/cleancss') ); ?>" style="width:100%;" />
										<p class="description"><?php _e('Enter the path to <a href="https://github.com/jakubpawlowicz/clean-css" target="_blank">Clean-CSS</a> on the server. Install via the CLI-command <code>npm install -g clean-css</code>.', 'abovethefold'); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Remove data-uri</th>
									<td>
                                        <label><input type="checkbox" name="abovethefold[remove_datauri]" value="1"<?php if (!isset($options['remove_datauri']) || intval($options['remove_datauri']) === 1) { print ' checked'; } ?>> Enabled</label>
                                        <p class="description"><?php _e('Strip data-uri from inline-CSS.', 'abovethefold'); ?></p>
									</td>
								</tr>
							</table>
							<hr />
							<?php
								submit_button( __( 'Generate Critical CSS', 'abovethefold' ), 'primary large', 'generate_css', false );
							?>
							&nbsp;
							<?php
								submit_button( __( 'Generate CLI Command', 'abovethefold' ), 'large', 'generate_cli', false );
							?>
						</div>
					</div>

	<!-- End of #post_form -->

				</div>
			</div> <!-- End of #post-body -->
		</div> <!-- End of #poststuff -->
	</div> <!-- End of .wrap .nginx-wrapper -->
</form>
