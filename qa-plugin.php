<?php
		
/*			  
		Plugin Name: Theme Switcher
		Plugin URI: https://github.com/NoahY/q2a-theme-switcher
		Plugin Update Check URI: https://github.com/NoahY/q2a-theme-switcher/raw/master/qa-plugin.php
		Plugin Description: Theme Switcher
		Plugin Version: 1.0b
		Plugin Date: 2011-09-05
		Plugin Author: NoahY
		Plugin Author URI:							  
		Plugin License: GPLv2						   
		Plugin Minimum Question2Answer Version: 1.4.2
*/					  

		if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
						header('Location: ../../');
						exit;   
		}			   
		
		$qa_theme_switch_is_mobile = false;
		
		function qa_theme_chooser() {
			
			$userid = qa_get_logged_in_userid();

			if(function_exists('qa_register_plugin_overrides')) // 1.5
				return qa_db_read_one_value(
					qa_db_query_sub(
						'SELECT meta_value FROM ^usermeta WHERE user_id=# AND meta_key=$',
						$userid, 'custom_theme'
					),true
				);	
			
			require_once QA_INCLUDE_DIR.'qa-app-users.php';
			
			
			if(!$userid) {
				$theme = @$_COOKIE['qa_theme_switch'];

				if(qa_opt('theme_switch_enable_mobile')) {
					$theme_choice_mobile = qa_theme_chooser_detect_mobile()?qa_opt('theme_switch_mobile'):false;
				}
				if($theme) {
					global $qa_theme_switch_is_mobile;
					$qa_theme_switch_is_mobile = $theme_choice_mobile; // it's mobile, but they have a preferred theme
					return $theme;
				}
				return $theme_choice_mobile;
			}
			
			if(qa_opt('theme_switch_enable_mobile')) {
				$theme_choice_mobile = @$_COOKIE['qa_theme_switch']==qa_opt('theme_switch_mobile')?qa_opt('theme_switch_mobile'):false;
				$theme_mobile = qa_theme_chooser_detect_mobile()?qa_opt('theme_switch_mobile'):false;
				
				global $qa_theme_switch_is_mobile;
				$qa_theme_switch_is_mobile = $theme_mobile; // it's mobile, but they have a preferred theme
				if($theme_choice && (!$theme_choice_mobile || !$theme_mobile)) { // have theme choice, and 1) it's not mobile, 2) it's mobile and they don't have a cookie, 3) they have a cookie but it's not mobile, 4) they have a cookie, it's mobile, but the cookie isn't mobile
					// do nothing
				}
				else if(!$theme_choice_mobile && $theme_mobile)  // no cookie, is mobile, give mobile
					return $theme_mobile;
				else if ($theme_choice_mobile && $theme_mobile) // cookie, is mobile, give cookie
					return $theme_choice_mobile;
			}
			if($theme_choice) {
				global $qa_theme_switch_is_mobile;
				return $theme_choice;
			}
			return $theme_choice_mobile;
		}
		
		function qa_theme_chooser_detect_mobile() {
			// error_log($_SERVER['HTTP_USER_AGENT']);
			if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}

			if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
				return true;
			}	

			$useragent=$_SERVER['HTTP_USER_AGENT'];
			if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|ipad|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
				return true;

			if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
				return true;
			}

			if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
				return false;
			}
			
			return false;
		}
		
		qa_register_plugin_module('module', 'qa-theme-admin.php', 'qa_theme_admin', 'Theme Admin');

//		qa_register_plugin_module('process', 'qa-theme-process.php', 'qa_theme_process', 'Theme Process');
		
		qa_register_plugin_layer('qa-theme-layer.php', 'Theme Layer');
		
		if(function_exists('qa_register_plugin_overrides')) { // 1.5
		
			qa_register_plugin_overrides('qa-theme-overrides.php');
			
		}
		else { // 1.4
		
			if(qa_opt('theme_switch_enable')) {
				$this_user_theme = qa_theme_chooser();
				qa_opt('site_theme',$this_user_theme?$this_user_theme:qa_opt('theme_switch_default'));
			}
			else if(qa_opt('theme_switch_enable_mobile')) {
				
				// for mobile switching links

				require_once QA_INCLUDE_DIR.'qa-app-users.php';
				
				$theme_choice = null;
				
				if($userid = qa_get_logged_in_userid()) {
					$theme_choice = qa_db_read_one_value(
						qa_db_query_sub(
							'SELECT meta_value FROM ^usermeta WHERE user_id=# AND meta_key=$',
							$userid, 'custom_theme'
						),true
					);
				}
				else {
					$theme_choice = @$_COOKIE['qa_theme_switch'];
					$qa_theme_switch_is_mobile = $theme_choice == qa_opt('theme_switch_mobile');
				}
				
				if($theme_choice == qa_opt('theme_switch_default') || $theme_choice == qa_opt('theme_switch_mobile')) { // don't allow other themes, since switcher isn't on
					qa_opt('site_theme',$theme_choice);
				}
				
				// check if mobile
				
				$this_user_mobile = qa_theme_chooser_detect_mobile();
				if($this_user_mobile) $qa_theme_switch_is_mobile = true;

				if(!$theme_choice) {
					qa_opt('site_theme',$this_user_mobile?qa_opt('theme_switch_mobile'):qa_opt('theme_switch_default'));
				}
			}		
		}

						
/*							  
		Omit PHP closing tag to help avoid accidental output
*/							  
						  

