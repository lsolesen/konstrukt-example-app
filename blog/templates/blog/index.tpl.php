<?php foreach ($resultset as $result) : ?>
  <h2><a href="<?php e(url($result->name)); ?>"><?php e($result->title); ?></a></h2>
  <span class="date"><?php e($result->published); ?></span>
  <p><?php e($result->excerpt); ?></p>
<?php endforeach; ?>

<p class="pager">
	<a style="float:right" href="<?php e(url(null, array('create'))); ?>">create</a>
</p>