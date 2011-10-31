<?php

	class qa_html_theme_layer extends qa_html_theme_base {

		var $signatures;
		
	// theme replacement functions
	
		function doctype()
		{
			global $qa_theme_switch_is_mobile;
			
			if($qa_theme_switch_is_mobile && qa_opt('theme_switch_enable_mobile') && qa_opt('site_theme') != qa_opt('theme_switch_mobile')) {
				$this->content['navigation']['footer']['theme_switch'] = array(
					'label' => 'Mobile Version',
					'url' => qa_path($this->request, array('theme_switch'=>qa_opt('theme_switch_mobile'))),
				);
			}
			else if(qa_opt('theme_switch_enable_mobile') && qa_opt('site_theme') == qa_opt('theme_switch_mobile')) {
				$this->content['navigation']['footer']['theme_switch'] = array(
					'label' => 'Full Site',
					'url' => qa_path($this->request, array('theme_switch'=>qa_opt('theme_switch_default'))),
				);
			}
			
			if(@$_GET['theme_switch']) {

			/*

				if($userid = qa_get_logged_in_userid()) {
					qa_db_query_sub(
						'INSERT INTO ^usermeta (user_id,meta_key,meta_value) VALUES (#,$,$) ON DUPLICATE KEY UPDATE meta_value=$',
						$userid,'custom_theme',$_GET['theme_switch'],$_GET['theme_switch']
					);
				}
			*/
				setcookie('qa_theme_switch', $_GET['theme_switch'], time()+86400*365, '/', QA_COOKIE_DOMAIN);
				qa_redirect($this->request,array());
			}

			if (qa_opt('theme_switch_enable')) {
				
				if($this->template == 'user' && !qa_get('tab')) { 

					// add theme switcher

					$handle = preg_replace('/^[^\/]+\/([^\/]+).*/',"$1",$this->request);
					$theme_form = $this->theme_switch_form();
					if($theme_form) {
					
						// insert our form
						
						if($this->content['q_list']) {  // paranoia
							// array splicing kungfu thanks to Stack Exchange
							
							// This adds form-theme-switch before q_list
						
							$keys = array_keys($this->content);
							$vals = array_values($this->content);

							$insertBefore = array_search('q_list', $keys);

							$keys2 = array_splice($keys, $insertBefore);
							$vals2 = array_splice($vals, $insertBefore);

							$keys[] = 'form-theme-switch';
							$vals[] = $theme_form;

							$this->content = array_merge(array_combine($keys, $vals), array_combine($keys2, $vals2));
						}
						else $this->content['form-theme-switch'] = $theme_form;  // this shouldn't happen
					}

				}
			}

			qa_html_theme_base::doctype();
		}
		function head_css()
		{
			qa_html_theme_base::head_css();
			if($this->template == 'user' && qa_opt('theme_switch_enable')) {

			}	
		}
		function head_custom()
		{
			qa_html_theme_base::head_custom();
			if($this->template == 'user' && qa_opt('theme_switch_enable')) {

			}
		}
	
	// worker functions

		function theme_switch_form() {
			// displays signature form in user profile
			
			global $qa_request;
			
			$handle = preg_replace('/^[^\/]+\/([^\/]+).*/',"$1",$qa_request);
			
			$userid = $this->getuserfromhandle($handle);
			
			if(!$userid) return;

			if(qa_get_logged_in_handle() && qa_get_logged_in_handle() == $handle) {

				if (qa_clicked('theme_switch_save')) {
					qa_db_query_sub(
						'INSERT INTO ^usermeta (user_id,meta_key,meta_value) VALUES (#,$,$) ON DUPLICATE KEY UPDATE meta_value=$',
						$userid,'custom_theme',qa_post_text('theme_choice'),qa_post_text('theme_choice')
					);
					qa_redirect($this->request,array('ok'=>qa_lang_html('admin/options_saved')));
				}
				else if (qa_clicked('theme_switch_user_reset')) {
					qa_db_query_sub(
						'DELETE FROM ^usermeta WHERE user_id=# AND meta_key=$',
						$userid,'custom_theme'
					);
					qa_redirect($this->request,array('ok'=>qa_lang_html('admin/options_reset')));
				}

				require_once QA_INCLUDE_DIR.'qa-app-admin.php';
				
				$ok = qa_get('ok')?qa_get('ok'):null;
				
				$theme_choice = qa_db_read_one_value(
					qa_db_query_sub(
						'SELECT meta_value FROM ^usermeta WHERE user_id=# AND meta_key=$',
						$userid, 'custom_theme'
					),true
				);				
				
				$themes = qa_admin_theme_options();
				$fields['themes'] = array(
					'label' => qa_opt('theme_switch_text'),
					'tags' => 'NAME="theme_choice"',
					'type' => 'select',
					'options' => qa_admin_theme_options(),
					'value' => @$themes[$theme_choice],					
				);


				$form=array(
				
					'ok' => ($ok && !isset($error)) ? $ok : null,
					
					'style' => 'tall',
					
					'title' => '<a name="theme_text"></a>'.qa_opt('theme_switch_title'),

					'tags' =>  'action="'.qa_self_html().'#theme_text" method="POST"',
					
					'fields' => $fields,
					
					'buttons' => array(
						array(
                        'label' => qa_lang_html('admin/reset_options_button'),
							'tags' => 'NAME="theme_switch_user_reset"',
						),
						array(
							'label' => qa_lang_html('main/save_button'),
							'tags' => 'NAME="theme_switch_save"',
						),
					),
				);
				return $form;
			}			
		}
		function getuserfromhandle($handle) {
			require_once QA_INCLUDE_DIR.'qa-app-users.php';
			
			if (QA_FINAL_EXTERNAL_USERS) {
				$publictouserid=qa_get_userids_from_public(array($handle));
				$userid=@$publictouserid[$handle];
				
			} 
			else {
				$userid = qa_db_read_one_value(
					qa_db_query_sub(
						'SELECT userid FROM ^users WHERE handle = $',
						$handle
					),
					true
				);
			}
			if (!isset($userid)) return;
			return $userid;
		}
				
		
	}

