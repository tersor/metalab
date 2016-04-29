<?php

/**
 * Abovethefold optimization functions and hooks.
 *
 * This class provides the functionality for optimization functions and hooks.
 *
 * @since      1.0
 * @package    abovethefold
 * @subpackage abovethefold/includes
 * @author     Optimalisatie.nl <info@optimalisatie.nl>
 */


class Abovethefold_Optimization {

	/**
	 * Above the fold controller
	 *
	 * @since    1.0
	 * @access   public
	 * @var      object    $CTRL
	 */
	public $CTRL;

	/**
	 * Buffer type
	 *
	 * @since    1.0
	 * @access   public
	 * @var      string   $buffertype W3 Total Cache buffer or regular buffer
	 */
	public $buffertype;

	/**
	 * CSS buffer started
	 */
	public $css_buffer_started = false;

	/**
	 * Critical CSS replacement string
	 */
	public $criticalcss_replacement_string = '++|CRITICALCSS|++';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @var      object    $Optimization       The Optimization class.
	 */
	public function __construct( &$CTRL ) {

		$this->CTRL =& $CTRL;

	}

	/**
	 * Init output buffering
	 *
	 * @since    2.0
	 */
	public function start_buffering( ) {

		if ($this->CTRL->extractcss) {

			if ($this->css_buffer_started) {
				return;
			}

			$this->css_buffer_started = true;

			ob_start(array($this, 'end_cssextract_buffering'));

			/**
			 * Curl
			 */
			if (function_exists('curl_version')) {
				require_once(plugin_dir_path( realpath(dirname( __FILE__ ) . '/') ) . 'includes/curl.class.php');
				$this->curl = new Abovethefold_Curl( $this );
			} else if (!ini_get('allow_url_fopen')) {
				$this->CTRL->set_notice('PHP lib Curl should be installed or <em>allow_url_fopen</em> should be enabled.','error');
				$this->curl = false;
				return;
			} else {
				$this->curl = 'file_get_contents';
			}
		} else {
			ob_start(array($this, 'end_buffering'));
		}
	}

	/**
	 * Rewrite callback
	 *
	 * @since    1.0
	 * @var      string    $buffer       HTML output
	 */
	public function end_buffering($buffer) {
		if (is_feed() || is_admin()) {
			return $buffer;
		}

		if ($this->CTRL->noop) { return $buffer; }

		$optimize_delivery = (isset($this->CTRL->options['cssdelivery']) && intval($this->CTRL->options['cssdelivery']) === 1) ? 1 : 0;
		if ($optimize_delivery === 0) {
			return $buffer;
		}

		/**
		 * Ignore List
		 */
		$rows = preg_split('#[\n|\s|,]#Ui',$this->CTRL->options['cssdelivery_ignore']);
		$ignorelist = array();
		foreach ($rows as $row) {
			if (trim($row) === '') {
				continue 1;
			}
			$ignorelist[] = trim($row);
		}

		/**
		 * Delete List
		 */
		$rows = preg_split('#[\n|\s|,]#Ui',$this->CTRL->options['cssdelivery_remove']);
		$deletelist = array();
		foreach ($rows as $row) {
			if (trim($row) === '') {
				continue 1;
			}
			$deletelist[] = trim($row);
		}

		$search = array();
		$replace = array();

		$search[] = '|(jQuery\(function\(\) \{\s+mdf_init_search_form[^\}]+\}\)\;)|is';
		$replace[] = 'head.ready(function() { $1 });';

		/**
		 * Parse CSS links
		 */
		$i = array();
		$_styles = array();
		if (preg_match_all('#(<\!--\[if[^>]+>)?([\s|\n]+)?<link([^>]+)href=[\'|"]([^\'|"]+)[\'|"]([^>]+)?>#is',$buffer,$out)) {
			foreach ($out[4] as $n => $file) {
				if (trim($out[1][$n]) != '' || strpos($out[3][$n] . $out[5][$n],'stylesheet') === false) {
					$i[] = array($out[3][$n] . $out[5][$n],$file);
					continue;
				}
				if (!empty($ignorelist)) {
					$ignore = false;
					foreach ($ignorelist as $_file) {
						if (strpos($file,$_file) !== false) {
							$ignore = true;
							break 1;
						}
					}
					if ($ignore) {
						continue;
					}
				}

				if (!empty($deletelist)) {
					$delete = false;
					foreach ($deletelist as $_file) {
						if (strpos($file,$_file) !== false) {
							$delete = true;
							break 1;
						}
					}
					if ($delete) {
						$search[] = '|<link[^>]+'.preg_quote($file).'[^>]+>|Ui';
						$replace[] = '';
						continue;
					}
				}

				$media = false;
				if (strpos($out[0][$n],'media=') !== false) {
                    $el = (array)simplexml_load_string($out[0][$n]);
					$media = trim($el['@attributes']['media']);
				}
				if (!$media) {
					$media = 'all';
				}
				$media = explode(',',$media);

				$_styles[] = array($media,$file);

				$search[] = '|<link[^>]+'.preg_quote($file).'[^>]+>|Ui';
				$replace[] = '';
			}
		}

		/**
		 * Remove duplicate CSS files
		 */
		$reflog = array();
		$styles = array();
		foreach ($_styles as $link) {
			$hash = md5($link[1]);
			if (isset($reflog[$hash])) {
				continue 1;
			}
			$reflog[$hash] = 1;
			$styles[] = $link;
		}

		$search[] = '#[\'|"]'.preg_quote($this->criticalcss_replacement_string).'[\'|"]#Ui';
		// PHP 5.4+
		if (defined('JSON_UNESCAPED_SLASHES')) {
			$replace[] = json_encode($styles, JSON_UNESCAPED_SLASHES);
		} else {
			$replace[] = str_replace('\\/','/',json_encode($styles));
		}

		$buffer = preg_replace($search,$replace,$buffer);

		return $buffer;
	}

	/**
	 * End CSS extract output buffer
	 *
	 * @since    1.0
	 */
	public function end_cssextract_buffering($HTML) {
		if (is_feed() || is_admin()) {
			return $buffer;
		}
		if ( stripos($HTML,"<html") === false || stripos($HTML,"<xsl:stylesheet") !== false ) {
			// Not valid HTML
			return $HTML;
		}

        $files = false;
		if (isset($_REQUEST['files'])) {
        	$files = explode(',',$_REQUEST['files']);
        	if (!is_array($files) || empty($files)) {
        		$files = false;
        	}
        }

		$siteurl = get_option('siteurl');

		/**
		 * Load HTML into DOMDocument
		 */
		$DOM = new DOMDocument();
		$DOM->preserveWhiteSpace = false;
		@$DOM->loadHTML(mb_convert_encoding($HTML, 'HTML-ENTITIES', 'UTF-8'));

		/**
		 * Query stylesheets
		 */
		$xpath = new DOMXpath($DOM);
		$stylesheets = $xpath->query('//link[not(self::script or self::noscript)]');

		$csscode = array();

		$cssfiles = array();
		$reflog = array();

		$remove = array();
		foreach ($stylesheets as $sheet) {

			$rel = $sheet->getAttribute('rel');
			if (strtolower(trim($rel)) !== 'stylesheet') {
				continue 1;
			}
			$src = $sheet->getAttribute('href');
			$media = $sheet->getAttribute('media');

			if($media) {
				$medias = explode(',',$media);
				$media = array();
				foreach($medias as $elem) {
					if (trim($elem) === '') { continue 1; }
					$media[] = $elem;
				}
			} else {
				// No media specified - applies to all
				$media = array('all');
			}

			/**
			 * Sheet file/url
			 */
			if($src) {

				$url = $src;

				// Strip query string
				$src = current(explode('?',$src,2));

				// URL decode
				if (strpos($src,'%')!==false) {
					$src = urldecode($src);
				}

				// Normalize URL
				if (strpos($url,'//')===0) {
					if (is_ssl()) {
						$url = "https:".$url;
					} else {
						$url = "http:".$url;
					}
				} else if ((strpos($url,'//')===false) && (strpos($url,parse_url($siteurl,PHP_URL_HOST))===false)) {
					$url = $siteurl.$url;
				}

				$hash = md5($url);
				if (isset($reflog[$hash])) {
					continue 1;
				}
				$reflog[$hash] = 1;

				/**
				 * External URL
				 *
				 */
				if (@parse_url($url,PHP_URL_HOST)!==parse_url($siteurl,PHP_URL_HOST)) {

					if ($this->curl === 'file_get_contents') {
						$css = file_get_contents($url);
					} else {
						$css = $this->curl->get($url);
					}
					if (trim($css) === '') {
						continue 1;
					}

					if ($files && !in_array(md5($url),$files)) {
						continue 1;
					}

					$csscode[] = array($media,$css);

				} else {
					$path = (substr(ABSPATH,-1) === '/') ? substr(ABSPATH,0,-1) : ABSPATH;
					$path .= preg_replace('|^(http(s)?:)?//[^/]+/|','/',$src);

					$css = file_get_contents($path);

					if ($files && !in_array(md5($url),$files)) {
						continue 1;
					}

					$csscode[] = array($media,$css);
				}

				if (isset($_REQUEST['output']) && strtolower($_REQUEST['output']) === 'print') {

					$cssfiles[] = array(
						'src' => $url,
						'code' => $css,
						'media' => $media
					);
				}
			}

			// Remove script from DOM
			$remove[] = $sheet;
		}

		/**
		 * Query inline styles
		 */
		$inlinestyles = $xpath->query('//style[not(self::script or self::noscript)]');
		foreach ($inlinestyles as $style) {

			$media = $style->getAttribute('media');

			if($media) {
				$medias = explode(',',strtolower($media));
				$media = array();
				foreach($medias as $elem) {
					if (trim($elem) === '') { continue 1; }
					$media[] = $elem;
				}
			} else {
				// No media specified - applies to all
				$media = array('all');
			}

			$code = $style->nodeValue;

			$hash = md5($code);
			if (isset($reflog[$hash])) {
				continue 1;
			}
			$reflog[$hash] = 1;

			if (strpos($code,'! Above The Fold Optimization') !== false) {
				continue 1;
			}

			$code = preg_replace('#.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*#sm','$1',$code);

			$xdoc = new DOMDocument();
			$xdoc->appendChild($xdoc->importNode($style, true));
			$inlinecode = $xdoc->saveHTML();

			if ($files && !in_array(md5($inlinecode),$files)) {
				continue 1;
			}

			$csscode[] = array($media,$code);

            if (isset($_REQUEST['output']) && strtolower($_REQUEST['output']) === 'print') {

				$cssfiles[] = array(
					'src' => md5($code),
					'inline' => true,
					'code' => $inlinecode,
					'inlinecode' => $code,
					'media' => $media
				);
			}

			// Remove script from DOM
			$remove[] = $style;
		}

		/**
		 * Print CSS for extraction by Critical CSS generator
		 */
		$inlineCSS = '';
		foreach ($csscode as $code) {
			if (in_array('all',$code[0]) || in_array('screen',$code[0])) {
				$inlineCSS .= $code[1];
			}
		}

		foreach($remove as $style) {
			$style->parentNode->removeChild($style);
		}

		$output = 'EXTRACT-CSS-' . md5(SECURE_AUTH_KEY . AUTH_KEY);
		$output .= "\n" . json_encode(array(
			'css' => $inlineCSS,
			'html' => $HTML
		));

		$url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$parsed = array();
		parse_str(substr($url, strpos($url, '?') + 1), $parsed);
		$extractkey = $parsed['extract-css'];
		unset($parsed['extract-css']);
		unset($parsed['output']);
		$url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'].'/';
		if(!empty($parsed))
		{
			$url .= '?' . http_build_query($parsed);
		}

		if (isset($_REQUEST['output']) && (
			strtolower($_REQUEST['output']) === 'css'
			|| strtolower($_REQUEST['output']) === 'download'
		)) {

			if (strtolower($_REQUEST['output']) === 'download') {
				header('Content-type: text/css');
				header('Content-disposition: attachment; filename="full-css-'.$extractkey.'.css"');
			}

			return $inlineCSS;

		} else if (isset($_REQUEST['output']) && strtolower($_REQUEST['output']) === 'print') {

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

			require_once(plugin_dir_path( realpath(dirname( __FILE__ ) . '/') ) . 'includes/extract-full-css.inc.php');

			return $cssoutput;
		}

		return $output;
	}

	/**
	 * WordPress Header hook
	 *
	 * Parse and modify WordPress header. This part includes inline Javascript and CSS and controls the renderproces.,
	 *
	 * @since    1.0
	 */
    public function header() {
		if ($this->CTRL->noop) { return; }

		if ($this->buffertype === 'ob') {
			ob_start(array($this,'rewrite_callback'));
		}

		$cssfile = $this->CTRL->cache_path() . 'inline.min.css';

		$jsfiles = array();

		$jsfiles[] = plugin_dir_path( dirname( __FILE__ ) ) . 'public/js/abovethefold.min.js';

		if (intval($this->CTRL->options['loadcss_enhanced']) === 1) {
			$jsfiles[] = plugin_dir_path( dirname( __FILE__ ) ) . 'public/js/abovethefold-loadcss-enhanced.min.js';
		} else {
			$jsfiles[] = plugin_dir_path( dirname( __FILE__ ) ) . 'public/js/abovethefold-loadcss.min.js';
		}

		$jscode = '';
		foreach ($jsfiles as $file) {
			$jscode .= ' ' . file_get_contents($file);
		}

		$jssettings = array(
			'css' => $this->criticalcss_replacement_string
		);

		if (isset($this->CTRL->options['cssdelivery_renderdelay']) && intval($this->CTRL->options['cssdelivery_renderdelay']) > 0) {
			$jssettings['delay'] = intval($this->CTRL->options['cssdelivery_renderdelay']);
		}

?>
<style type="text/css">
/*! Above The Fold Optimization <?php print $this->CTRL->get_version(); ?> */
<?php if (file_exists($cssfile)) { print file_get_contents($cssfile); } ?></style>
<script type="text/javascript" id="atfcss"><?php print $jscode; ?>
<?php if (current_user_can( 'manage_options' ) && intval($this->CTRL->options['debug']) === 1) { print 'window.abovethefold.debug = true;'; }
if (!empty($jssettings)) {
	print "window['abovethefold'].config(".json_encode($jssettings).");";
}
if ($this->CTRL->options['cssdelivery_position'] === 'header') {
	print "window['abovethefold'].css();";
}
?>
</script>
<?php //
	}

	/**
	 * WordPress Footer hook
	 *
	 * Parse and modify WordPress footer.
	 *
	 * @since    1.0
	 */
	public function footer() {
		if ($this->OPTIMIZE->noop) { return; }

		if (empty($this->CTRL->options['cssdelivery_position']) || $this->CTRL->options['cssdelivery_position'] === 'footer') {
        	print "<script type=\"text/javascript\">if (window['abovethefold']) { window['abovethefold'].css(); }</script>";
        }

	}

	/**
	 * Skip autoptimize CSS
	 */
	public function autoptimize_skip_css($excludeCSS) {
		$excludeCSS .= ',! Above The Fold Optimization,';
		return $excludeCSS;
	}

	/**
	 * Skip autoptimize Javascript
	 */
	public function autoptimize_skip_js($excludeJS) {
		$excludeJS .= ',abovethefold\'].css(),' . $this->criticalcss_replacement_string;

		if ($this->CTRL->options['gwfo']) {
			$excludeJS .= ',WebFontConfig';
		}

		return $excludeJS;
	}

	/**
	 * Extract Google fonts from CSS
	 */
	public function extract_google_fonts($css) {

		$googlefonts = array();

		if (preg_match_all('#(?:@import)(?:\\s)(?:url)?(?:(?:(?:\\()(["\'])?(?:[^"\')]+)\\1(?:\\))|(["\'])(?:.+)\\2)(?:[A-Z\\s])*)+(?:;)#Ui',$css,$out) && !empty($out[0])) {

			foreach ($out[0] as $n => $fontLink) {
				if (substr_count($fontLink, "fonts.googleapis.com/css") > 0) {
					$fontLink = preg_replace('|^.*(//fonts\.[^\s\'\"\)]+)[\s\|\'\|\"\|\)].*|is','$1',$fontLink);
					$googlefonts[] = $fontLink;
				}
			}

			if (!empty($googlefonts)) {

				$fonts = '';
				foreach ($googlefonts as $font) {
					$fonts .= '<link rel="stylesheet" type="text/css" href="'.htmlentities($font).'" />';
				}

				$html = '<html><head><title>Fonts 2</title>'.$fonts.'</head><body></body></html>';
				$fonts = GWFO::googlefonts_find_google_fonts($html);

				return $fonts;

			}
		}

		return false;
	}

	/**
	 * Autoptimize: process CSS (@imports of Google fonts etc.)
	 */
	 public function autoptimize_process_css($css) {

	 	if ($this->CTRL->options['gwfo']) {

			/**
			 * Parse fonts with Google Webfont Optimizer function
			 */
			$fonts = $this->extract_google_fonts($css);

			$GWFOQUE = get_option('abovethefold_gwfo_que');

			if (empty($GWFOQUE)) {
				$GWFOQUE = array();
			}

			if (!empty($fonts)) {
				$GWFOQUE[] = $fonts;
				$css = preg_replace('#(?:@import)(?:\\s)(?:url)?(?:(?:(?:\\()(["\'])?(?:[^"\')]+)\\1(?:\\))|(["\'])(?:.+)\\2)(?:[A-Z\\s])*)+(?:;)#Ui','',$css);
			}

			$GWFOQUE = array_unique($GWFOQUE);
			update_option('abovethefold_gwfo_que',$GWFOQUE);
		}

		return $css;

	 }


	/**
	 * Autoptimize: process Javascript
	 */
	 public function autoptimize_process_js($js) {

		/**
		 * Localize Javascript
		 */
		if ($this->CTRL->options['localizejs_enabled']) {
			$js = $this->CTRL->localizejs->parse_js($js);
		}

		return $js;

	 }

	/**
	 * Autoptimize: process HTML
	 */
	public function autoptimize_process_html($html) {

		/**
		 * Include @import of Google Fonts in optimized delivery via the plugin Google Webfont Optimizer
		 */
		if ($this->CTRL->options['gwfo']) {

			$GWFOQUE = get_option('abovethefold_gwfo_que');

			if (!empty($GWFOQUE)) {

				if (preg_match('|WebFontConfig = \{[^<]+families: (\[[^\]]+\])|is',$html,$out)) {

					$json = $out[1];
					$newJSON = '';

					$jsonLength = strlen($out[1]);
					for ($i = 0; $i < $jsonLength; $i++) {
						if ($json[$i] == '"' || $json[$i] == "'") {
							$nextQuote = strpos($json, $json[$i], $i + 1);
							$quoteContent = substr($json, $i + 1, $nextQuote - $i - 1);
							$newJSON .= '"' . str_replace('"', "'", $quoteContent) . '"';
							$i = $nextQuote;
						} else {
							$newJSON .= $json[$i];
						}
					}

					$json = json_decode($newJSON,true);

					$newJSON = array_unique($json);

					if (!is_array($json)) {
						$json = array();
					}
				}

				foreach ($GWFOQUE as $qn => $data) {
					foreach ($data['google'] as $font) {
						$json[] = $font;
					}
				}

				$html = preg_replace('|(WebFontConfig = \{[^<]+families: )(\[[^\]]+\])|is','$1' . json_encode($json),$html);
			}
		}

		/**
		 * Old localizejs enabled setting conversion
		 *
		 * @since 2.3.5
		 */
		if (!isset($this->CTRL->options['localizejs_enabled']) && isset($this->CTRL->options['localizejs']) && intval($this->CTRL->options['localizejs']['enabled']) === 1) {
			$this->CTRL->options['localizejs_enabled'] = 1;
		}

		/**
		 * Localize Javascript
		 */
		if ($this->CTRL->options['localizejs_enabled']) {
			$html = $this->CTRL->localizejs->parse_html($html);
		}

		return $html;
	}

}
