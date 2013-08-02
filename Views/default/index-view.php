<?php
/** @var Controller $this */
/** @var NotificationYii\Models\ActiveMessage $data */

echo CHtml::openTag('div', array(
	'id' => 'notice-' . $data->id,
	'class' => 'shadow radius panel notice-item comment-item',
));

switch ($data->getMeta('type')) {
	case 'company.comment':
	case 'company.product.comment':
		$this->renderPartial('items/comment', array('data' => $data));
		break;
	case 'company.product.create':
		$this->renderPartial('items/product', array('data' => $data));
		break;
	default:
		echo 'Возникли ошибки при отображении уведомления.';
}

echo CHtml::closeTag('div');
