<?php
/*
 * Copyright 2014-2021 GPLv3, Open Crypto Portfolio Tracker by Mike Kilday: http://DragonFrugal.com
 */


//////////////////////////////////////////////////////////////////
// Scheduled maintenance (run every ~3 hours if NOT cron runtime, OR if runtime is cron every ~1 hours)
//////////////////////////////////////////////////////////////////
if ( $runtime_mode != 'cron' && update_cache($base_dir . '/cache/events/scheduled-maintenance.dat', (60 * 3) ) == true 
|| $runtime_mode == 'cron' && update_cache($base_dir . '/cache/events/scheduled-maintenance.dat', (60 * 1) ) == true  ) {
//////////////////////////////////////////////////////////////////



	////////////////////////////////////////////////////////////
	// Maintenance to run only if cron is setup and running
	////////////////////////////////////////////////////////////
	if ( $runtime_mode == 'cron' ) {
	
		// Chart backups...run before any price checks to avoid any potential file lock issues
		if ( $ocpt_conf['gen']['asset_charts_toggle'] == 'on' && $ocpt_conf['power']['charts_backup_freq'] > 0 ) {
		backup_archive('charts-data', $base_dir . '/cache/charts/', $ocpt_conf['power']['charts_backup_freq']); // No $backup_arch_pass extra param here (waste of time / energy to encrypt charts data backups)
		}
   
	}
	////////////////////////////////////////////////////////////
	// END cron-only maintenance routines
	////////////////////////////////////////////////////////////
	

// Upgrade check
require($base_dir . '/app-lib/php/other/upgrade-check.php');


// Update cached vars...

// Current default primary currency stored to flat file (for checking if we need to reconfigure things for a changed value here)
$ocpt_cache->save_file($base_dir . '/cache/vars/default_btc_prim_curr_pairing.dat', $default_btc_prim_curr_pairing);
	

// Current app version stored to flat file (for the bash auto-install/upgrade script to easily determine the currently-installed version)
$ocpt_cache->save_file($base_dir . '/cache/vars/app_version.dat', $app_version);


// Determine / store portfolio cache size
$ocpt_cache->save_file($base_dir . '/cache/vars/cache_size.dat', convert_bytes( dir_size($base_dir . '/cache/') , 3) );


// Cache files cleanup...

// Delete ANY old zip archive backups scheduled to be purged
delete_old_files($base_dir . '/cache/secured/backups', $ocpt_conf['power']['backup_arch_del_old'], 'zip');


// Stale cache files cleanup...

delete_old_files($base_dir . '/cache/events/throttling', 1, 'dat'); // Delete throttling event tracking cache files older than 1 day

delete_old_files($base_dir . '/cache/events/lite_chart_rebuilds', 3, 'dat'); // Delete lite chart rebuild event tracking cache files older than 3 days

delete_old_files($base_dir . '/cache/secured/activation', 1, 'dat'); // Delete activation cache files older than 1 day

delete_old_files($base_dir . '/cache/secured/external_api', 1, 'dat'); // Delete external API cache files older than 1 day

delete_old_files($base_dir . '/internal-api', 1, 'dat'); // Delete internal API cache files older than 1 day


// Secondary logs cleanup
$logs_cache_cleanup = array(
									$base_dir . '/cache/logs/debugging/external_api',
									$base_dir . '/cache/logs/errors/external_api',
									);
									
delete_old_files($logs_cache_cleanup, $ocpt_conf['power']['logs_purge'], 'dat'); // Delete LOGS API cache files older than $ocpt_conf['power']['logs_purge'] day(s)


// Update the maintenance event tracking
$ocpt_cache->save_file($base_dir . '/cache/events/scheduled-maintenance.dat', $ocpt_gen->time_date_format(false, 'pretty_date_time') );


}
//////////////////////////////////////////////////////////////////
// END SCHEDULED MAINTENANCE
//////////////////////////////////////////////////////////////////

 
 ?>