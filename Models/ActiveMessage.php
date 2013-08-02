<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Models;

use DateTime;
use Yii;
use CDbException;
use ActiveRecord;
use Notification\Model\MessageInterface;

/**
 * Class ActiveMessage
 *
 * Attributes
 * @property int $id
 * @property string $message
 * @property string $create_at
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class ActiveMessage extends ActiveRecord implements MessageInterface
{
	const DATETIME_FORMAT = 'Y-m-d H-i-s';

	protected $_meta  = array();

	public function tableName()
	{
		return 'msg_queue_message';
	}

	public function save($runValidation = true, $attributes = null)
	{
		$res = parent::save($runValidation, $attributes);

		if (!$res) {
			throw new CDbException('Cannot save post to timeline');
		}

		return $res;
	}

	protected function beforeSave()
	{
		$this->message = serialize($this);
		return parent::beforeSave();
	}

	protected function afterFind()
	{
		$this->setMeta(unserialize($this->message)->getMeta());
		parent::afterFind();
	}

	public function defaultScope()
	{
		return array_merge(parent::defaultScope(), array(
				'condition' => 'published = 1',
				'order' => 'create_at DESC, id DESC',
			));
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->getMeta());
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{
		$this->setMeta((array) unserialize($serialized));
	}

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param array $meta
	 * @return MessageInterface
	 */
	public function setMeta($meta)
	{
		$this->_meta = $meta;
		return $this;
	}

	/**
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getMeta($key = null, $default = null)
	{
		if (null === $key) {
			return $this->_meta;
		} elseif (isset($this->_meta[$key])) {
			return $this->_meta[$key];
		} else {
			return $default;
		}
	}

	public function getCreateAt()
	{
		return DateTime::createFromFormat(self::DATETIME_FORMAT, $this->create_at);
	}
}
