<?php
require_once('authenticate.php');
$q = $db->prepare('select PostID, URL, group_concat(Name) tags from tags join posttags pt on pt.CompositeTagID=TagID JOIN tempPosts on CompositePostID=PostID group by PostID;');
$q->execute();
echo "<pre>";
while($row = $q->fetch(PDO::FETCH_ASSOC)) print_r($row);
echo "</pre>";
?>