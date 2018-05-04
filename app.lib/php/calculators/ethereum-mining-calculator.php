<?php
/*
 * Copyright 2014-2018 GPLv3, DFD Cryptocoin Values by Mike Kilday: http://DragonFrugal.com
 */
			
			// Coin information, to dynamically populate general sections
			$calculation_form_data = array(
											'Ethereum', // Coin name
											'eth', // Coin symbol
											etherscan_api('number'), // Block height
											etherscan_api('difficulty'), // Mining difficulty
											'https://etherscan.io/apis/', // Blockchain data API url
											'etherscan.io/apis', // Blockchain data API name
											'poloniex', // Exchange name (lowercase for API logic)
											'BTC_ETH' // Market pair name
											);
			
			
			///////////////////////////////////////////////////////////////////////////
			
			echo '<p><b>Block height:</b> ' . number_format(hexdec($calculation_form_data[2])) . '</p>';
			echo '<p><b>Gas limit:</b> ' . number_format(hexdec(etherscan_api('gasLimit'))) . '</p>'; // Custom for this Ethereum mining calculator
				
			// Start form submission results
			if ( $_POST[$calculation_form_data[1].'_submitted'] ) {
				    
				include('results/post.data.processing.php'); // Generalized module
				
				
			///////////////////////////////////////////////////////////////////////////
			
				// Difficulty calculation for this coin...MAY BE DIFFERENT PER COIN
				$time = ( trim($_POST['difficulty']) / $miner_hashrate );
				
			///////////////////////////////////////////////////////////////////////////
			
				include('results/time.calculation.php'); // Generalized module
				include('results/profit.calculation.php'); // Generalized module
				include('results/earned.daily.php'); // Generalized module
				
			}
			// End form submission results
				
				mining_calc_form($calculation_form_data); // Generalized module
				
			///////////////////////////////////////////////////////////////////////////
			
			?>