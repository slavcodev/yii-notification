<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Controllers;

use StdLib\VarDumper;
use Yii;
use Controller;

use Notification\Model\MessageInterface;
use Notification\Service\MessageServiceInterface;

/**
 * Class DefaultController
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 *
 * @method \NotificationYii\Module getModule()
 */
class DefaultController extends Controller implements MessageServiceInterface
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
		$queue = $this->getModule()->getQueue('user-' . Yii::app()->getUser()->getId());

		if ($queue->count() > 0) {
			$transaction = Yii::app()->getDb()->beginTransaction();

			try {
				while ($message = $queue->dequeue()) {
					$this->publish($message);
				}

				$transaction->commit();
			} catch (\CDbException $e) {
				$transaction->rollback();
				throw $e;
			}
		}

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

	public function publish(MessageInterface $message)
	{
		// ActiveMessage no need to publish, they are stored in the database.
	}
}
