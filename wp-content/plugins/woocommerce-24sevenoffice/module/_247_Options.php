<?php

class _247_Options{


	public static function getApiSettings(){
		global $api_settings;

		foreach ($api_settings as $key => $value) {
			?>
			<div>
				<label class="mb-backend-label" for="<?php echo $key; ?>"><?php echo $value; ?></label>
				<input type="input" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo get_option($key); ?>" size="50" >
			</div>

			<?php
		}
	}


	public static function updateApiSettings(){
		global $api_settings;

		foreach ($api_settings as $key => $value) {
			if ( isset($_POST[$key]) ){
				update_option( $key, trim($_POST[$key])  );
			}
		}
	}



}