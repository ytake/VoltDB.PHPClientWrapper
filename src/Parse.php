<?php
namespace Ytake\VoltDB;

use VoltInvocationResponse;
use Ytake\VoltDB\Exception\StatusErrorException;

/**
 * Class Parse
 * @package Ytake\VoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Parse implements ParseInterface
{

    /**
     * @param $stdClass
     * @return mixed
     * @throws Exception\StatusErrorException
     * @throws \InvalidArgumentException
     */
    public function getResult($stdClass)
    {
        if($stdClass instanceof \stdClass) {
            if($stdClass->status != VoltInvocationResponse::SUCCESS) {
                throw new StatusErrorException($stdClass->statusstring, $stdClass->status);
            }
        }
        if($stdClass instanceof VoltInvocationResponse) {
            if($stdClass->statusCode() != VoltInvocationResponse::SUCCESS) {
                throw new StatusErrorException($stdClass->statusString(), $stdClass->statusCode());
            }
        }
        if(!$stdClass instanceof VoltInvocationResponse && !$stdClass instanceof \stdClass) {
            throw new \InvalidArgumentException("must be \\VoltInvocationResponse or \\stdClass");
        }
        return $stdClass;
    }
} 