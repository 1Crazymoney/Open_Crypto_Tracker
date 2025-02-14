<?php
/*
 * Copyright 2014-2022 GPLv3, Open Crypto Tracker by Mike Kilday: Mike@DragonFrugal.com
 */

?>

<div class='full_width_wrapper'>
	
	
				<span class='red countdown_notice'></span>
			
	
	<p style='margin-top: 15px; margin-bottom: 15px;'><?=$ct_gen->start_page_html('charts')?></p>		
			
	  
	<p style='margin-top: 25px;'><a style='font-weight: bold;' class='red' href='javascript: show_more("chartsnotice");' title='Click to show notices about how charts run within this app.'><b>Charts Notices / Information</b></a></p>
		
		
	<div id='chartsnotice' style='display: none;' class='align_left'>
		
		<?php
		$supported_prim_currency_count = 0;
		foreach ( $ct_conf['power']['btc_currency_mrkts'] as $key => $unused ) {
		$supported_prim_currency_list .= strtoupper($key) . ' / ';
		$supported_prim_currency_count = $supported_prim_currency_count + 1;
		}
		$supported_prim_currency_list = trim($supported_prim_currency_list);
		$supported_prim_currency_list = rtrim($supported_prim_currency_list,'/');
		$supported_prim_currency_list = trim($supported_prim_currency_list);
		
		foreach ( $ct_conf['assets']['BTC']['pair'][$default_btc_prim_currency_pair] as $key => $unused ) {
		
			if( stristr($key, 'bitmex_') == false ) { // Futures markets not allowed
			$supported_exchange_list .= $ct_gen->key_to_name($key) . ' / ';
			}
			
		}
		$supported_exchange_list = trim($supported_exchange_list);
		$supported_exchange_list = rtrim($supported_exchange_list,'/');
		$supported_exchange_list = trim($supported_exchange_list);
		?>
					
		<p class='bitcoin' style='font-weight: bold;'><span class='red'>Did you just install this app?</span> If you would like to bootstrap the demo price chart data (get many months of spot price data already pre-populated), <a href='https://github.com/taoteh1221/bootstrapping/raw/main/bootstrap-price-charts-data.zip' target='_blank'>download it from github</a>.</p>
		 
		<p class='bitcoin' style='font-weight: bold;'>Charts are only available to show for each asset properly configured in the Admin Config CHARTS AND ALERTS section. Charts (and price alerts) must be <a href='README.txt' target='_blank'>setup as a cron job on your web server</a> (if you are running the "Server Edition"), or <i>they will not work</i>. The chart's tab / page, and chart data caching can be disabled in the Admin Config GENERAL section, if you choose to not setup a cron job.</p>
		 
		<p class='bitcoin' style='font-weight: bold;'>A few crypto exchanges only provide asset volume data (with no pair volume data included). If 24 hour pair volume is NOT available for a market, it will be emulated via the asset volume multiplied by the <i>current</i> asset market value (which gives us the rough pair volume for a better chart user experience).</p>
					
		<p class='black' style='font-weight: bold;'>The administrator has set the <i>price charts primary currency market</i> (in the Admin Config GENERAL section) to: <span class='bitcoin'><?=strtoupper($default_btc_prim_currency_pair)?> @ <?=$ct_gen->key_to_name($default_btc_prim_exchange)?></span> &nbsp;(enables <i>additional</i> "<?=strtoupper($default_btc_prim_currency_pair)?> Value" charts)</p>
		
		<p class='black' style='font-weight: bold;'><?=strtoupper($default_btc_prim_currency_pair)?>-paired BTC exchanges supported in this app are: <br /><span class='bitcoin'><?=$supported_exchange_list?></span></p>
		
		<p class='black' style='font-weight: bold;'><?=$supported_prim_currency_count?> primary currency pairs are supported for conversion charts (in the Admin Config GENERAL section, using the "btc_prim_currency_pair" setting):<br /> <span class='bitcoin'><?=$supported_prim_currency_list?></span> </p>
		 
		<p class='red' style='font-weight: bold;'>!NOT! ALL EXCHANGES SUPPORT ALL CURRENCY PAIRS, double check any setting changes you make (and check the error log at /cache/logs/error.log for any reported issues).</p>
		 
		<p class='red' style='font-weight: bold;'>v4.03.0 and higher charts are NOT backwards-compatible, as the 24 hour volume format was completely changed over to always be based off pair volume data only (24 hour asset volume is no longer supported).</p>
	
	</div>
	
    		
   <ul style='margin-top: 25px; font-weight: bold;'>
	
        <?php
        if ( $app_edition == 'desktop' ) {
        ?>
	<li class='red' style='font-weight: bold;'>Using 'zoom' (top right) can skew chart hovering mouse positions.</li>	
        <?php
        }
        ?>
        
	<li class='bitcoin' style='font-weight: bold;'>Setting this page as the 'start page' (top left) will save your vertical scroll position during reloads.</li>	
	
	<li class='bitcoin' style='font-weight: bold;'>If your web browser freezes on this page for a long time, try selecting fewer charts.</li>	
	
	<li class='bitcoin' style='font-weight: bold;'>Charts may take awhile to update with the latest data.</li>	
   
   </ul>
   
				
	<p style='margin-top: 25px;'><button class="show_chart_settings force_button_style">Select Charts</button></p>
	
	<br clear='all' />
	
	
	<div id="show_chart_settings">
	
		
		<h4 style='display: inline;'>Select Charts</h4>
	
				<span style='z-index: 99999;' class='red countdown_notice'></span>
	
	<br clear='all' />
	<br clear='all' />
	
	<p class='red'>*Charts are not activated by default to increase page loading speed / responsiveness. It's recommended to avoid activating too many charts at the same time, to keep your page load times quick.</p>
	
	<p class='red'>Low memory devices (Raspberry Pi / Pine64 / etc) MAY CRASH #IF YOU SHOW TOO MANY CHARTS#.
	     
		<img id='charts_raspi_crash' src='templates/interface/media/images/info-red.png' alt='' width='30' style='position: relative; left: -5px;' /> </p>
		
	 <script>
	 
			var charts_raspi_crash = '<h5 class="align_center red tooltip_title">Low Memory Devices Crashing</h5>'
			
			
			+'<p class="coin_info extra_margins" style="white-space: normal; max-width: 600px;">If your low memory device (Raspberry PI / Pine64 / etc) crashes when you select too many news feeds OR charts, you may need to restart your device, and then delete all cookies in your browser related to the web domain you run the app from (before using the app again).</p>'
			
			+'<p class="coin_info extra_margins" style="white-space: normal; max-width: 600px;">For the more technically-inclined, try decreasing "MaxRequestWorkers" in Apache\'s prefork configuration file (10 maximum is the best for low memory devices, AND "MaxSpareServers" above it MUST BE SET EXACTLY THE SAME #OR YOUR SYSTEM MAY STILL CRASH#), to help stop the web server from crashing under heavier loads. <span class="red">ALWAYS BACKUP THE CURRENT SETTINGS FIRST, IN CASE IT DOESN\'T WORK.</span></p>'
			
			
			+'<p> </p>';

	
		
			$('#charts_raspi_crash').balloon({
			html: true,
			position: "left",
  			classname: 'balloon-tooltips',
			contents: charts_raspi_crash,
			css: {
					fontSize: ".8rem",
					minWidth: "450px",
					padding: ".3rem .7rem",
					border: "2px solid rgba(212, 212, 212, .4)",
					borderRadius: "6px",
					boxShadow: "3px 3px 6px #555",
					color: "#eee",
					backgroundColor: "#111",
					opacity: "0.99",
					zIndex: "32767",
					textAlign: "left"
					}
			});
		
		 </script>
		 
	    </p>
	
	<p class='bitcoin'>You can enable "Use cookies to save data" on the Settings page <i>before activating your charts</i>, if you want them to stay activated between browser sessions.</p>
	
	
	<div> &nbsp; </div>
	
	<!-- Submit button must be OUTSIDE form tags here, or it submits the target form improperly and loses data -->
	<p><button class='force_button_style' onclick='
	$(".show_chart_settings").modaal("close");
	$("#coin_amnts").submit();
	'>Update Selected Charts</button></p>
	
	<div> &nbsp; </div>
	
	<p><input type='checkbox' onclick='
	
		selectAll(this, "activate_charts");
		
		if ( this.checked == false ) {
		$("#show_charts").val("");
		}
		
	' /> <b>Select / Unselect All</b> &nbsp;&nbsp; <span class='bitcoin'>(if "loading charts" notice freezes, check / uncheck this box, then click "Update Selected Charts")</span></p>
	
	<div> &nbsp; </div>
		
		<form id='activate_charts' name='activate_charts'>
		
	<?php
	
	$zebra_stripe = 'long_list_odd';
	foreach ( $ct_conf['charts_alerts']['tracked_mrkts'] as $key => $val ) {
		
		// Remove any duplicate asset array key formatting, which allows multiple alerts per asset with different exchanges / trading pairs (keyed like SYMB, SYMB-1, SYMB-2, etc)
		$show_asset = ( stristr($key, "-") == false ? $key : substr( $key, 0, mb_strpos($key, "-", 0, 'utf-8') ) );
		$show_asset = strtoupper($show_asset);
		
		$show_asset_params = explode("||", $val);
		
				
			// We also want to make sure this asset hasn't been removed from the 'assets' app config, for UX
			if ( $show_asset_params[2] == 'chart' && isset($ct_conf['assets'][strtoupper($show_asset)]) 
			|| $show_asset_params[2] == 'both' && isset($ct_conf['assets'][strtoupper($show_asset)]) ) {
	?>
	
		<div class='<?=$zebra_stripe?> long_list <?=( $last_rendered != $show_asset ? 'activate_chart_sections' : '' )?>'>
				
				<input type='checkbox' value='<?=$key?>_<?=$show_asset_params[1]?>' onchange='chart_toggle(this);' <?=( in_array("[".$key . '_' . $show_asset_params[1]."]", $sel_opt['show_charts']) ? 'checked' : '' )?> /> <span class='blue'><?=$show_asset?></span> / <?=strtoupper($show_asset_params[1])?> @ <?=$ct_gen->key_to_name($show_asset_params[0])?>
			
				<?php
				// Markets that are NOT the same as PRIMARY CURRENCY CONFIG get a secondary chart for PRIMARY CURRENCY CONFIG
				if ( $show_asset_params[1] != $default_btc_prim_currency_pair ) {
				?>
				
				 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type='checkbox' value='<?=$key?>_<?=$default_btc_prim_currency_pair?>' onchange='chart_toggle(this);' <?=( in_array("[".$key . '_' . $default_btc_prim_currency_pair."]", $sel_opt['show_charts']) ? 'checked' : '' )?> /> <?=strtoupper($default_btc_prim_currency_pair)?> Value
				
				<?php
				}
				?>
	
			</div>
				
	<?php
	    
		 		if ( $zebra_stripe == 'long_list_odd' ) {
			 	$zebra_stripe = 'long_list_even';
			 	}
			 	else {
			 	$zebra_stripe = 'long_list_odd';
			 	}
		 	
			$last_rendered = $show_asset;
		 	}
	
	}
	    
	?>
	<div class='long_list_end list_end_black'> &nbsp; </div>
	
		</form>
	
		<!-- Submit button must be OUTSIDE form tags here, or it submits the target form improperly and loses data -->
		<p><button class='force_button_style' onclick='
		$(".show_chart_settings").modaal("close");
		$("#coin_amnts").submit();
		'>Update Selected Charts</button></p>
		
	</div>
	
	
	<script>
	$('.show_chart_settings').modaal({
		content_source: '#show_chart_settings'
	});
	</script>
	
	
	
	<div class='red' id='charts_error'></div>
	
	
	<?php
	
	// Render the charts
	foreach ( $ct_conf['charts_alerts']['tracked_mrkts'] as $key => $val ) {
    
	// Remove any duplicate asset array key formatting, which allows multiple alerts per asset with different exchanges / trading pairs (keyed like SYMB, SYMB-1, SYMB-2, etc)
	$chart_asset = ( stristr($key, "-") == false ? $key : substr( $key, 0, mb_strpos($key, "-", 0, 'utf-8') ) );
	$chart_asset = strtoupper($chart_asset);
		
	$charts_available = 1;
	$alerts_mrkt_parse = explode("||", $val );	
		
		// We also want to make sure this asset hasn't been removed from the 'assets' app config, for UX
		if ( !isset($ct_conf['assets'][strtoupper($chart_asset)]) ) {
      continue;
    	}
		
		// Pairs chart
		if ( in_array('['.$key.'_'.$alerts_mrkt_parse[1].']', $sel_opt['show_charts']) ) {
		$charts_shown = 1;
	?>
	
	<div style='display: flex; flex-flow: column wrap; overflow: hidden;' class='chart_wrapper' id='<?=$key?>_<?=$alerts_mrkt_parse[1]?>_chart'>
	
	<span class='chart_loading' style='color: <?=$ct_conf['power']['charts_text']?>;'> &nbsp; Loading chart for <?=strtoupper($chart_asset)?> / <?=strtoupper($alerts_mrkt_parse[1])?> @ <?=$ct_gen->key_to_name($alerts_mrkt_parse[0])?>...</span>
	
	<div style='z-index: 99999; margin-top: 7px;' class='chart_reload align_center absolute_centered loading bitcoin'><img src="templates/interface/media/images/auto-preloaded/loader.gif" height='17' alt="" style='vertical-align: middle;' /> <div class='chart_reload_msg'></div></div>
		
	</div>
	
	
	<script>
	
	// Load AFTER page load, for quick interface loading
	$(document).ready(function(){
		
	<?php
	$chart_mode = 'pair';
	include('templates/interface/desktop/php/user/user-elements/asset-charts.php');
	?>
	
	});

	</script>
	
	
	<br/><br/><br/>
	
	<?php
		}
		
		// PRIMARY CURRENCY CONFIG chart
		if ( $alerts_mrkt_parse[1] != $default_btc_prim_currency_pair && in_array('['.$key.'_'.$default_btc_prim_currency_pair.']', $sel_opt['show_charts']) ) {
		$charts_shown = 1;
	?>
	
	<div style='display: flex; flex-flow: column wrap; overflow: hidden;' class='chart_wrapper' id='<?=$key?>_<?=strtolower($default_btc_prim_currency_pair)?>_chart'>
	
	<span class='chart_loading' style='color: <?=$ct_conf['power']['charts_text']?>;'> &nbsp; Loading chart for <?=strtoupper($chart_asset)?> / <?=strtoupper($alerts_mrkt_parse[1])?> @ <?=$ct_gen->key_to_name($alerts_mrkt_parse[0])?> (<?=strtoupper($default_btc_prim_currency_pair)?> Value)...</span>
	
	<div style='z-index: 99999; margin-top: 7px;' class='chart_reload align_center absolute_centered loading bitcoin'><img src="templates/interface/media/images/auto-preloaded/loader.gif" height='17' alt="" style='vertical-align: middle;' /> <div class='chart_reload_msg'></div></div>
		
	</div>
	
	
	<script>
	
	<?php
	$chart_mode = strtolower($default_btc_prim_currency_pair);
	include('templates/interface/desktop/php/user/user-elements/asset-charts.php');
	?>
	
	</script>
	
	
	<br/><br/><br/>
	
	<?php
		}
		
	}
	
	if ( $charts_available == 1 && $charts_shown != 1 ) {
	?>
	<div class='align_center' style='min-height: 100px;'>
	
		<p><img src='templates/interface/media/images/favicon.png' alt='' class='image_border' /></p>
		<p class='red' style='font-weight: bold; position: relative; margin: 15px;'>Click the "Select Charts" button (top left) to add charts.</p>
	</div>
	<?php
	}
	?>
	
				
				
				
</div> <!-- charts_page_wrapper END -->



