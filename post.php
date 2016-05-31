<?php require_once 'utils.php'; ?>
<style>
	
</style>

<?php
	$wellClass = "well post-container col-xs-";
	if(!$narrow) $wellClass .= '12'; 
	else $wellClass .= '10';
	$leftClass = "left-of-post col-xs-";
	if(!$narrow) $leftClass .= '0'; 
	else $leftClass .= '2';
?>

<span>
	<div class="<?=$wellClass;?>" id="<?=$post['id']?>">
		<div class="row">
			<a class="col-md-11" href="https://www.reddit.com/<?=$post['id']?>">
				<span class="col-md-1 post-score">
					<span><?=$post['score']?></span>
				</span>
				<span class="col-md-11 post">
					<div class="row">
						<span class="col-md-9 post-title">
							<?=$post['title']?>
						</span>
						<span class="col-md-3 post-author">
							<?=$post['author']?>
						</span>
					</div>
					<div class="row">
						<span class="col-md-3 post-comments">
							<?=$post['comments']?> comments
						</span>
						<span class="col-md-6"></span>
						<span class="col-md-3 post-time">
							<!--<?=prettyTimeInterval($post['age'])?>-->
						</span>
					</div>
					<div class="row tag-row">
						<!-- FLOAT ALL THE TAGS HERE! -->
						<?php
							$postId = $post['id'];
							$query =   "SELECT tag
										FROM post_tags
										WHERE post='$postId'";
							$stm = $db->prepare($query);
							$stm->execute();
							while ($tag = $stm->fetch(PDO::FETCH_ASSOC))
							{
								?>
								<small class="display-tag text-muted bg-success text-center">
									<?=str_replace('_',' ',$tag['tag'])?>
								</small>
								<?php
							}
						?>
					</div>
				</span>
			</a>
		</div>
	</div>
	<span class="<?=$leftClass;?>" data-tag="<?=$post['id'];?>"></span>
</span>






