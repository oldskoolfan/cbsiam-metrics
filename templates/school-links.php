<?php
require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();
$pageScores = $dataHelper->getScoresForLinks();
?>
<ul id="accordion" role="tablist">
<?php foreach($pageScores as $i => $pageScore): ?>
<div data-url="<?= $pageScore->url ?>" class="card">
	<h5 id="heading<?= $i ?>" class="card-header" role="tab">
		<a href="#collapse<?= $i ?>" class="collapsed" data-parent="accordion" data-toggle="collapse" aria-expanded="false" aria-controls="collapse<?= $i ?>">
			<?= $pageScore->url ?>
		</a>
	</h5>
	<div id="collapse<?= $i ?>" class="collapse" role="tabpanel" aria-labelledby="heading<?= $i ?>">
		<div class="card-block">
			<table class="table table-bordered" style="table-layout: fixed">
				<?php if (count($pageScore->scores) > 0): ?>
					<?php foreach($pageScore->scores as $score): ?>
					<tr>
						<td>Score (out of 100)</td>
						<td>
							<span><?= $score->data['speedScore'] ?? 'No score computed yet' ?></span>
							<span><?= date('n-j-Y h:i:s A', $score->getTimestamp()) ?></span>
						</td>
					</tr>
					<?php endforeach;?>
				<?php else: ?>
					<tr style="display:none">
						<td>Score (out of 100)</td>
						<td>
							<span></span>
							<span></span>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<button class="btn btn-primary">Get Latest Pagespeed Results</button>
		</div>
	</div>
</div>
<?php endforeach; ?>
</ul>
