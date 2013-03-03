<link class="include" rel="stylesheet" type="text/css" href="<?php echo Slim::config('rooturl'); ?>/templates/js/jqplot/jquery.jqplot.css" />

<h1><?php echo $site; ?></h1>

<?php
	$total = $browsers['ios'] + $browsers['android'];
	$iospct = ($browsers['ios'] / $total) * 100;
	$androidpct = ($browsers['android'] / $total) * 100;
?>
<p>iOS - <?php echo $browsers['ios']; ?> (<?php echo round($iospct); ?>%)</p>
<p>Android - <?php echo $browsers['android']; ?> (<?php echo round($androidpct); ?>%)</p>
<div id="chart1" style="height:300px; width:500px;"></div>

<script class="include" type="text/javascript" src="<?php echo Slim::config('rooturl'); ?>/templates/js/jqplot/jquery.jqplot.js"></script>
	<script type="text/javascript" src="<?php echo Slim::config('rooturl'); ?>/templates/js/jqplot/plugins/jqplot.canvasTextRenderer.js"></script>
	<script type="text/javascript" src="<?php echo Slim::config('rooturl'); ?>/templates/js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.js"></script>
	<script class="code" type="text/javascript">
	$(document).ready(function(){
	  var plot1 = $.jqplot ('chart1', [[<?php foreach($stats as $item){ echo $item->hits . ","; }; ?>]]);
	});
	</script>

