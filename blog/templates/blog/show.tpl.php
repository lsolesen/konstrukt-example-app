<?php $record = $context->getModel(); ?>
<h1>
  <?php e($record['title']); ?>
</h1>
<span class="date"><?php e($record['published']); ?></span>
<a href="<?php e(url(null, array('edit'))); ?>">edit</a>
|
<a href="<?php e(url(null, array('delete'))); ?>">delete</a>
<p>
  <?php e($record['content']); ?>
</p>
