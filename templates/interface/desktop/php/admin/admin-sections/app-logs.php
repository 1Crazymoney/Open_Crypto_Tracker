<?php
/*
 * Copyright 2014-2022 GPLv3, Open Crypto Tracker by Mike Kilday: Mike@DragonFrugal.com
 */


?>


	
   <ul>
	
	<li class='bitcoin' style='font-weight: bold;'>Error / debugging logs will automatically display here, if they exist (primary error log always shows, even if empty).</li>	
	
	<li class='bitcoin' style='font-weight: bold;'>All log timestamps are UTC time (Coordinated Universal Time).</li>	
	
	<li class='bitcoin' style='font-weight: bold;'>Current UTC time: <span class='utc_timestamp red'></span></li>	
   
   </ul>
	
		
		<p class='red' style='font-weight: bold;'>*Log format: </p>
		
	   <!-- Looks good highlighted as: less, yaml  -->
	   <pre class='rounded' style='display: inline-block;<?=( $ct_gen->is_msie() == false ? ' padding-top: 1em !important;' : '' )?>'><code class='hide-x-scroll less' style='white-space: nowrap; width: auto; display: inline-block;'>[UTC timestamp] runtime_mode => error_type: error_msg; [ (tracing if log verbosity set to verbose) ]</code></pre>
	
	
	    <fieldset class='subsection_fieldset'><legend class='subsection_legend'> Error Log </legend>
	        
	        <p>
	        
	        <b>Extra Spacing:</b> <input type='checkbox' id='error_log_space' value='1' onchange="system_logs('error_log');" />
	        
	        &nbsp; <b>Last lines:</b> <input type='text' id='error_log_lines' value='100' maxlength="5" size="4" />
	        
	        &nbsp; <button class='force_button_style' onclick="copy_text('error_log', 'error_log_alert');">Copy To Clipboard</button> 
	        
	        &nbsp; <button class='force_button_style' onclick="system_logs('error_log');">Refresh</button> 
	        
	        &nbsp; <span id='error_log_alert' class='red'></span>
	        
	        </p>
	        
	        <!-- Looks good highlighted as: less, yaml  -->
	        <pre class='rounded'><code class='hide-x-scroll less' style='width: 100%; height: 750px;' id='error_log'></code></pre>
			  
			  <script>
			  system_logs('error_log');
			  </script>
		
	    </fieldset>
				
	<?php
	if ( $ct_conf['dev']['debug'] != 'off' || is_readable($base_dir . '/cache/logs/debug.log') ) {
	?>
	    <fieldset class='subsection_fieldset'><legend class='subsection_legend'> Debugging Log </legend>
	        
	        <p>
	        
	        <b>Extra Spacing:</b> <input type='checkbox' id='debug_log_space' value='1' onchange="system_logs('debug_log');" />
	        
	        &nbsp; <b>Last lines:</b> <input type='text' id='debug_log_lines' value='100' maxlength="5" size="4" />
	        
	        &nbsp; <button class='force_button_style' onclick="copy_text('debug_log', 'debug_log_alert');">Copy To Clipboard</button> 
	        
	        &nbsp; <button class='force_button_style' onclick="system_logs('debug_log');">Refresh</button> 
	        
	        &nbsp; <span id='debug_log_alert' class='red'></span>
	        
	        </p>
	        
	        <!-- Looks good highlighted as: less, yaml  -->
	        <pre class='rounded'><code class='hide-x-scroll less' style='width: 100%; height: 750px;' id='debug_log'></code></pre>
			  
			  <script>
			  system_logs('debug_log');
			  </script>
		
	    </fieldset>
	    
	<?php
	}
	if ( is_readable($base_dir . '/cache/logs/smtp_error.log') ) {
	?>
	    <fieldset class='subsection_fieldset'><legend class='subsection_legend'> SMTP Error Log </legend>
	        
	        <p>
	        
	        <b>Extra Spacing:</b> <input type='checkbox' id='smtp_error_log_space' value='1' onchange="system_logs('smtp_error_log');" />
	        
	        &nbsp; <b>Last lines:</b> <input type='text' id='smtp_error_log_lines' value='100' maxlength="5" size="4" />
	        
	        &nbsp; <button class='force_button_style' onclick="copy_text('smtp_error_log', 'smtp_error_log_alert');">Copy To Clipboard</button> 
	        
	        &nbsp; <button class='force_button_style' onclick="system_logs('smtp_error_log');">Refresh</button> 
	        
	        &nbsp; <span id='smtp_error_log_alert' class='red'></span>
	        
	        </p>
	        
	        <!-- Looks good highlighted as: less, yaml  -->
	        <pre class='rounded'><code class='hide-x-scroll less' style='width: 100%; height: 750px;' id='smtp_error_log'></code></pre>
			  
			  <script>
			  system_logs('smtp_error_log');
			  </script>
		
	    </fieldset>
	<?php
	}
	if ( is_readable($base_dir . '/cache/logs/smtp_debug.log') ) {
	?>
	    <fieldset class='subsection_fieldset'><legend class='subsection_legend'> SMTP Debugging Log </legend>
	        
	        <p>
	        
	        <b>Extra Spacing:</b> <input type='checkbox' id='smtp_debug_log_space' value='1' onchange="system_logs('smtp_debug_log');" />
	        
	        &nbsp; <b>Last lines:</b> <input type='text' id='smtp_debug_log_lines' value='100' maxlength="5" size="4" />
	        
	        &nbsp; <button class='force_button_style' onclick="copy_text('smtp_debug_log', 'smtp_debug_log_alert');">Copy To Clipboard</button> 
	        
	        &nbsp; <button class='force_button_style' onclick="system_logs('smtp_debug_log');">Refresh</button> 
	        
	        &nbsp; <span id='smtp_debug_log_alert' class='red'></span>
	        
	        </p>
	        
	        <!-- Looks good highlighted as: less, yaml  -->
	        <pre class='rounded'><code class='hide-x-scroll less' style='width: 100%; height: 750px;' id='smtp_debug_log'></code></pre>
			  
			  <script>
			  system_logs('smtp_debug_log');
			  </script>
		
	    </fieldset>
	<?php
	}
	?>
	    
			    


		    