<?php
/*
 * Copyright 2014-2022 GPLv3, Open Crypto Tracker by Mike Kilday: Mike@DragonFrugal.com
 */


// ###########################################################################################
// SEE /DOCUMENTATION-ETC/PLUGINS-README.txt FOR CREATING YOUR OWN CUSTOM PLUGINS
// ###########################################################################################


// All "plug-conf.php" PLUGIN CONFIG settings MUST BE INSIDE THE "$plug_conf[$this_plug]" ARRAY (sub-arrays are allowed)

// EXAMPLES...

// $plug_conf[$this_plug]['SETTING_NAME_HERE'] = 'mysetting'; 

// $plug_conf[$this_plug]['SETTING_NAME_HERE'] = array('mysetting1', 'mysetting2');


// What runtime modes this plugin should run during (MANDATORY)
$plug_conf[$this_plug]['runtime_mode'] = 'cron'; // 'cron', 'ui', 'all'


// If running in the UI, set the preferred location it should show in
$plug_conf[$this_plug]['ui_location'] = 'tools'; // 'tools', 'more_stats' (defaults to 'tools' if not set)


// If running in the UI, set the preferred plugin name that should show for end-users
$plug_conf[$this_plug]['ui_name'] = 'My Plugin Name'; // (defaults to $this_plug if not set)


// Enable / disable "do not disturb" time (#24 HOUR FORMAT#, HOURS / MINUTES ONLY, SET EITHER TO BLANK '' TO DISABLE)
// THIS TAKES INTO ACCOUNT YOUR TIME ZONE OFFSET, IN 'loc_time_offset' IN THE MAIN CONFIG OF THIS APP ('GENERAL' SECTION)
$plug_conf[$this_plug]['do_not_dist'] = array(
											  // ALWAYS USE THIS FORMAT: '00:00', OR THIS FEATURE WON'T BE ENABLED!
											  'on' => '17:30', // DND #START#, Default = '17:30' (5:30 AT NIGHT)
											  'off' => '9:30' // DND #END#, Default = '9:30' (9:30 IN MORNING)
											  );


// Reminders array (add unlimited reminders as new subarray objects)
$plug_conf[$this_plug]['reminders'] = array(
																	
																	
												// PORTFOLIO RE-BALANCE REVIEW REMINDER
												array(
													 'days' => 30.4167, // Decimals supported (30.4167 days is AVERAGE LENGTH of 1 month)
													 'message' => "Review whether you should re-balance your portfolio (have individual assets take up a different precentage of your portfolio's total " . strtoupper($ct_conf['gen']['btc_prim_currency_pair']) . " value)." // Reminder message
													 ),
																			
																			
												// VITAMIN D / COVID-19 PREVENTION REMINDER
												array(
													 'days' => 4, // Decimals supported
													 'message' => "Take 2000 IU of Vitamin D and 500 MG of Vitamin C every 4 days with food, to help prevent Covid-19 and other viral infections." // Reminder message
													 ),
																	
																	
											); // END reminders array




// DON'T LEAVE ANY WHITESPACE AFTER THE CLOSING PHP TAG!

?>