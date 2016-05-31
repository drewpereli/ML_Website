<?php

	if (!isset($auth['logged in']) || !$auth['logged in'])
	{
		header('Location: index.php');
	}

?>