<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
* @ORM\Entity(repositoryClass="App\Entity\WorkEntryRepository")
* @ORM\Table(name="work_entry",
*	indexes={
*		@ORM\Index(name="title_idx", columns={"title"}),
*		@ORM\Index(name="author_idx", columns={"author"}),
*		@ORM\Index(name="status_idx", columns={"status"}),
*		@ORM\Index(name="date_idx", columns={"date"})}
* )
*/
class WorkEntry extends Entity implements RoutedItemInterface {

	const STATUS_0 = 0;
	const STATUS_1 = 1;
	const STATUS_2 = 2;
	const STATUS_3 = 3;
	const STATUS_4 = 4;
	const STATUS_5 = 5;
	const STATUS_6 = 6;
	const STATUS_7 = 7;

	private static $statuses = array(
		self::STATUS_0 => 'Планира се',
		self::STATUS_1 => 'Сканира се',
		self::STATUS_2 => 'За корекция',
		self::STATUS_3 => 'Коригира се',
		self::STATUS_4 => 'Иска се SFB',
		self::STATUS_5 => 'Чака проверка',
		self::STATUS_6 => 'Проверен',
		self::STATUS_7 => 'За добавяне',
	);

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="App\Doctrine\CustomIdGenerator")
	 */
	private $id;

	/**
	 * @var int
	 * @ORM\Column(type="smallint")
	 */
	private $type;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=100)
	 */
	private $title;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $author;

	/**
	 * Year of publication on paper or in e-format
	 * @var int
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $pubYear;

	/**
	 * Publisher of the book
	 * @var string
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $publisher;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	private $user;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $comment;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $date;

	/**
	 * @var int
	 * @ORM\Column(type="smallint")
	 */
	private $status = 0;

	/**
	 * @var int
	 * @ORM\Column(type="smallint")
	 */
	private $progress = 0;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $is_frozen = false;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $tmpfiles;

	/**
	 * @var int
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $tfsize;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $uplfile;

	/**
	 * Every user gets an automatic e-mail if his entry reaches some predefined
	 * period without updates. Here we track the date of the most recent notification.
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $last_notification_date;

	/**
	 * A status managed and seen only from the adminstrator
	 * @var string
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $admin_status;

	/**
	 * A comment managed and seen only from the adminstrator
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $admin_comment;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $deleted_at;

	/**
	 * @var WorkContrib[]
	 * @ORM\OneToMany(targetEntity="WorkContrib", mappedBy="entry")
	 */
	private $contribs;

	/**
	 * @var Thread
	 * @ORM\OneToOne(targetEntity="Thread", inversedBy="workEntry")
	 */
	private $comment_thread;

	public function __toString() {
		return $this->getTitle();
	}

	public function getId() { return $this->id; }

	public function setType($type) { $this->type = $type; }
	public function getType() { return $this->type; }

	public function setTitle($title) { $this->title = $title; }
	public function getTitle() { return $this->title; }

	public function setAuthor($author) { $this->author = $author; }
	public function getAuthor() { return $this->author; }

	public function setPublisher($publisher) { $this->publisher = $publisher; }
	public function getPublisher() { return $this->publisher; }

	public function setPubYear($pubYear) { $this->pubYear = $pubYear; }
	public function getPubYear() { return $this->pubYear; }

	public function setUser($user) { $this->user = $user; }
	/** @return User */
	public function getUser() { return $this->user; }

	public function setComment($comment) { $this->comment = $comment; }
	public function getComment() { return $this->comment; }

	public function setDate($date) { $this->date = $date; }
	public function getDate() { return $this->date; }

	public function setStatus($status) { $this->status = $status; }
	public function getStatus() { return $this->status; }

	public function getStatusName() {
		return self::$statuses[$this->getStatus()];
	}

	public function setProgress($progress) { $this->progress = $progress; }
	public function getProgress() { return $this->progress; }

	public function setIsFrozen($isFrozen) { $this->is_frozen = $isFrozen; }
	public function getIsFrozen() { return $this->is_frozen; }

	public function setTmpfiles($tmpfiles) { $this->tmpfiles = $tmpfiles; }
	public function getTmpfiles() { return $this->tmpfiles; }

	public function setTfsize($tfsize) { $this->tfsize = $tfsize; }
	public function getTfsize() { return $this->tfsize; }

	public function setUplfile($uplfile) { $this->uplfile = $uplfile; }
	public function getUplfile() { return $this->uplfile; }

	/**
	 * @param \DateTime $date
	 */
	public function setLastNotificationDate($date) { $this->last_notification_date = $date; }
	public function getLastNotificationDate() { return $this->last_notification_date; }

	public function setAdminStatus($admin_status) { $this->admin_status = $admin_status; }
	public function getAdminStatus() { return $this->admin_status; }

	public function setAdminComment($admin_comment) { $this->admin_comment = $admin_comment; }
	public function getAdminComment() { return $this->admin_comment; }

	public function isNotifiedWithin($interval) {
		if ($this->getLastNotificationDate() === null) {
			return false;
		}
		return $this->getLastNotificationDate() > new \DateTime("-$interval");
	}

	public function setCommentThread(Thread $thread) {
		$this->comment_thread = $thread;
		return $this;
	}
	public function getCommentThread() { return $this->comment_thread; }

	public function getDeletedAt() { return $this->deleted_at; }

	/**
	 * @param \DateTime $deleted_at
	 */
	public function setDeletedAt($deleted_at) { $this->deleted_at = $deleted_at; }
	public function delete() {
		$this->setDeletedAt(new \DateTime);
	}
	public function isDeleted() {
		return $this->deleted_at !== null;
	}

	public function getContribs() { return $this->contribs; }

	public function getOpenContribs() {
		$openContribs = array();
		foreach ($this->getContribs() as $contrib/*@var $contrib WorkContrib*/) {
			if ( ! $contrib->isFinished() && ! $contrib->isDeleted()) {
				$openContribs[] = $contrib;
			}
		}
		return $openContribs;
	}

	/** {@inheritdoc} */
	public function getFeedItemTitle() {
		return implode(' — ', array_filter(array($this->getTitle(), $this->getAuthor())));
	}

	/** {@inheritdoc} */
	public function getFeedItemDescription() {
		$comment = nl2br($this->getComment());
		return <<<DESC
$comment
<ul>
	<li>Заглавие: {$this->getTitle()}</li>
	<li>Автор: {$this->getAuthor()}</li>
	<li>Издател: {$this->getPublisher()}</li>
	<li>Година: {$this->getPubYear()}</li>
	<li>Отговорник: {$this->getUser()->getUsername()}</li>
	<li>Етап: {$this->getStatusName()}</li>
</ul>
DESC;
	}

	/** {@inheritdoc} */
	public function getFeedItemPubDate() {
		return $this->getDate();
	}

	/** {@inheritdoc} */
	public function getFeedItemRouteName() {
		return 'workroom_entry_edit';
	}

	/** {@inheritdoc} */
	public function getFeedItemRouteParameters() {
		return array('id' => $this->getId());
	}

	/** {@inheritdoc} */
	public function getFeedItemUrlAnchor() {
		return '';
	}

	public function getFeedItemCreator() {
		return $this->getUser()->getUsername();
	}

	public function getFeedItemGuid() {
		return "chitanka-work-entry-{$this->getId()}-{$this->getStatus()}-{$this->getProgress()}";
	}
}
