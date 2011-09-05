<?php
        
/*              
        Plugin Name: Theme Switcher
        Plugin URI: https://github.com/NoahY/q2a-theme-switcher
        Plugin Description: Theme Switcher
        Plugin Version: 0.1
        Plugin Date: 2011-09-05
        Plugin Author: NoahY
        Plugin Author URI:                              
        Plugin License: GPLv2                           
        Plugin Minimum Question2Answer Version: 1.4
*/                      
                        
        
        if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
                        header('Location: ../../');
                        exit;   
        }               
        
        function qa_theme_chooser() {
            if(@qa_opt('theme_switch_enable_mobile')) {
                $theme_choice = qa_theme_chooser_detect_mobile()?@qa_opt('theme_switch_mobile'):false;
            }
            if($theme_choice) return $theme_choice;

            require_once QA_INCLUDE_DIR.'qa-app-users.php';
            
            $theme_choice = qa_db_read_one_value(
                qa_db_query_sub(
                    'SELECT meta_value FROM ^usermeta WHERE user_id=# AND meta_key=$',
                    qa_get_logged_in_userid(), 'custom_theme'
                ),true
            );
            
            return $theme_choice;
        }
        if(@qa_opt('theme_switch_enable')) {
            $this_user_theme = qa_theme_chooser();
            @qa_opt('site_theme',$this_user_theme?$this_user_theme:@qa_opt('theme_switch_default'));
        }

        qa_register_plugin_module('module', 'qa-theme-admin.php', 'qa_theme_admin', 'Theme Admin');
        
        qa_register_plugin_layer('qa-theme-layer.php', 'Theme Layer');
        
        function qa_theme_chooser_detect_mobile() {
            $mobile_browser = '0';

            if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $mobile_browser++;
            }

            if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
                $mobile_browser++;
            }    

            $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
            $mobile_agents = array(
                'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
                'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
                'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
                'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
                'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
                'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
                'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
                'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                'wapr','webc','winw','winw','xda','xda-');

            if(in_array($mobile_ua,$mobile_agents)) {
                $mobile_browser++;
            }

            if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
                $mobile_browser++;
            }

            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
                $mobile_browser=0;
            }
            return $mobile_browser;
        }
                        
/*                              
        Omit PHP closing tag to help avoid accidental output
*/                              
                          

