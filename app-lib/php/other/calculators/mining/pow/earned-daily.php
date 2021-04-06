<?php
/*
 * Copyright 2014-2020 GPLv3, Open Crypto Portfolio Tracker by Mike Kilday: http://DragonFrugal.com
 */
	

?>				
				<br />
				<br />
				<b>Current <?=$pow_coin_data['name']?> Value Per Coin:</b> 
				
				<?php
				$val_per_coin = round( $mined_coin_val * $sel_btc_prim_curr_val , 8);
				
				$val_per_coin = ( $pt_var->num_to_str($val_per_coin) >= $pt_conf['gen']['prim_curr_dec_max_thres'] ? round($val_per_coin, 2) : round($val_per_coin, $pt_conf['gen']['prim_curr_dec_max']) );
				
				echo ( $pow_coin_data['symbol'] == 'btc' ? number_format($sel_btc_prim_curr_val, 2) . ' ' . strtoupper($pt_conf['gen']['btc_prim_curr_pairing']) : number_format($mined_coin_val, 8) . ' BTC (' . $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . $val_per_coin . ' '.strtoupper($pt_conf['gen']['btc_prim_curr_pairing']).')' );
				?>
				
				<br />
				<br />
				 ###################################################
				<br />
				<br />
				
				<?php
				if ( $pow_coin_data['symbol'] != 'btc' ) {
				?>
				<b>Average <?=strtoupper($pow_coin_data['symbol'])?> Earned Daily (block reward only):</b> 
				
				
				<?php
				echo number_format( $daily_avg , 8) . ' ' . strtoupper($pow_coin_data['symbol']);
				?>
				
				<br />
				<br />
				<?php
				}
				?>
				
				<b>Average BTC Value Earned Daily:</b> 
				
				<?php
				$prim_curr_daily_avg_raw = ( $pt_var->num_to_str($prim_curr_daily_avg_raw) >= $pt_conf['gen']['prim_curr_dec_max_thres'] ? round($prim_curr_daily_avg_raw, 2) : round($prim_curr_daily_avg_raw, $pt_conf['gen']['prim_curr_dec_max']) );
				
				echo number_format( $btc_daily_avg_raw, 8 ) . ' BTC (' . $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . $prim_curr_daily_avg_raw . ' '.strtoupper($pt_conf['gen']['btc_prim_curr_pairing']).')';
				?>
				
				<br />
				<br />
				
				<span class='red'><b>Power Cost Daily:</b> 
				
				<?php
				echo $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format($kwh_cost_daily, 2);
				?>
				
				</span> 
				
				<br />
				<br />
				
				<span class='red'><b>Pool Fee Daily:</b> 
				
				<?php
				echo $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format($pool_fee_daily, 2);
				?>
				
				</span> 
				
				<br />
				<br />
				
				<?php
				
				$mining_daily_profit = $pt_var->num_to_str($prim_curr_daily_avg_raw - $kwh_cost_daily - $pool_fee_daily); // Better decimal support
				
				if ( $mining_daily_profit >= 0 ) {
				$mining_daily_profit_span = 'green';
				}
				else {
				$mining_daily_profit_span = 'red';
				}
				
				?>
				
				<b><span class="<?=$mining_daily_profit_span?>">Daily Profit:</span></b> 
				
				<?php
				echo '<span class="'.$mining_daily_profit_span.'">' . $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format($mining_daily_profit, 2) . '</span>';
				?>
				
				<br />
				<br />
				 ###################################################
				<br />
				<br />
				
				<?php
				if ( $pow_coin_data['symbol'] != 'btc' ) {
				?>
				<b>Average <?=strtoupper($pow_coin_data['symbol'])?> Earned Weekly (block reward only):</b> 
				
				
				<?php
				echo number_format( $daily_avg * 7 , 8) . ' ' . strtoupper($pow_coin_data['symbol']);
				?>
				
				<br />
				<br />
				<?php
				}
				?>
				
				<b>Average BTC Value Earned Weekly:</b> 
				
				<?php
				echo number_format( $btc_daily_avg_raw * 7 , 8) . ' BTC (' . $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format( $prim_curr_daily_avg_raw * 7 , 2) . ' '.strtoupper($pt_conf['gen']['btc_prim_curr_pairing']).')';
				?>
				
				<br />
				<br />
				
				<span class='red'><b>Power Cost Weekly:</b> 
				
				<?php
				echo $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format($kwh_cost_daily * 7, 2);
				?>
				
				</span>
				
				<br />
				<br />
				
				<span class='red'><b>Pool Fee Weekly:</b> 
				
				<?php
				echo $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format($pool_fee_daily * 7, 2);
				?>
				
				</span>
				
				<br />
				<br />
				
				<b><span class="<?=$mining_daily_profit_span?>">Weekly Profit:</span></b> 
				
				<?php
				echo '<span class="'.$mining_daily_profit_span.'">' . $pt_conf['power']['btc_curr_markets'][$pt_conf['gen']['btc_prim_curr_pairing']] . number_format( ($mining_daily_profit * 7) , 2) . '</span>';
				?>
				
				<br />
				<br />
				 ###################################################
				<br />
				<br />
				
		