<?php if (count($score->data) > 0): ?>
	<?php foreach ($score->data as $key => $val): ?>
		<div class="d-flex score-data">
			<div><?= $key ?></div><div><?= $val ?></div>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="d-flex score-data">
		<div>No additional score data</div>
	</div>
<?php endif; ?>
