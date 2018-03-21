<?php

namespace JddjOpenApi\Api;


class UserService extends RequestService
{
    /** 查询商家用户信息
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=67a5cd92e9704612b77064401a696144
     * @return mixed
     */
    public function get_user($page)
    {
        $params = array(
            'pageNo'=>(string)$page,
            'pageSize'=>$this->rows_num
        );
        return $this->call('privilege/searchUser',$params);
    }

}