
				<?php
				
				echo '<p><b>Block height:</b> ' . number_format(hexdec(etherscan_api('number'))) . '</p>';
				echo '<p><b>Gas limit:</b> ' . number_format(hexdec(etherscan_api('gasLimit'))) . '</p>';
				
				
				if ( $_POST['eth_submitted'] ) {
				    
				$_POST['eth_difficulty'] = str_replace("    ", '', $_POST['eth_difficulty']);
				$_POST['eth_difficulty'] = str_replace(" ", '', $_POST['eth_difficulty']);
				$_POST['eth_difficulty'] = str_replace(",", '', $_POST['eth_difficulty']);
				
				$miner_eth_hashrate = ( trim($_POST['eth_your_hashrate']) * trim($_POST['eth_measure']) );
				
				$time = ( trim($_POST['eth_difficulty']) / $miner_eth_hashrate );
				
				$minutes = ( $time / 60 );
				
				$hours = ( $time / 3600 );
				
				$days = ( $hours / 24 );
				
				$months = ( $days / 30 );
				
				$years = ( $days / 365 );
				    
				    //echo '<p>'.$scale;
				    //echo '<p>'.$time;
				?>
				<p style='color: green;'>
				<?php
				    if ( $minutes < 60 ) {
				    ?>
				    Minutes until block found: 
				    <?php
				    echo round($minutes, 2);
				    }
				
				    elseif ( $hours < 24 ) {
				    ?>
				    Hours until block found: 
				    <?php
				    echo round($hours, 2);
				    }
				
				    elseif ( $days < 30 ) {
				    ?>
				    Days until block found: 
				    <?php
				    echo round($days, 2);
				    }
				
				    elseif ( $days < 365 ) {
				    ?>
				    Months until block found: 
				    <?php
				    echo round($months, 2);
				    }
				    
				    else {
				    ?>
				    Years until block found: 
				    <?php
				    echo round($years, 2);
				    }
				
				$caculate_daily = ( 24 / $hours );
				$daily_average = ( $caculate_daily * ( get_trade_price('poloniex', 'BTC_ETH') * trim($_POST['eth_block_reward']) ) );
				?>
				<br />
				<br />
				Current Ethereum Value Per Coin: 
				<?php
				echo round(get_trade_price('poloniex', 'BTC_ETH'), 8) . ' BTC ($' . round(( round(get_trade_price('poloniex', 'BTC_ETH'), 8) * get_btc_usd($btc_in_usd) ), 8) . ' USD)';
				?>
				<br />
				<br />
				Average ETH Earned Daily (block reward only): 
				<?php
				echo number_format( round(( round($daily_average, 8) / get_trade_price('poloniex', 'BTC_ETH') ), 8) , 8) . ' ETH';
				?>
				<br />
				<br />
				Average BTC Value Earned Daily: 
				<?php
				echo number_format(round($daily_average, 8), 8) . ' BTC ($' . round(( round($daily_average, 8) * get_btc_usd($btc_in_usd) ), 2) . ' USD)';
				}
				?>
				</p>
				<form name='eth' action='index.php#calculators' method='post'>
				    
				    <input type='hidden' value='1' name='eth_submitted' />
				
				<p><b>Difficulty:</b> <input type='text' value='<?=( $_POST['eth_difficulty'] ? number_format($_POST['eth_difficulty']) : number_format(hexdec(etherscan_api('difficulty'))) )?>' name='eth_difficulty' /> (uses <a href='https://etherscan.io/apis/' target='_blank'>etherscan.io/apis</a>)</p>
				
				
				<p><b>Your Hashrate:</b> <input type='text' value='<?=$_POST['eth_your_hashrate']?>' name='eth_your_hashrate' />
				
				<select name='eth_measure'>
				<option value='1000000' <?=( $_POST['eth_measure'] == '1000000' ? 'selected' : '' )?>> Mhs </option>
				<option value='1000' <?=( $_POST['eth_measure'] == '1000' ? 'selected' : '' )?>> Khs </option>
				</select>
				</p>
				
				<p><b>Block Reward:</b> <input type='text' value='<?=( $_POST['eth_block_reward'] ? $_POST['eth_block_reward'] : $mining_rewards['ethereum'] )?>' name='eth_block_reward' /> (static from config.php file, verify current block reward manually)</p>
				
				<input type='submit' value='Calculate ETH Mining Profit' />
				
	
				</form>
				