<?php
	function prettyTimeInterval($seconds){
		$str = 'PT'.$seconds.'S';
		$age = (new DateTime())->add(new DateInterval($str));
		$age = $age->diff(new DateTime());
		$days = $age->format('%d');
		if ($days == "0")
		{
			return $age->format('%h hours, %i minutes ago');
		}
		return $age->format('%d days, %h hours, %i minutes ago');
	}
	function pp($arg){
		echo "<pre>";
		print_r($arg);
		echo "</pre>";
	}
?>

<html>
<script>
	function prettyTimeInterval(seconds){
		var msSinceCreation = (Date.now() - (seconds * 1000));
		var minutesSinceCreation = Math.round(msSinceCreation / (1000*60));
		var hoursSinceCreation = Math.floor(minutesSinceCreation / (60));
		var daysSinceCreation = Math.floor(hoursSinceCreation / 24);

		var minuteComponent = minutesSinceCreation - (hoursSinceCreation * 60);
		var hoursComponent = hoursSinceCreation - (daysSinceCreation * 24);

		var returnString = daysSinceCreation + " days, " + hoursComponent + " hours, " + minuteComponent + " minutes ago";
		return returnString; 
	}

	function capitalize(string){
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
</script>
</html>