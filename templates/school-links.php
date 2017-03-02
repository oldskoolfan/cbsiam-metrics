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
				<tr>
					<td>Score (out of 100)</td>
					<td data-key="speedScore">
						<?= $pageScore->data['speedScore'] ?? 'No score computed yet' ?>
					</td>
				</tr>
			</table>
			<button class="btn btn-primary">Get Latest Pagespeed Results</button>
		</div>
	</div>
</div>
<?php endforeach; ?>
</ul>
