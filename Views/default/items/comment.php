<?php
/** @var Controller $this */
/** @var NotificationYii\Models\ActiveMessage $data */

Yii::import('comments.models.*');

/** @var Comment $comment */
$comment = Comment::model()->findByPk($data->getMeta('comment'));
$user = !empty($comment->creator_id) ? $comment->user : $comment->user_name;
$owner = $comment->getOwnerModel();

echo CHtml::openTag('div', array('class' => 'comment-avatar'));
echo Yii::f()->formatUserLink($user, array(), null, Yii::f()->formatUserPic($user));
echo CHtml::closeTag('div');

echo CHtml::openTag('div', array('class' => 'comment-header'));

echo Yii::f()->formatDatetimeTag($data->createAt, '%s', array(
		'style' => 'padding: 0;'
	));
echo CHtml::closeTag('div');

echo CHtml::openTag('div', array('class' => 'comment-body'));

if ($owner instanceof Company) {
	echo sprintf(
		'%s добавил комментарий на странице компании "%s"',
		Yii::f()->formatUserLink($user),
		CHtml::link($owner->name, array('/company/view', 'id' => $owner->id))
	);
} elseif ($owner instanceof Product) {
	echo sprintf(
		'%s добавил комментарий на странице продукта "%s"',
		Yii::f()->formatUserLink($user),
		CHtml::link($owner->name, $owner->createUrl())
	);
} else {
	echo sprintf(
		'%s добавил комментарий',
		Yii::f()->formatUserLink($user)
	);
}

echo CHtml::closeTag('div');
