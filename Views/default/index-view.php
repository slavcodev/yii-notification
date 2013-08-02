<?php
/** @var Controller $this */
/** @var NotificationYii\Models\ActiveMessage $data */

echo CHtml::tag(
	'div',
	array(
		'id' => 'notice-' . $data->id,
		'class' => 'notice-item',
	),
	$data->getMeta('type')
);
