<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use \ArrayObject;
use \JsonSerializable;

abstract class AbstractEntity extends ArrayObject implements JsonSerializable
{
    /**
     * jsonSerialize implementation from \JsonSerializable interface
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
