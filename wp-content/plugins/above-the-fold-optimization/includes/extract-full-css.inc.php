<?php

/**
 * Extract Full CSS template
 *
 * @since      2.3.5
 * @package    abovethefold
 * @subpackage abovethefold/admin
 * @author     Optimalisatie.nl <info@optimalisatie.nl>
 */

$cssoutput = '<html>
<head>
<title>Full CSS extraction</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
function humanFileSize(bytes, si) {
    var thresh = si ? 1000 : 1024;
    if(Math.abs(bytes) < thresh) {
        return bytes + \' B\';
    }
    var units = si
        ? [\'kB\',\'MB\',\'GB\',\'TB\',\'PB\',\'EB\',\'ZB\',\'YB\']
        : [\'KiB\',\'MiB\',\'GiB\',\'TiB\',\'PiB\',\'EiB\',\'ZiB\',\'YiB\'];
    var u = -1;
    do {
        bytes /= thresh;
        ++u;
    } while(Math.abs(bytes) >= thresh && u < units.length - 1);
    return bytes.toFixed(1)+\' \'+units[u];
}
function update_fullcss() {
	var css = \'\';
	jQuery.each(jQuery(\'input[name=css]:checked\'),function(i,el) {
		css += \'/**\\n * \' + jQuery(\'#code\'+jQuery(el).val()).attr(\'title\') + \'\\n */\\n \' + jQuery(\'#code\'+jQuery(el).val()).val() + \'\\n\\n\';
	});
	jQuery(\'#fullcss\').val(css);
	jQuery(\'#fullcsssize\').html(humanFileSize(css.length,false));
}
function show_inline(id) {
	var w = window.open();
	w.document.open(\'about:blank\',\'cssdisplay\');
	w.document.write(document.getElementById(id).value);
	w.document.close();
}
function download_css() {
	var cssstr = \'\';
	jQuery.each(jQuery(\'input[name=css]:checked\'),function(i,el) {
		if (cssstr) {
			cssstr += \',\';
		}
		cssstr += jQuery(el).val();
	});
	document.location.href = \''.$url.'?extract-css='.$extractkey.'&output=download&files=\' + cssstr;
}
</script>
</head>
<body>

<h1>Full CSS Extraction</h1>

<div>Url: <a href="'.$url.'" target="_blank">'.$url.'</a></div>
<br />
';

			foreach($cssfiles as $file) {

				if ($file['inline']) {
					$cssoutput .= '<textarea style="display:none;" id="inline'.$file['src'].'_display">'.htmlentities(htmlentities($file['code'])).'</textarea>
					<textarea style="display:none;" id="code'.md5($file['code']).'" title="' . $file['src'] . ' ('.human_filesize(strlen($file['inlinecode']), 2).')">'.htmlentities($file['inlinecode']).'</textarea>
					<label style="display:block;border-bottom:solid 1px #efefef;padding-bottom:5px;margin-bottom:5px;">
						<input type="checkbox" name="css" value="'.md5($file['code']).'" checked="true"> Inline <a href="javascript:void(0);" onclick="show_inline(\'inline'.$file['src'].'_display\');">'.$file['src'].'</a> ('.human_filesize(strlen($file['inlinecode']), 2).') - Media: '.implode(', ',$file['media']).'
					</label>';
				} else {
					$cssoutput .= '<textarea style="display:none;" id="code'.md5($file['src']).'" title="'.$file['src'].' ('.human_filesize(strlen($file['code']), 2).')">'.htmlentities($file['code']).'</textarea>
					<label style="display:block;border-bottom:solid 1px #efefef;padding-bottom:5px;margin-bottom:5px;">
						<input type="checkbox" name="css" value="'.md5($file['src']).'" checked="true"> <a href="'.$file['src'].'" target="_blank">'.$file['src'].'</a> ('.human_filesize(strlen($file['code']), 2).') - Media: '.implode(', ',$file['media']).'
					</label>';
				}

			}


$cssoutput .= '

<br />
<fieldset>
<legend>Full CSS (<span id="fullcsssize">&hellip;</span>)</legend>
<textarea style="width:100%;height:300px;" id="fullcss"></textarea>

	<div style="padding:10px;text-align:left;font-size:20px;line-height:24px;">
		<strong><a href="'.$url.'?extract-css='.$extractkey.'&amp;output=download" onclick="download_css(); return false;">Download</a></strong>
		| <a href="https://www.google.com/search?q=beautify+css+online" target="_blank">Beautify</a>
        | <a href="https://www.google.com/search?q=minify+css+online" target="_blank">Minify</a>

	</div>

</fieldset>


<script type="text/javascript">
	setTimeout(function() {
		update_fullcss();
	},100);
	jQuery(\'input[name=css]\').on(\'change\',function() {
		update_fullcss();
	});
</script>
</body>
</html>';