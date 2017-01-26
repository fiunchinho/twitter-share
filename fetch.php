<?php
ini_set("error_reporting", 1);
ini_set("html_errors", 1);
ini_set("display_errors", 1);

include __DIR__ . '/vendor/autoload.php';

define("CONSUMER_KEY", "***");
define("CONSUMER_SECRET", "***");
define("ACCESS_TOKEN", "***");
define("ACCESS_TOKEN_SECRET", "***");


$twitter = new Endroid\Twitter\Twitter(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);


$response = $twitter->query('statuses/home_timeline', 'GET', 'json', ['count' => 1000, 'contributor_details' => true]);
$tweets = json_decode($response->getContent());

$user_count = [];
$users 		= [];
foreach( $tweets as $tweet)
{
	$users[$tweet->user->id] = $tweet->user->screen_name;
	if ( !isset( $user_count[$tweet->user->id] ) )
	{
		$user_count[$tweet->user->id] = 1;
	}
	else
	{
		$user_count[$tweet->user->id]++;
	}
}
asort($user_count);
$js_string = '';
foreach( $user_count as $user_id => $count)
{
	if ( empty( $js_string ) )
	{
		$js_string = '[\'' . $users[$user_id] . '\', '. $count . ']';
	}
	else
	{
		$js_string .= ',[\'' . $users[$user_id] . '\', '. $count . ']';
	}
}

?>
<?php
arsort($user_count);
$html_string = '';
foreach( $user_count as $user_id => $count )
{
	$html_string .= "<a href=\"http://twitter.com/" . $users[$user_id] . "\" target=\"_blank\">" . $users[$user_id] . "</a>:\t\t\t\t" . $count . " tweets<br />";
}


$top_ten = array_slice($user_count, 0, 10, true);
//var_dump( $top_ten  );die;
$top_ten_string = '';
foreach( $top_ten as $user_id => $count)
{
	if ( empty( $top_ten_string ) )
	{
		$top_ten_string = '[\'' . $users[$user_id] . '\', '. $count . ']';
	}
	else
	{
		$top_ten_string .= ',[\'' . $users[$user_id] . '\', '. $count . ']';
	}
}
?>
<div id="top_ten" style="min-width: 310px; height: 400px; margin: 0 auto"></div>


<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<script type="text/javascript">
$(function () {
    var chart;
    
    $(document).ready(function () {
    	
    	// Build the chart
        $('#container').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Tweets en mi timeline'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Tweets en mi timeline',
                data: [
                    <?php echo $js_string; ?>
                ]
            }]
        });    	
    	// Build the chart
        $('#top_ten').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Tweets en mi timeline'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Tweets en mi timeline',
                data: [
                    <?php echo $top_ten_string; ?>
                ]
            }]
        });
    });
    
});
</script>
<?php
//var_dump( $users, $user_count );
echo $html_string;
