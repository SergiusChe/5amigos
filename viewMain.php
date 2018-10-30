<div class="fluid-container center-block">
	<BR>		
	<h3 class="text-center">Привет, <?=$user['first_name']?>!</h3>
	<?php if ($friends['count'] < 5): ?>
		<h4 class="text-center">У Вас меньше пяти друзей!</h4>
	<?php else: ?>
		<h4 class="text-center">Вот пять Ваших друзей!</h4>
		<div class="text-center">
			<?php foreach ($friends['items'] as $friend): ?>
				<div class="well">
		    			<div>
						<a href="https://vk.com/id<?=$friend['id']?>" target="_blank"><img class="img" src="<?=$friend['photo_100']?>"></a>
					</div>
					<div>
						<?=$friend['first_name']?> <?=$friend['last_name']?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<BR>
	<button type="submit" class="btn btn-block btn-danger" onClick='window.open("<?=$logoutQuery;?>", "_self");'>Выйти</button>
</div>
