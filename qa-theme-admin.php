<?php
	class qa_theme_admin {

		function option_default($option) {
			
			switch($option) {
				case 'theme_switch_title':
					return 'Theme';
				case 'theme_switch_text':
					return 'Choose theme:';
				case 'theme_switch_default':
					return qa_opt('site_theme');
				default:
					return null;				
			}
			
		}
		
		function allow_template($template)
		{
			return ($template!='admin');
		}	   
			
		function admin_form(&$qa_content)
		{					   

		// Process form input
			
			$ok = null;
			
			if (qa_clicked('theme_switch_save')) {
				if(!qa_post_text('theme_switch_enable')) {
					qa_opt('site_theme',qa_opt('theme_switch_default'));
				}

				qa_db_query_sub(
					'CREATE TABLE IF NOT EXISTS ^usermeta (
					meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					user_id bigint(20) unsigned NOT NULL,
					meta_key varchar(255) DEFAULT NULL,
					meta_value longtext,
					PRIMARY KEY (meta_id),
					UNIQUE (user_id,meta_key)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8'
				);		

				qa_opt('theme_switch_enable',(bool)qa_post_text('theme_switch_enable'));
				qa_opt('theme_switch_default',qa_post_text('theme_switch_default'));
				qa_opt('theme_switch_title',qa_post_text('theme_switch_title'));
				qa_opt('theme_switch_text',qa_post_text('theme_switch_text'));
				qa_opt('theme_switch_enable_mobile',(bool)qa_post_text('theme_switch_enable_mobile'));
				qa_opt('theme_switch_mobile',qa_post_text('theme_switch_mobile'));
				global $qa_request;
				qa_redirect($qa_request,array('ok'=>qa_lang_html('admin/options_saved')));
			}
			
					
			// Create the form for display
			
			$themes = qa_admin_theme_options();
			
			$fields = array();
			
			$fields[] = array(
				'label' => 'Enable theme switching',
				'tags' => 'NAME="theme_switch_enable"',
				'value' => qa_opt('theme_switch_enable'),
				'type' => 'checkbox',
			);
				
			$fields[] = array(
				'label' => 'Default theme',
				'tags' => 'NAME="theme_switch_default"',
				'type' => 'select',
				'options' => qa_admin_theme_options(),
				'value' => @$themes[qa_opt('theme_switch_default')],
			);				
		  
				
			$fields[] = array(
				'label' => 'Theme switch title',
				'type' => 'text',
				'value' => qa_html(qa_opt('theme_switch_title')),
				'tags' => 'NAME="theme_switch_title"',
			);		   
			
				
			$fields[] = array(
				'label' => 'Theme switch text',
				'type' => 'text',
				'value' => qa_html(qa_opt('theme_switch_text')),
				'tags' => 'NAME="theme_switch_text"',
			);		   
			
			if(!function_exists('qa_register_plugin_overrides')) { // 1.4
				
				$fields[] = array(
					'label' => 'Enable mobile theme',
					'tags' => 'NAME="theme_switch_enable_mobile"',
					'value' => qa_opt('theme_switch_enable_mobile'),
					'type' => 'checkbox',
				);
					
				$fields[] = array(
					'label' => 'Mobile theme',
					'tags' => 'NAME="theme_switch_mobile"',
					'type' => 'select',
					'options' => qa_admin_theme_options(),
					'value' => @$themes[qa_opt('theme_switch_mobile')],
				);				
			}

			return array(		   
				'ok' => ($ok && !isset($error)) ? $ok : null,
					
				'fields' => $fields,
			 
				'buttons' => array(
					array(
						'label' => 'Save',
						'tags' => 'NAME="theme_switch_save"',
					)
				),
			);
		}
	}

