<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics\Entity;

use DateTimeImmutable;

class Action
{
    /**
     * @var string|null
     */
    private $actionType;

    /**
     * @var string|null
     */
    private $actionText;

    /**
     * @var DateTimeImmutable|null
     */
    private $performedAt;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @return string|null
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * @param string|null $actionType
     *
     * @return $this
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getActionText()
    {
        return $this->actionText;
    }

    /**
     * @param string|null $actionText
     *
     * @return $this
     */
    public function setActionText($actionText)
    {
        $this->actionText = $actionText;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getPerformedAt()
    {
        return $this->performedAt;
    }

    /**
     * @param DateTimeImmutable $performedAt
     *
     * @return $this
     */
    public function setPerformedAt(DateTimeImmutable $performedAt)
    {
        $this->performedAt = $performedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}
