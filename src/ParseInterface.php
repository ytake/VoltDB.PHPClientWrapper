<?php
namespace Ytake\VoltDB;

/**
 * Interface ParseInterface
 * @package Ytake\VoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface ParseInterface
{

    /**
     * @param $stdClass
     * @return mixed
     * @throws Exception\StatusErrorException
     * @throws \InvalidArgumentException
     */
    public function getResult($stdClass);

} 