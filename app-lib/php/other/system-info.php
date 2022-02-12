<?php
/*
 * Copyright 2014-2022 GPLv3, Open Crypto Tracker by Mike Kilday: Mike@DragonFrugal.com
 */


$system_info = $ct_gen->system_info(); // MUST RUN AFTER SETTING $base_dir
    			
$system_load = $system_info['system_load'];
$system_load = preg_replace("/ \(15 min avg\)(.*)/i", "", $system_load);
$system_load = preg_replace("/(.*)\(5 min avg\) /i", "", $system_load); // Use 15 minute average
    		
$system_temp = preg_replace("/° Celsius/i", "", $system_info['system_temp']);

$system_free_space_mb = $ct_gen->in_megabytes($system_info['free_partition_space'])['in_megs'];

$portfolio_cache_size_mb = $ct_gen->in_megabytes($system_info['portfolio_cache'])['in_megs'];

$system_memory_total_mb = $ct_gen->in_megabytes($system_info['memory_total'])['in_megs'];
    		
$system_memory_free_mb = $ct_gen->in_megabytes($system_info['memory_free'])['in_megs'];
    		
// Percent difference (!MUST BE! absolute value)
$memory_percent_free = abs( ($system_memory_free_mb - $system_memory_total_mb) / abs($system_memory_total_mb) * 100 );
$memory_percent_free = round( 100 - $memory_percent_free, 2);
    		
$system_load_redline = ( $system_info['cpu_threads'] > 1 ? ($system_info['cpu_threads'] * 2) : 2 );


// Interface alert messages (UI / email / etc)
if ( substr($system_info['uptime'], 0, 6) == '0 days' ) {
$system_warnings['uptime'] = 'Low uptime (0 days)';
$system_warnings_cron_interval['uptime'] = 12; // 12 hours
}
	
	
if ( $system_load > $system_load_redline ) {
$system_warnings['system_load'] = 'High CPU load (' . $system_load . ' 15 minute average)';
$system_warnings_cron_interval['system_load'] = 4; // 4 hours
}

	
if ( $system_temp >= $ct_conf['power']['system_temp_warning'] ) {
$system_warnings['system_temp'] = 'High temperature (' . $system_temp . ' degrees celcius)';
$system_warnings_cron_interval['system_temp'] = 1; // 1 hours
}

	
if ( $system_info['memory_used_percent'] >= $ct_conf['power']['memory_used_percent_warning'] ) {
$system_warnings['memory_used_percent'] = 'High memory usage (' . $system_info['memory_used_percent'] . ' percent used)';
$system_warnings_cron_interval['memory_used_percent'] = 4; // 4 hours
}

	
if ( $system_free_space_mb <= $ct_conf['power']['free_partition_space_warning'] ) {
$system_warnings['free_partition_space'] = 'High disk storage usage (only ' . $ct_var->num_pretty($system_free_space_mb, 1) . ' megabytes free space left)';
$system_warnings_cron_interval['free_partition_space'] = 4; // 4 hours
}

	
if ( $portfolio_cache_size_mb >= $ct_conf['power']['portfolio_cache_warning'] ) {
$system_warnings['portfolio_cache_size'] = 'High app cache disk storage usage (' . $ct_var->num_pretty($portfolio_cache_size_mb, 1) . ' megabytes in app cache)';
$system_warnings_cron_interval['portfolio_cache_size'] = 72; // 72 hours
}


// Log errors / send email alerts for any system warnings, if time interval has passed since any previous runs
if ( is_array($system_warnings) && sizeof($system_warnings) > 0 ) {
    
    foreach ( $system_warnings as $key => $unused ) {
    $ct_gen->throttled_warning_log($key);
    }

}



?>