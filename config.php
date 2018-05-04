<?php
/*
 * Copyright 2014-2018 GPLv3, DFD Cryptocoin Values by Mike Kilday: http://DragonFrugal.com
 */


// Forbid direct access to config.php
if ( realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']) ) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}


//apc_clear_cache(); apcu_clear_cache(); opcache_reset();  // DEBUGGING ONLY
 
$version = '2.0.4';  // 2018/MAY/3RD
 
session_start();
require_once("app.lib/php/functions.php");
require_once("app.lib/php/cookies.php");
require_once("app.lib/php/init.php");


/*
 * USAGE (ADDING / UPDATING COINS) ...API support for: kraken / gatecoin / poloniex / coinbase / bitstamp / bittrex / bitfinex and ethfinex / cryptofresh / bter / gemini / hitbtc / liqui / cryptopia / livecoin / upbit / kucoin / okex...BTC, XMR, ETH, LTC, AND USDT trading pair support
 * Ethereum ICO subtoken support has been built in, but values are static ICO values in ETH
 *
 SEE THE BOTTOM OF THE README.txt FOR FOR AN EXAMPLE SET OF PRE-CONFIGURED ASSETS
 
 
                    // UPPERCASE_COIN_SYMBOL
                    'UPPERCASE_COIN_SYMBOL' => array(
                        
                        'coin_name' => 'COIN_NAME',
                        'coin_symbol' => 'UPPERCASE_COIN_SYMBOL',
                        'coinmarketcap' => 'coin-slug', // Is this coin listed on coinmarketcap, leave blank if not
                        'ico' => 'no', // yes / no ...was this an ICO
                        'market_pairing' => array(
                                    'btc' => array(
                                          'LOWERCASE_MARKETPLACE1' => 'MARKETNUMBERHERE',
                                          'LOWERCASE_MARKETPLACE2' => 'BTC_COINSYMBOLHERE',
                                          'LOWERCASE_MARKETPLACE3' => 'BTC-COINSYMBOLHERE'
                                                    ),
                                    'xmr' => array(
                                          'LOWERCASE_MARKETPLACE1' => 'MARKETNUMBERHERE',
                                          'LOWERCASE_MARKETPLACE2' => 'XMR_COINSYMBOLHERE',
                                          'LOWERCASE_MARKETPLACE3' => 'XMR-COINSYMBOLHERE'
                                                    ),
                                    'eth' => array(
                                          'LOWERCASE_MARKETPLACE1' => 'MARKETNUMBERHERE',
                                          'LOWERCASE_MARKETPLACE2' => 'ETH_COINSYMBOLHERE',
                                          'LOWERCASE_MARKETPLACE3' => 'ETH-COINSYMBOLHERE',
                                          'eth_subtokens_ico' => 'THEDAO' // Must be defined in $eth_subtokens_ico_values at top of config.php
                                                    ),
                                    'ltc' => array(
                                          'LOWERCASE_MARKETPLACE1' => 'MARKETNUMBERHERE',
                                          'LOWERCASE_MARKETPLACE2' => 'LTC_COINSYMBOLHERE',
                                          'LOWERCASE_MARKETPLACE3' => 'LTC-COINSYMBOLHERE'
                                                    ),
                                    'usdt' => array(
                                          'LOWERCASE_MARKETPLACE1' => 'MARKETNUMBERHERE',
                                          'LOWERCASE_MARKETPLACE2' => 'USDT_COINSYMBOLHERE',
                                          'LOWERCASE_MARKETPLACE3' => 'USDT-COINSYMBOLHERE'
                                                    )
                                          ),
                        'default_pairing' => 'LOWERCASE_BTC_OR_XMR_OR_ETH_OR_LTC_OR_USDT_TRADING_PAIR'
                        
                    ),
                    
                    
                    
 * 
 */


/////////////////// GENERAL CONFIG -START- ////////////////////////////////////////////////////

$api_timeout = 15; // Seconds to wait for response from API endpoint

$btc_in_usd = 'coinbase'; // Default Bitcoin value in USD: coinbase / bitfinex / gemini / okcoin / bitstamp / kraken / hitbtc / gatecion / livecoin

$coinmarketcap_ranks_max = '400'; // Maximum number of Coinmarketcap.com rankings to request from their API

$eth_subtokens_ico_values = array(
                        // Static values in ETH for Ethereum subtokens, like during crowdsale periods etc
                        'ETHSUBTOKENNAME' => '0.15',
                        'GOLEM' => '0.001',
                        'SWARMCITY' => '0.0133333333333333',
                        'ARAGON' => '0.01',
                        'STATUS' => '0.0001',
                        'INVESTFEED' => '0.0001',
                        '0XPROJECT' => '0.00016929425',
                        'DECENTRALAND' => '0.00008'
                        );


$mining_rewards = array(
					//Mining rewards for different platforms (to prefill editable mining calculator forms)
					'eth' => '3',
					'dcr' => '13',
					'vtc' => '25',
					'rvn' => '5000'
					);


/*
 * STEEM Power yearly interest rate START 11/29/2016 (1.425%, decreasing every year by roughly 0.075% until it hits a minimum of 0.075% and stays there)
 */
$steempower_yearly_interest = 1.425;  // 1.425 (DO NOT INCLUDE PERCENT SIGN) the first year at 11/29/2016 refactored rates, see above for manual yearly adjustment
$steem_powerdown_time = 13;  // Weeks to power down all STEEM Power holdings

/////////////////// GENERAL CONFIG -END- ////////////////////////////////////////////////////




/////////////////// COIN MARKETS CONFIG -START- ////////////////////////////////////////////////////

$coins_array = array(
                
                    // BTC
                    'BTC' => array(
                        
                        'coin_name' => 'Bitcoin',
                        'coin_symbol' => 'BTC',
                        'coinmarketcap' => 'bitcoin',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'okcoin' => 'okcoin',
                                          'bitfinex' => 'tBTCUSD',
                                          'kraken' => 'kraken',
                                          'coinbase' => 'coinbase',
                                          'bitstamp' => 'bitstamp',
                                          'gemini' => 'gemini',
                                          'hitbtc' => 'hitbtc',
                                          'gatecoin' => 'gatecoin',
                                          'livecoin' => 'livecoin'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // Misc. USD Assets
                    'USD' => array(
                        
                        'coin_name' => 'Misc. USD Assets',
                        'coin_symbol' => 'USD',
                        'coinmarketcap' => '',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'usd_assets' => 'usdtobtc'
                                                    ),
                                    'xmr' => array(
                                          'usd_assets' => 'usdtoxmr'
                                                    ),
                                    'eth' => array(
                                          'usd_assets' => 'usdtoeth'
                                                    ),
                                    'ltc' => array(
                                          'usd_assets' => 'usdtoltc'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // XMR
                    'XMR' => array(
                        
                        'coin_name' => 'Monero',
                        'coin_symbol' => 'XMR',
                        'coinmarketcap' => 'monero',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_XMR',
                                          'bittrex' => 'BTC-XMR',
                                        	'upbit' => 'BTC-XMR',
                                          'bitfinex' => 'tXMRBTC',
                                        	'binance' => 'XMRBTC',
                                          'hitbtc' => 'XMRBTC',
                                          'kraken' => 'XXMRXXBT',
                                          'cryptopia' => 'XMR/BTC',
                                          'okex' => 'xmr_btc',
                                          'bter' => 'xmr_btc',
                                          'livecoin' => 'XMR/BTC'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-XMR',
                                          'upbit' => 'ETH-XMR',
                                          'hitbtc' => 'XMRETH',
                                        	'binance' => 'XMRETH'
                                                    ),
                                    'usdt' => array(
                                          'bittrex' => 'USDT-XMR',
                                          'upbit' => 'USDT-XMR',
                                          'poloniex' => 'USDT_XMR',
                                          'okex' => 'xmr_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // ETH
                    'ETH' => array(
                        
                        'coin_name' => 'Ethereum',
                        'coin_symbol' => 'ETH',
                        'coinmarketcap' => 'ethereum',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_ETH',
                                          'kraken' => 'XETHXXBT',
                                          'coinbase' => 'ETH',
                                          'hitbtc' => 'ETHBTC',
                                          'gatecoin' => 'ETHBTC',
                                          'bitfinex' => 'tETHBTC',
                                          'bitstamp' => 'ethbtc',
                                          'gemini' => 'ethbtc',
                                          'bittrex' => 'BTC-ETH',
                                          'upbit' => 'BTC-ETH',
                                          'binance' => 'ETHBTC',
                                          'kucoin' => 'ETH-BTC',
                                          'okex' => 'eth_btc',
                                          'livecoin' => 'ETH/BTC',
                                          'liqui' => 'eth_btc',
                                          'bter' => 'eth_btc',
                                          'cryptofresh' => 'OPEN.ETH'
                                                    ),
                                    'ltc' => array(
                                          'cryptopia' => 'ETH/LTC'
                                                    ),
                                    'usdt' => array(
                                          'poloniex' => 'USDT_ETH',
                                          'bittrex' => 'USDT-ETH',
                                          'upbit' => 'USDT-ETH',
                                        	'binance' => 'ETHUSDT',
                                          'hitbtc' => 'ETHUSD',
                                          'liqui' => 'eth_usdt',
                                          'okex' => 'eth_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // DCR
                    'DCR' => array(
                        
                        'coin_name' => 'Decred',
                        'coin_symbol' => 'DCR',
                        'coinmarketcap' => 'decred',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_DCR',
                                          'bittrex' => 'BTC-DCR',
                                          'upbit' => 'BTC-DCR',
                                          'cryptopia' => 'DCR/BTC'
                                                    ),
                                    'usdt' => array(
                                          'cryptopia' => 'DCR/USDT'
                                          			)
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // DASH
                    'DASH' => array(
                        
                        'coin_name' => 'Dash',
                        'coin_symbol' => 'DASH',
                        'coinmarketcap' => 'dash',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                        'poloniex' => 'BTC_DASH',
                                        'bittrex' => 'BTC-DASH',
                                        'upbit' => 'BTC-DASH',
                                        'kraken' => 'DASHXBT',
                                        'bitfinex' => 'tDSHBTC',
                                        'binance' => 'DASHBTC',
                                        'hitbtc' => 'DASHBTC',
                                        'kucoin' => 'DASH-BTC',
                                        'okex' => 'dash_btc',
                                        'livecoin' => 'DASH/BTC',
                                        'cryptopia' => 'DASH/BTC',
                                        'liqui' => 'dash_btc',
                                        'bter' => 'dash_btc',
                                        'tradesatoshi' => 'DASH_BTC'
                                                    ),
												'xmr' => array(
													  'poloniex' => 'XMR_DASH'
                                                    ),
                                    'eth' => array(
                                         'bittrex' => 'ETH-DASH',
                                         'upbit' => 'ETH-DASH',
                                         'binance' => 'DASHETH',
                                         'hitbtc' => 'DASHETH',
                                         'kucoin' => 'DASH-ETH',
                                         'okex' => 'dash_eth',
                                         'liqui' => 'dash_eth'
                                                    ),
                                    'usdt' => array(
                                         'poloniex' => 'USDT_DASH',
                                         'bittrex' => 'USDT-DASH',
                                         'upbit' => 'USDT-DASH',
                                         'cryptopia' => 'DASH/USDT'
                                          			)
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // LTC
                    'LTC' => array(
                        
                        'coin_name' => 'Litecoin',
                        'coin_symbol' => 'LTC',
                        'coinmarketcap' => 'litecoin',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                        'coinbase' => 'LTC',
                                        'okex' => 'ltc_btc',
                                        'bitfinex' => 'tLTCBTC',
                                        'poloniex' => 'BTC_LTC',
                                        'bittrex' => 'BTC-LTC',
                                        'upbit' => 'BTC-LTC',
                                        'kraken' => 'XLTCXXBT',
                                        'hitbtc' => 'LTCBTC',
                                        'bitstamp' => 'ltcbtc',
                                        'binance' => 'LTCBTC',
                                        'kucoin' => 'LTC-BTC',
                                        'livecoin' => 'LTC/BTC',
                                        'cryptopia' => 'LTC/BTC',
                                        'liqui' => 'ltc_btc',
                                        'bter' => 'ltc_btc',
                                        'cryptofresh' => 'OPEN.LTC',
                                        'tradesatoshi' => 'LTC_BTC'
                                                    ),
                                    'xmr' => array(
                                        'poloniex' => 'XMR_LTC'
                                                    ),
                                    'eth' => array(
                                    	 'okex' => 'ltc_eth',
                                        'bittrex' => 'ETH-LTC',
                                        'upbit' => 'ETH-LTC',
                                        'binance' => 'LTCETH',
                                        'hitbtc' => 'LTCETH',
                                        'kucoin' => 'LTC-ETH',
                                        'liqui' => 'ltc_eth'
                                                    ),
                                    'usdt' => array(
                                        'poloniex' => 'USDT_LTC',
                                        'bittrex' => 'USDT-LTC',
                                        'upbit' => 'USDT-LTC',
                                        'okex' => 'ltc_usdt',
                                        'binance' => 'LTCUSDT',
                                        'hitbtc' => 'LTCUSD',
                                        'kucoin' => 'LTC-USDT',
                                        'cryptopia' => 'LTC/USDT',
                                        'liqui' => 'ltc_usdt'
                                          			)
                                        ),
                        'default_pairing' => 'btc'
                    ),
                    // TUSD
                    'TUSD' => array(
                        
                        'coin_name' => 'True USD',
                        'coin_symbol' => 'TUSD',
                        'coinmarketcap' => 'true-usd',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                        'bittrex' => 'BTC-TUSD',
                                        'upbit' => 'BTC-TUSD'
                                                    ),
                                    'eth' => array(
                                        'bittrex' => 'ETH-TUSD',
                                        'upbit' => 'ETH-TUSD'
                                                    ),
                                    'usdt' => array(
                                        'bittrex' => 'USDT-TUSD'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                    ),
                    // STEEM
                    'STEEM' => array(
                        
                        'coin_name' => 'Steem',
                        'coin_symbol' => 'STEEM',
                        'coinmarketcap' => 'steem',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_STEEM',
                                          'bittrex' => 'BTC-STEEM',
                                          'upbit' => 'BTC-STEEM',
                                        	'binance' => 'STEEMBTC',
                                          'hitbtc' => 'STEEMBTC',
                                          'livecoin' => 'STEEM/BTC',
                                          'cryptofresh' => 'OPEN.STEEM'
                                                    ),
                                    'eth' => array(
                                          'poloniex' => 'ETH_STEEM',
                                        	'binance' => 'STEEMETH'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // FCT
                    'FCT' => array(
                        
                        'coin_name' => 'Factom',
                        'coin_symbol' => 'FCT',
                        'coinmarketcap' => 'factom',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_FCT',
                                          'bittrex' => 'BTC-FCT',
                                        	'upbit' => 'BTC-FCT',
                                          'cryptopia' => 'FCT/BTC'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-FCT',
                                        	'upbit' => 'ETH-FCT'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // XLM
                    'XLM' => array(
                        
                        'coin_name' => 'Stellar',
                        'coin_symbol' => 'XLM',
                        'coinmarketcap' => 'stellar',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_STR',
                                          'bittrex' => 'BTC-XLM',
                                          'upbit' => 'BTC-XLM',
                                        	'binance' => 'XLMBTC',
                                          'hitbtc' => 'XLMBTC',
                                          'kraken' => 'XXLMXXBT',
                                        	'okex' => 'xlm_btc'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-XLM',
                                          'upbit' => 'ETH-XLM',
                                          'binance' => 'XLMETH',
                                          'hitbtc' => 'XLMETH',
                                        	'okex' => 'xlm_eth'
                                                    ),
                                    'usdt' => array(
                                        	'poloniex' => 'USDT_STR',
                                          'hitbtc' => 'XLMUSD',
                                        	'okex' => 'xlm_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // VTC
                    'VTC' => array(
                        
                        'coin_name' => 'Vertcoin',
                        'coin_symbol' => 'VTC',
                        'coinmarketcap' => 'vertcoin',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_VTC',
                                          'bittrex' => 'BTC-VTC',
                                        	'upbit' => 'BTC-VTC'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // ANT
                    'ANT' => array(
                        
                        'coin_name' => 'Aragon',
                        'coin_symbol' => 'ANT',
                        'coinmarketcap' => 'aragon',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'bittrex' => 'BTC-ANT',
                                        	'upbit' => 'BTC-ANT',
                                          'hitbtc' => 'ANTBTC',
                                          'liqui' => 'ant_btc'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-ANT',
                                          'upbit' => 'ETH-ANT',
                                          'liqui' => 'ant_eth'
                                                    ),
                                    'usdt' => array(
                                        	'liqui' => 'ant_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // ZRX
                    'ZRX' => array(
                        
                        'coin_name' => 'oxProject',
                        'coin_symbol' => 'ZRX',
                        'coinmarketcap' => '0x',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_ZRX',
                                          'bittrex' => 'BTC-ZRX',
                                        	'upbit' => 'BTC-ZRX',
                                        	'ethfinex' => 'tZRXBTC',
                                          'hitbtc' => 'ZRXBTC',
                                        	'binance' => 'ZRXBTC',
                                          'liqui' => 'zrx_btc',
                                          'livecoin' => 'ZRX/BTC',
                                          'gatecoin' => 'ZRXBTC',
                                          'bter' => 'zrx_btc'
                                                    ),
                                    'eth' => array(
                                          'poloniex' => 'ETH_ZRX',
                                          'bittrex' => 'ETH-ZRX',
                                          'upbit' => 'ETH-ZRX',
                                        	'ethfinex' => 'tZRXETH',
                                          'hitbtc' => 'ZRXETH',
                                        	'binance' => 'ZRXETH',
                                          'liqui' => 'zrx_eth',
                                          'livecoin' => 'ZRX/ETH',
                                          'gatecoin' => 'ZRXETH',
                                        	'okex' => 'zrx_eth'
                                                    ),
                                    'usdt' => array(
                                          'hitbtc' => 'ZRXUSD',
                                        	'liqui' => 'zrx_usdt',
                                        	'okex' => 'zrx_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // MANA
                    'MANA' => array(
                        
                        'coin_name' => 'Decentraland',
                        'coin_symbol' => 'MANA',
                        'coinmarketcap' => 'decentraland',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'bittrex' => 'BTC-MANA',
                                        	'upbit' => 'BTC-MANA',
                                        	'binance' => 'MANABTC',
                                        	'ethfinex' => 'tMNABTC',
                                          'liqui' => 'mana_btc',
                                          'gatecoin' => 'MANBTC',
                                          'okex' => 'mana_btc'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-MANA',
                                        	'upbit' => 'ETH-MANA',
                                        	'binance' => 'MANAETH',
                                          'hitbtc' => 'MANAETH',
                                        	'ethfinex' => 'tMNAETH',
                                          'liqui' => 'mana_eth',
                                          'gatecoin' => 'MANETH',
                                          'okex' => 'mana_eth'
                                                    ),
                                    'usdt' => array(
                                          'liqui' => 'mana_usdt',
                                          'hitbtc' => 'MANAUSD',
                                          'okex' => 'mana_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // DGD
                    'DGD' => array(
                        
                        'coin_name' => 'DigixDAO',
                        'coin_symbol' => 'DGD',
                        'coinmarketcap' => 'digixdao',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                        'binance' => 'DGDBTC',
                                        'liqui' => 'dgd_btc',
                                        'hitbtc' => 'DGDBTC',
                                        'livecoin' => 'DGD/BTC',
                                        'okex' => 'dgd_btc'
                                                    ),
                                    'eth' => array(
                                        'binance' => 'DGDETH',
                                        'liqui' => 'dgd_eth',
                                        'livecoin' => 'DGD/ETH',
                                        'okex' => 'dgd_eth'
                                                    ),
                                    'usdt' => array(
                                        'liqui' => 'dgd_usdt',
                                        'okex' => 'dgd_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                    ),
                    // SNT
                    'SNT' => array(
                        
                        'coin_name' => 'Status',
                        'coin_symbol' => 'SNT',
                        'coinmarketcap' => 'status',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'bittrex' => 'BTC-SNT',
                                          'upbit' => 'BTC-SNT',
                                        	'ethfinex' => 'tSNTBTC',
                                          'hitbtc' => 'SNTBTC',
                                        	'binance' => 'SNTBTC',
                                          'gatecoin' => 'SNTBTC',
                                          'liqui' => 'snt_btc',
                                        	'kucoin' => 'SNT-BTC',
                                        	'livecoin' => 'SNT/BTC',
                                       	'okex' => 'snt_btc',
                                          'bter' => 'snt_btc'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-SNT',
                                          'upbit' => 'ETH-SNT',
                                        	'ethfinex' => 'tSNTETH',
                                          'hitbtc' => 'SNTETH',
                                          'binance' => 'SNTETH',
                                          'liqui' => 'snt_eth',
                                        	'kucoin' => 'SNT-ETH',
                                        	'livecoin' => 'SNT/ETH',
                                          'gatecoin' => 'SNTETH',
                                        	'okex' => 'snt_eth'
                                                    ),
                                    'usdt' => array(
                                          'hitbtc' => 'SNTUSD',
                                        	'liqui' => 'snt_usdt',
                                        	'okex' => 'snt_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // GNT
                    'GNT' => array(
                        
                        'coin_name' => 'Golem',
                        'coin_symbol' => 'GNT',
                        'coinmarketcap' => 'golem-network-tokens',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_GNT',
                                          'bittrex' => 'BTC-GNT',
                                        	'upbit' => 'BTC-GNT',
                                        	'ethfinex' => 'tGNTBTC',
                                          'liqui' => 'gnt_btc',
                                        	'livecoin' => 'GNT/BTC',
                                          'cryptopia' => 'GNT/BTC',
                                        	'okex' => 'gnt_btc'
                                                    ),
                                    'eth' => array(
                                          'poloniex' => 'ETH_GNT',
                                          'bittrex' => 'ETH-GNT',
                                          'upbit' => 'ETH-GNT',
                                        	'ethfinex' => 'tGNTETH',
                                          'liqui' => 'gnt_eth',
                                        	'livecoin' => 'GNT/ETH',
                                        	'okex' => 'gnt_eth'
                                                    ),
                                    'usdt' => array(
                                        	'liqui' => 'gnt_usdt',
                                        	'okex' => 'gnt_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // ADA
                    'ADA' => array(
                        
                        'coin_name' => 'Cardano',
                        'coin_symbol' => 'ADA',
                        'coinmarketcap' => 'cardano',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                        'bittrex' => 'BTC-ADA',
                                        'upbit' => 'BTC-ADA',
                                        'hitbtc' => 'ADABTC',
                                        'binance' => 'ADABTC'
                                                    ),
                                    'eth' => array(
                                        'bittrex' => 'ETH-ADA',
                                        'upbit' => 'ETH-ADA',
                                        'hitbtc' => 'ADAETH',
                                        'binance' => 'ADAETH'
                                                    ),
                                    'usdt' => array(
                                        'bittrex' => 'USDT-ADA',
                                        'hitbtc' => 'ADAUSD'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                    ),	
                    // ZEN	
                    'ZEN' => array(	
                        	
                        'coin_name' => 'ZenCash',	
                        'coin_symbol' => 'ZEN',	
                        'coinmarketcap' => 'zencash',	
                         'ico' => 'no',
                        'market_pairing' => array(	
                                    'btc' => array(	
                                        'bittrex' => 'BTC-ZEN',
                                        'upbit' => 'BTC-ZEN',
                                        'okex' => 'zen_btc',
                                        'cryptopia' => 'ZEN/BTC'	
                                                    ),
                                    'eth' => array(	
                                        'okex' => 'zen_eth'	
                                                    ),
                                    'ltc' => array(	
                                        'cryptopia' => 'ZEN/LTC'	
                                                    ),
                                    'usdt' => array(	
                                        'okex' => 'zen_usdt'	
                                                    )	
                                        ),	
                        'default_pairing' => 'btc'	
                        	
                    ),
                    // DATA
                    'DATA' => array(
                        
                        'coin_name' => 'Streamr DATAcoin',
                        'coin_symbol' => 'DATA',
                        'coinmarketcap' => 'streamr-datacoin',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                        'hitbtc' => 'DATABTC',
                                        'ethfinex' => 'tDATBTC'
                                                    ),
                                    'eth' => array(
                                        'hitbtc' => 'DATAETH',
                                        'ethfinex' => 'tDATETH'
                                                    ),
                                    'usdt' => array(
                                        'hitbtc' => 'DATAUSD'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                    ),
                    // BTS
                    'BTS' => array(
                        
                        'coin_name' => 'BitShares',
                        'coin_symbol' => 'BTS',
                        'coinmarketcap' => 'bitshares',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_BTS',
                                        	'binance' => 'BTSBTC',
                                          'livecoin' => 'BTS/BTC',
                                          'bter' => 'bts_btc',
                                          'cryptofresh' => 'BTS'
                                                    ),
                                    'eth' => array(
                                        	'binance' => 'BTSETH'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // XRP
                    'XRP' => array(
                        
                        'coin_name' => 'Ripple',
                        'coin_symbol' => 'XRP',
                        'coinmarketcap' => 'ripple',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'poloniex' => 'BTC_XRP',
                                          'bittrex' => 'BTC-XRP',
                                          'upbit' => 'BTC-XRP',
                                        	'binance' => 'XRPBTC',
                                          'kraken' => 'XXRPXXBT',
                                          'bitfinex' => 'tXRPBTC',
                                          'bitstamp' => 'xrpbtc',
                                        	'hitbtc' => 'XRPBTC'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-XRP',
                                          'upbit' => 'ETH-XRP',
                                        	'binance' => 'XRPETH',
                                        	'hitbtc' => 'XRPETH',
                                        	'okex' => 'xrp_eth'
                                                    ),
                                    'usdt' => array(
                                        	'poloniex' => 'USDT_XRP',
                                          'bittrex' => 'USDT-XRP',
                                          'upbit' => 'USDT-XRP',
                                        	'hitbtc' => 'XRPUSDT'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),
                    // DNT
                    'DNT' => array(
                        
                        'coin_name' => 'District0x',
                        'coin_symbol' => 'DNT',
                        'coinmarketcap' => 'district0x',
                        'ico' => 'yes',
                        'market_pairing' => array(
                                    'btc' => array(
                                          'bittrex' => 'BTC-DNT',
                                        	'upbit' => 'BTC-DNT',
                                        	'binance' => 'DNTBTC',
                                          'liqui' => 'dnt_btc',
                                          'hitbtc' => 'DNTBTC',
                                          'bter' => 'dnt_btc',
                                        	'okex' => 'dnt_btc'
                                                    ),
                                    'eth' => array(
                                          'bittrex' => 'ETH-DNT',
                                          'upbit' => 'ETH-DNT',
                                          'binance' => 'DNTETH',
                                          'liqui' => 'dnt_eth',
                                        	'okex' => 'dnt_eth'
                                                    ),
                                    'usdt' => array(
                                        	'liqui' => 'dnt_usdt',
                                        	'okex' => 'dnt_usdt'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                        
                    ),	
                    // SWT	
                    'SWT' => array(	
                        	
                        'coin_name' => 'Swarm City',	
                        'coin_symbol' => 'SWT',	
                        'coinmarketcap' => 'swarm-city',
                         'ico' => 'yes',	
                        'market_pairing' => array(	
                                    'btc' => array(	
                                          'bittrex' => 'BTC-SWT',	
                                        	'upbit' => 'BTC-SWT',	
                                          'hitbtc' => 'SWTBTC'	
                                                    ),
                                    'eth' => array(
                                          'hitbtc' => 'SWTETH'
                                                    )
                                        ),	
                        'default_pairing' => 'btc'	
                        	
                    ),	
                    // POA	
                    'POA' => array(	
                        	
                        'coin_name' => 'POA Network',	
                        'coin_symbol' => 'POA',	
                        'coinmarketcap' => 'poa-network',	
                         'ico' => 'yes',
                        'market_pairing' => array(	
                                    'btc' => array(	
                                          'binance' => 'POABTC'	
                                                    ),	
                                    'eth' => array(	
                                          'binance' => 'POAETH'	
                                                    )	
                                        ),	
                        'default_pairing' => 'eth'	
                        	
                    ),	
                    // MYST	
                    'MYST' => array(	
                        	
                        'coin_name' => 'Mysterium',	
                        'coin_symbol' => 'MYST',	
                        'coinmarketcap' => 'mysterium',	
                         'ico' => 'yes',
                        'market_pairing' => array(	
                                    'btc' => array(	
                                          'liqui' => 'myst_btc'	
                                                    ),
                                    'eth' => array(	
                                          'liqui' => 'myst_eth'	
                                                    ),
                                    'usdt' => array(	
                                          'liqui' => 'myst_usdt'	
                                                    )	
                                        ),	
                        'default_pairing' => 'eth'	
                        	
                    ),	
                    // PIRL	
                    'PIRL' => array(	
                        	
                        'coin_name' => 'Pirl',	
                        'coin_symbol' => 'PIRL',	
                        'coinmarketcap' => 'pirl',	
                         'ico' => 'no',
                        'market_pairing' => array(	
                                    'btc' => array(	
                                          'cryptopia' => 'PIRL/BTC'	
                                                    )
                                        ),	
                        'default_pairing' => 'btc'	
                        	
                    ),
                    // RVN
                    'RVN' => array(
                        
                        'coin_name' => 'Ravencoin',
                        'coin_symbol' => 'RVN',
                        'coinmarketcap' => 'ravencoin',
                        'ico' => 'no',
                        'market_pairing' => array(
                                    'btc' => array(
                                         'cryptofresh' => 'BRIDGE.RVN'
                                                    )
                                        ),
                        'default_pairing' => 'btc'
                    )
                
                
);

/////////////////// COIN MARKETS CONFIG -END- ////////////////////////////////////////////////////



?>