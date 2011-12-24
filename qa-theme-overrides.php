<?php
		
	function qa_get_site_theme() {
		if(qa_opt('theme_switch_enable')) {
			$this_user_theme = qa_theme_chooser();
			qa_opt('site_theme',$this_user_theme?$this_user_theme:qa_opt('theme_switch_default'));
		}
		return qa_get_site_theme_base();
	}
						
/*							  
		Omit PHP closing tag to help avoid accidental output
*/							  
						  

