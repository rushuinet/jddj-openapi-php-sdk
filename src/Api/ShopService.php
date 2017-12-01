<?php

namespace JddjOpenApi\Api;


class ShopService extends RequestService
{
    /** 查询商家信息
     * @return mixed
     */
    public function get_user()
    {
        return $this->call('privilege/searchUser');
    }

}