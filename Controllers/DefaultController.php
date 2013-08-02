<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Controllers;

use Yii;
use Controller;

/**
 * Class DefaultController
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 *
 * @method \NotificationYii\Module getModule()
 */
class DefaultController extends Controller
{
	public $layout = null;

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
			array(
				'allow',
				'users' => array('@'),
				'actions' => array('index'),
			),
			array(
				'deny',
				'users' => array('*'),
			),
		);
	}

	public function actionIndex()
	{
		// Забираем сообщения из очереди ожидания
		$queue = $this->getModule()->getQueue('user-' . Yii::app()->getUser()->getId());
		$this->getModule()->dispatch($queue);

		$dataProvider = new \CActiveDataProvider(
			'\NotificationYii\Models\ActiveMessage',
			array(
				'pagination' => false,
				'sort' => false,
			)
		);

		$this->render('index', array(
				'dataProvider' => $dataProvider,
			));
	}
}
