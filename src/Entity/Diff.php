<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics\Entity;

use DateTimeImmutable;

class Diff
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array|Action[]
     */
    private $actionList = [];

    /**
     * @var DateTimeImmutable|null
     */
    private $createdAt;

    /**
     * @var string|null
     */
    private $authorUsername;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array|Action[]
     */
    public function getActionList()
    {
        return $this->actionList;
    }

    public function addAction(Action $action)
    {
        $this->actionList[] = $action;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorUsername(): string
    {
        return $this->authorUsername;
    }

    /**
     * @param string|null $authorUsername
     *
     * @return $this
     */
    public function setAuthorUsername($authorUsername)
    {
        $this->authorUsername = $authorUsername;

        return $this;
    }
}
