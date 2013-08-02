<?php
/** @var Controller $this */
/** @var IDataProvider $dataProvider */

$moduleName = Yii::t('timeline', ucfirst($this->module->name));
$this->breadcrumbs = array(
	$this->pageTitle = $moduleName,
);

$this->widget('zii.widgets.CListView', array(
		'dataProvider' => $dataProvider,
		'itemView' => 'index-view',
		'template' => '{items}',
		'enableSorting' => false,
		'enablePagination' => false,
		'emptyText' => CHtml::tag('div', array(
				'class' => 'shadow radius panel'
			), 'Уведомлений нет'),
	));

