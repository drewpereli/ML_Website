<?php require_once 'utils.php'; ?>
<div class="well post-container">
	<div class="row">
		<a class="col-md-12" href="https://www.reddit.com/<?=$post['id']?>">
			<span class="col-md-1 post-score">
				<span><?=$post['score']?></span>
			</span>
			<span class="col-md-11 post">
				<div class="row">
					<span class="col-md-10 post-title">
						<?=$post['title']?>
					</span>
					<span class="col-md-2 post-author">
						<?=$post['author']?>
					</span>
				</div>
				<div class="row">
					<span class="col-md-2 post-comments">
						<?=$post['comments']?> comments
					</span>
					<span class="col-md-7"></span>
					<span class="col-md-3 post-time">
						<?=prettyTimeInterval($post['age'])?>
					</span>
				</div>
				<div class="row">
					<!-- FLOAT ALL THE TAGS HERE! -->
				</div>
			</span>
		</a>
	</div>
</div>