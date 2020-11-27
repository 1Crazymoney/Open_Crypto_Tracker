<?php
/*
 * Copyright 2014-2021 GPLv3, DFD Cryptocoin Values by Mike Kilday: http://DragonFrugal.com
 */


require_once($base_dir . '/app-lib/php/other/sub-init/minimized-sub-init.php');


// CSV header
$example_download_array[] = array(
	        							'Asset Symbol',
	        							'Holdings',
	        							'Average Paid (per-token)',
	        							'Margin Leverage',
	        							'Long or Short',
	        							'Exchange ID',
	        							'Market Pairing'
	        							);
	        
// BTC
$example_download_array[] = array(
	        							'BTC',
	        							'0.00123',
	        							'11,500.25',
	        							'0',
	        							'long',
	        							'1',
	        							'usdt'
	        							);		
	        
// LTC
$example_download_array[] = array(
	        							'ETH',
	        							'7.255',
	        							'120.50',
	        							'0',
	        							'long',
	        							'1',
	        							'usdt'
	        							);			
	        
// GRIN
$example_download_array[] = array(
	        							'XMR',
	        							'45.755',
	        							'65.25',
	        							'0',
	        							'long',
	        							'3',
	        							'btc'
	        							);	
	        
// MISCASSETS
$example_download_array[] = array(
	        							'MISCASSETS',
	        							'80.15',
	        							'',
	        							'0',
	        							'long',
	        							'1',
	        							'btc'
	        							);							
	        							


// Run last, as it exits when completed
create_csv_file('temp', 'Crypto_Portfolio_Example.csv', $example_download_array); 


?>