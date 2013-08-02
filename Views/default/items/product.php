<?php
/** @var Controller $this */
/** @var TimelineYii\Models\Posts\CompanyProduct $data */

$product = $data->getSourceProduct();
$companyUrl = array('/company/view', 'id' => $product->shop->id);

echo CHtml::openTag('div', array('class' => 'comment-avatar'));
echo CHtml::link(Yii::f()->formatCompanyPic($product->shop), $companyUrl);
echo CHtml::closeTag('div');

echo CHtml::openTag('div', array('class' => 'comment-header'));

echo Yii::f()->formatDatetimeTag($data->createAt, '%s', array(
		'style' => 'padding: 0;'
	));
echo CHtml::closeTag('div');

echo CHtml::openTag('div', array('class' => 'comment-body'));

echo sprintf(
	'Компания "%s" добавила новый товар/услугу "%s"',
	CHtml::link($product->shop->name, $companyUrl),
	CHtml::link($product->name, $product->createUrl())
);

echo CHtml::closeTag('div');
