<?php

namespace App\Entity;

use Craue\ConfigBundle\Entity\BaseSetting;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Craue\ConfigBundle\Repository\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Settings extends BaseSetting {

    /**
     * @var string|null
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    protected $value;

    /**
     * @var string|null
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    protected $comment;

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function getComment() {
        return $this->comment;
    }

}