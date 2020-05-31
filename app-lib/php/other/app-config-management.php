<?php
/*
 * Copyright 2014-2020 GPLv3, DFD Cryptocoin Values by Mike Kilday: http://DragonFrugal.com
 */


//////////////////////////////////////////////////////////////////
// APP CONFIG DYNAMIC MANAGEMENT
//////////////////////////////////////////////////////////////////

    

// START CONFIG CLEANUP (auto-correct any basic end user data entry errors in config.php)

// Cleaning lowercase alphanumeric string values, and auto-correct minor errors
$app_config['developer']['debug_mode'] = auto_correct_string($app_config['developer']['debug_mode'], 'lower');
$app_config['comms']['upgrade_check'] = auto_correct_string($app_config['comms']['upgrade_check'], 'lower');
$app_config['general']['btc_primary_currency_pairing'] = auto_correct_string($app_config['general']['btc_primary_currency_pairing'], 'lower');
$app_config['general']['btc_primary_exchange'] = auto_correct_string($app_config['general']['btc_primary_exchange'], 'lower');
$app_config['developer']['log_verbosity'] = auto_correct_string($app_config['developer']['log_verbosity'], 'lower');
$app_config['general']['default_theme'] = auto_correct_string($app_config['general']['default_theme'], 'lower');
$app_config['general']['primary_marketcap_site'] = auto_correct_string($app_config['general']['primary_marketcap_site'], 'lower');
$app_config['comms']['price_alerts_block_volume_error'] = auto_correct_string($app_config['comms']['price_alerts_block_volume_error'], 'lower');
$app_config['power_user']['remote_api_strict_ssl'] = auto_correct_string($app_config['power_user']['remote_api_strict_ssl'], 'lower');
$app_config['general']['charts_toggle'] = auto_correct_string($app_config['general']['charts_toggle'], 'lower');
$app_config['comms']['smtp_secure'] = auto_correct_string($app_config['comms']['smtp_secure'], 'lower');
$app_config['comms']['proxy_alerts'] = auto_correct_string($app_config['comms']['proxy_alerts'], 'lower');
$app_config['comms']['proxy_alerts_runtime'] = auto_correct_string($app_config['comms']['proxy_alerts_runtime'], 'lower');
$app_config['comms']['proxy_alerts_checkup_ok'] = auto_correct_string($app_config['comms']['proxy_alerts_checkup_ok'], 'lower');

// Cleaning charts/alerts array
$cleaned_charts_and_price_alerts = array();
foreach ( $app_config['charts_alerts']['tracked_markets'] as $key => $value ) {
$cleaned_key = auto_correct_string($key, 'lower');
$cleaned_value = auto_correct_string($value, 'lower');
$cleaned_charts_and_price_alerts[$cleaned_key] = $cleaned_value;
}
$app_config['charts_alerts']['tracked_markets'] = $cleaned_charts_and_price_alerts;

// Cleaning mobile networks array
$cleaned_mobile_networks = array();
foreach ( $app_config['mobile_network_text_gateways'] as $key => $value ) {
$cleaned_key = auto_correct_string($key, 'lower');
$cleaned_value = auto_correct_string($value, 'lower');
$cleaned_mobile_networks[$cleaned_key] = $cleaned_value;
}
$app_config['mobile_network_text_gateways'] = $cleaned_mobile_networks;

// END CONFIG CLEANUP



// Dynamically reconfigure / configure where needed


// Default BTC CRYPTO/CRYPTO market pairing support, BEFORE GENERATING MISCASSETS ARRAY
// (so we activate it here instead of in config.php, for good UX adding ONLY altcoin markets dynamically there)
$app_config['power_user']['crypto_pairing']['btc'] = 'Ƀ ';


// Default lite chart mode 'all' (we activate it here instead of in config.php, for good UX adding ONLY day intervals there)
$app_config['power_user']['lite_chart_day_intervals'][] = 'all';



// Dynamically add MISCASSETS to $app_config['portfolio_assets'] BEFORE ALPHABETICAL SORTING
// ONLY IF USER HASN'T MESSED UP $app_config['portfolio_assets'], AS WE DON'T WANT TO CANCEL OUT ANY
// CONFIG CHECKS CREATING ERROR LOG ENTRIES / UI ALERTS INFORMING THEM OF THAT
if (is_array($app_config['portfolio_assets']) || is_object($app_config['portfolio_assets'])) {
    
    $app_config['portfolio_assets']['MISCASSETS'] = array(
                                        'asset_name' => 'Misc. '.strtoupper($app_config['general']['btc_primary_currency_pairing']).' Value',
                                        'marketcap_website_slug' => '',
                                        'market_pairing' => array()
                                        );
            
            
            foreach ( $app_config['power_user']['crypto_pairing'] as $pairing_key => $pairing_unused ) {
            $app_config['portfolio_assets']['MISCASSETS']['market_pairing'][$pairing_key] = array('misc_assets' => $pairing_key);
            }
            
            foreach ( $app_config['power_user']['bitcoin_currency_markets'] as $pairing_key => $pairing_unused ) {
            	
            	// WE HAVE A COUPLE CRYPTOS SUPPORTED HERE, BUT WE ONLY WANT DESIGNATED FIAT-EQIV HERE (cryptos are added via 'crypto_to_crypto_pairing')
            	if ( !array_key_exists($pairing_key, $app_config['power_user']['crypto_pairing']) ) {
            	$app_config['portfolio_assets']['MISCASSETS']['market_pairing'][$pairing_key] = array('misc_assets' => $pairing_key);
            	}
            
            }
    
}



// Update dynamic mining rewards (UI only), if we are using the json config in the secured cache
if ( $runtime_mode == 'ui' && is_array($app_config['power_user']['mining_rewards']) || $runtime_mode == 'ui' && is_object($app_config['power_user']['mining_rewards']) ) {
$app_config['power_user']['mining_rewards']['xmr'] = monero_reward(); // (2^64 - 1 - current_supply * 10^12) * 2^-19 * 10^-12
$app_config['power_user']['mining_rewards']['dcr'] = ( decred_api('subsidy', 'work_reward') / 100000000 );      
}


    
// !!BEFORE MANIPULATING ANYTHING ELSE!!, alphabetically sort all exchanges / pairings for UX
if (is_array($app_config['portfolio_assets']) || is_object($app_config['portfolio_assets'])) {
    
    foreach ( $app_config['portfolio_assets'] as $symbol_key => $symbol_unused ) {
            
            if ( $app_config['portfolio_assets'][$symbol_key] == 'MISCASSETS' ) {
            asort($app_config['portfolio_assets'][$symbol_key]['market_pairing']);
            }
            else {
            ksort($app_config['portfolio_assets'][$symbol_key]['market_pairing']);
            }
            
            
            foreach ( $app_config['portfolio_assets'][$symbol_key]['market_pairing'] as $pairing_key => $pairing_unused ) {
            ksort($app_config['portfolio_assets'][$symbol_key]['market_pairing'][$pairing_key]);
            }
        
    }
    
}


// Alphabetically sort news feeds
ksort($app_config['power_user']['news_feeds']);


// Better decimal support for these vars...
$app_config['power_user']['system_stats_first_chart_highest_value'] = number_to_string($app_config['power_user']['system_stats_first_chart_highest_value']); 
$app_config['general']['primary_currency_decimals_max_threshold'] = number_to_string($app_config['general']['primary_currency_decimals_max_threshold']); 
$app_config['comms']['price_alerts_threshold'] = number_to_string($app_config['comms']['price_alerts_threshold']); 
$app_config['power_user']['hivepower_yearly_interest'] = number_to_string($app_config['power_user']['hivepower_yearly_interest']); 


// Backup archive password protection / encryption
if ( $app_config['general']['backup_archive_password'] != '' ) {
$backup_archive_password = $app_config['general']['backup_archive_password'];
}
else {
$backup_archive_password = false;
}


//////////////////////////////////////////////////////////////////
// END APP CONFIG DYNAMIC MANAGEMENT
//////////////////////////////////////////////////////////////////

?>