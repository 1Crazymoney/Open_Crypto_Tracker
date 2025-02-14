<?php
/*
 * Copyright 2014-2022 GPLv3, Open Crypto Tracker by Mike Kilday: Mike@DragonFrugal.com
 */


// Forbid direct INTERNET access to this file, UNLESS IT'S EMULATED CRON IN THE DESKTOP EDITION
if ( !isset($_GET['cron_emulate']) && isset($_SERVER['REQUEST_METHOD']) && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']) ) {
header('HTTP/1.0 403 Forbidden', TRUE, 403);
exit;
}


// Make sure the cron job will always finish running completely
ignore_user_abort(true); 


// Assure CLI runtime is in install directory (server compatibility required for some PHP setups)
chdir( dirname(__FILE__) );


// Calculate script runtime length
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start_runtime = $time;


// Runtime mode
$runtime_mode = 'cron';

// Load app config / etc
require("config.php");


//////////////////////////////////////////////
/// CRON LOGIC #START#
//////////////////////////////////////////////

  
// ONLY run cron if it is allowed
if ( $run_cron == true ) {
     
$cron_run_lock_file = $base_dir . '/cache/events/emulated-cron-lock.dat';
    
    
    // If we find no file lock (OR if there is a VERY stale file lock [OVER 9 MINUTES OLD]), we can proceed
    // (we don't want Desktop Editions to run multiple cron runtimes at the same time, if they are also
    // viewing in a regular browser on localhost port 22345)
    if ( $ct_cache->update_cache($cron_run_lock_file, 9) == true ) {  
    
    // Re-save new file lock
    $ct_cache->save_file($cron_run_lock_file, $ct_gen->time_date_format(false, 'pretty_date_time') );
    
    /////////////////////////////////////////////////
    ////////////FILE-LOCKED START////////////////////
    /////////////////////////////////////////////////
    
    
        // If we are running EMULATED cron, we track when to run it with /cache/events/emulated-cron.dat
        if ( isset($_GET['cron_emulate']) && $ct_conf['power']['desktop_cron_interval'] > 0 ) {
            
            
            if ( $ct_cache->update_cache($base_dir . '/cache/events/emulated-cron.dat', $ct_conf['power']['desktop_cron_interval']) == false ) {
            
            $exit_result = array('result' => "Too early to re-run EMULATED cron job");
            
            // Log errors / debugging, send notifications
            $ct_cache->error_log();
            $ct_cache->debug_log();
            $ct_cache->send_notifications();
            
            echo json_encode($exit_result, JSON_PRETTY_PRINT);
            exit; // Force exit runtime now
        
            }
            else {
            // We run this EARLY in the cron logic, so we have fairly consistant emulated cron job intervals
            $ct_cache->save_file($base_dir . '/cache/events/emulated-cron.dat', $ct_gen->time_date_format(false, 'pretty_date_time') );
            }
        
        
        }
      

        ///////////////////////////////////////////////////////////////////////////////////////
        // Only run below logic if cron has run for the first time already (for better new install UX)
        ///////////////////////////////////////////////////////////////////////////////////////
        if ( file_exists($base_dir . '/cache/events/cron-first-run.dat') ) {
        
            
            // Only run if charts / alerts has run for the first time already (for better new install UX)
            // #MUST# BE ABOVE CHARTS / ALERTS LOGIC!
            if ( file_exists($base_dir . '/cache/events/charts-first-run.dat') ) {
            
            	// Re-cache RSS feeds for faster UI runtimes later
            	foreach($ct_conf['power']['news_feed'] as $feed_item) {
            	    
            		if ( isset($feed_item["url"]) && trim($feed_item["url"]) != '' ) {
            	 	$ct_api->rss($feed_item["url"], 'no_theme', 0, true);
            	 	}
            	 	
            	}
        	
            	// News feeds - new posts email
            	if ( $ct_conf['comms']['news_feed_email_freq'] > 0 ) {
            	$ct_gen->news_feed_email($ct_conf['comms']['news_feed_email_freq']);
            	}
        	
            }
        
        
            // Charts and price alerts
            foreach ( $ct_conf['charts_alerts']['tracked_mrkts'] as $key => $val ) {
            
            $val = explode("||",$val); // Convert $val into an array
            
            $exchange = $val[0];
            $pair = $val[1];
            $mode = $val[2];
            
            // ALWAYS RUN even if $mode != 'none' etc, as charts_price_alerts() is optimized to run UX logic scanning
            // (such as as removing STALE EXISTING ALERT CACHE FILES THAT WERE PREVIOUSLY-ENABLED,
            // THEN USER-DISABLED...IN CASE USER RE-ENABLES, THE ALERT STATS / ETC REMAIN UP-TO-DATE)
            $ct_asset->charts_price_alerts($key, $exchange, $pair, $mode);
            
            }
        
        
            // Flag if we have run the first alerts / charts job (for logic to improve speed of first time run of cron tasks, skipping uneeded pre-caching etc)
            if ( !file_exists($base_dir . '/cache/events/charts-first-run.dat') ) {
            $ct_cache->save_file($base_dir . '/cache/events/charts-first-run.dat', $ct_gen->time_date_format(false, 'pretty_date_time') );
            }
        
        
            // Checkup on each failed proxy
            if ( $ct_conf['comms']['proxy_alert'] != 'off' ) {
            	
            	foreach ( $proxy_checkup as $problem_proxy ) {
            	$ct_gen->test_proxy($problem_proxy);
            	sleep(1);
            	}
            
            }
            
        
        // Queue notifications if there were any price alert resets, BEFORE $ct_cache->send_notifications() runs
        $ct_gen->reset_price_alert_notice();
        
        
        }
        ///////////////////////////////////////////////////////////////////////////////////////
        // END after first-run only
        ///////////////////////////////////////////////////////////////////////////////////////
        
        
        // Calculate script runtime length (BEFORE system stats logging)
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $total_runtime = round( ($time - $start_runtime) , 3);
        
        
        // System stats, chart the 15 min load avg / temperature / free partition space / free memory [mb/percent] / portfolio cache size / runtime length
        // RUN BEFORE plugins (in case custom plugin crashes)
        
        if ( isset($system_info['system_load']) ) {
        $chart_data_set .= '||' . trim($system_load);
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        
        
        if ( isset($system_info['system_temp']) ) {
        $chart_data_set .= '||' . trim($system_temp);
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        
        
        if ( isset($system_info['memory_used_megabytes']) ) {
        $chart_data_set .= '||' . round( $system_info['memory_used_megabytes'] / 1000 , 4); // Gigabytes, for chart UX
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        
        
        if ( isset($system_info['memory_used_percent']) ) {
        $chart_data_set .= '||' . $system_info['memory_used_percent'];
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        
        
        if ( isset($system_info['free_partition_space']) ) {
        $chart_data_set .= '||' . round( trim($system_free_space_mb) / 1000000 , 4); // Terabytes, for chart stats UX
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        
        
        if ( isset($system_info['portfolio_cache']) ) {
        $chart_data_set .= '||' . round( trim($portfolio_cache_size_mb) / 1000 , 4); // Gigabytes, for chart UX
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        
        
        if ( trim($total_runtime) >= 0 ) {
        $chart_data_set .= '||' . trim($total_runtime);
        }
        else {
        $chart_data_set .= '||NO_DATA';
        }
        	
        
        // In case a rare error occured from power outage / corrupt memory / etc, we'll check the timestamp (in a non-resource-intensive way)
        // (#SEEMED# TO BE A REAL ISSUE ON A RASPI ZERO AFTER MULTIPLE POWER OUTAGES [ONE TIMESTAMP HAD PREPENDED CORRUPT DATA])
        $now = time();
        
        
        // (WE DON'T WANT TO STORE DATA WITH A CORRUPT TIMESTAMP)
        if ( $now > 0 ) {
        
        // Store system data to archival / lite charts
        $sys_stats_path = $base_dir . '/cache/charts/system/archival/system_stats.dat';
        $sys_stats_data = $now . $chart_data_set;
        
        $ct_cache->save_file($sys_stats_path, $sys_stats_data . "\n", "append", false); // WITH newline (UNLOCKED file write)
            		
        // Lite charts (update time dynamically determined in $ct_cache->update_lite_chart() logic)
        // Try to assure file locking from archival chart updating has been released, wait 0.12 seconds before updating lite charts
        usleep(120000); // Wait 0.12 seconds
        		
        	foreach ( $ct_conf['power']['lite_chart_day_intervals'] as $light_chart_days ) {
        	    
        	    // If we reset light charts, just skip the rest of this update session
        	    if ( $system_light_chart_result == 'reset' ) {
        	    continue;
        	    }
        	           
        	$system_light_chart_result = $ct_cache->update_lite_chart($sys_stats_path, $sys_stats_data, $light_chart_days); // WITHOUT newline (var passing)
        	
        	}
        		
        }
        else {
        	
        $ct_gen->log(
        			'system_error',
        			'time() returned a corrupt value (from power outage / corrupt memory / etc), chart updating canceled',
        			'chart_type: system stats'
        			);
        
        }
        		
        // SYSTEM STATS END
        		
        
        // If debug mode is on
        // RUN BEFORE plugins (in case custom plugin crashes)
        if ( $ct_conf['dev']['debug'] == 'all' || $ct_conf['dev']['debug'] == 'all_telemetry' || $ct_conf['dev']['debug'] == 'stats' ) {
        		
        	foreach ( $system_info as $key => $val ) {
        	$system_telemetry .= $key . ': ' . $val . '; ';
        	}
        			
        // Log system stats
        $ct_gen->log(
        			'system_debug',
        			'Hardware / software stats (requires log_verbosity set to verbose)',
        			$system_telemetry
        			);
        			
        // Log runtime stats
        $ct_gen->log(
        			'system_debug',
        			strtoupper($runtime_mode).' runtime was ' . $total_runtime . ' seconds'
        			);
        
        }
        
        
        // Log errors / debugging, send notifications
        // RUN BEFORE any activated plugins (in case a custom plugin crashes)
        $ct_cache->error_log();
        $ct_cache->debug_log();
        $ct_cache->send_notifications();
        
        
        // If any plugins are activated, RESET $log_array for plugin logging, SO WE DON'T GET DUPLICATE LOGGING
        if ( is_array($activated_plugins['cron']) && sizeof($activated_plugins['cron']) > 0 ) {
            
        $log_array = array();
        
        // Give a bit of time for the "core runtime" error / debugging logs to 
        // close their file locks, before we append "plugin runtime" log data
        sleep(1); 
        			
        }
        
        
        // DEBUGGING ONLY (checking logging capability)
        //$ct_cache->check_log('cron.php:pre-plugin-runtime');
        
        
        // Run any cron-designated plugins activated in ct_conf
        // ALWAYS KEEP PLUGIN RUNTIME LOGIC INLINE (NOT ISOLATED WITHIN A FUNCTION), 
        // SO WE DON'T NEED TO WORRY ABOUT IMPORTING GLOBALS!
        foreach ( $activated_plugins['cron'] as $plugin_key => $plugin_init ) {
        		
        $this_plug = $plugin_key;
        	
        	if ( file_exists($plugin_init) ) {
        	
        		// This plugin's default class (only if the file exists)
        		if ( file_exists($base_dir . '/plugins/'.$this_plug.'/plug-lib/plug-class.php') ) {
                include($base_dir . '/plugins/'.$this_plug.'/plug-lib/plug-class.php');
        		}
        	
        	// This plugin's plug-init.php file (runs the plugin)
        	include($plugin_init);
        	
        	}
        	
        // Reset $this_plug at end of loop
        unset($this_plug); 
        
        }
        
        
        // DEBUGGING ONLY (checking logging capability)
        //$ct_cache->check_log('cron.php:post-plugin-runtime');
        
        
        // Log errors / debugging, send notifications
        // (IF ANY PLUGINS ARE ACTIVATED, RAN AGAIN SEPERATELY FOR PLUGIN LOGGING / ALERTS ONLY)
        if ( is_array($activated_plugins['cron']) && sizeof($activated_plugins['cron']) > 0 ) {
        $ct_cache->error_log();
        $ct_cache->debug_log();
        $ct_cache->send_notifications();
        }
        
        
        // Flag if we have run the first cron job (for logic to improve speed of first time run of cron tasks, skipping uneeded pre-caching etc)
        if ( !file_exists($base_dir . '/cache/events/cron-first-run.dat') ) {
        $ct_cache->save_file($base_dir . '/cache/events/cron-first-run.dat', $ct_gen->time_date_format(false, 'pretty_date_time') );
        }
        
              
    $exit_result = array('result' => "Emulated cron job has finished running");

      
    /////////////////////////////////////////////////
    ////////////FILE-LOCKED END//////////////////////
    /////////////////////////////////////////////////
      
    // We are done running cron, so we can release the lock
    unlink($cron_run_lock_file);
    
    }
    else {
    
    $exit_result_text = 'another instance of cron is already running, skipping this additional instance';
    
    $ct_gen->log('other_error', $exit_result_text);
    
    $ct_cache->error_log();
    $ct_cache->debug_log();
    $ct_cache->send_notifications();
    
    $exit_result = array('display_error' => 1, 'result' => $exit_result_text);
    
    }


gc_collect_cycles(); // Clean memory cache
    
    
    // If emulated cron, show a result in json (for interface / console log)
    if ( isset($_GET['cron_emulate']) ) {
    echo json_encode($exit_result, JSON_PRETTY_PRINT);
    }
     

exit; // For extra security, force exit at end of this script file

}
  

//////////////////////////////////////////////
/// CRON LOGIC #END#
//////////////////////////////////////////////


// DON'T LEAVE ANY WHITESPACE AFTER THE CLOSING PHP TAG!

?>