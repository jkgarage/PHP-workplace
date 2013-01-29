
//@Brendan: set interval for image download at 7seconds.
//anything faster risks overloading calls to box.net
var interval = 7000;
var ticker = 1; //this ticket detects how many times the timer has ticked

function ticker_download() {
	$.ajax({ url: '/jkgarage_boxshow/BoxShowHome.php',
			data: {bss_callback: 'true', bss_ticker: ticker},
			type: 'post'
			//,success: function(output) {
				//alert(output);
			//}
		});
	ticker++;
	if ( ticker > 99999 ) ticker = 0;
}

setInterval( "ticker_download()", interval );