<?php

namespace JddjOpenApi\Api;


class ShopService extends RequestService
{
    /**
     * 店铺列表
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=e142a0e8e5a149bea7bb57384863caea
     * @param $page
     * @return mixed
     */
    public function get_shop_list($page)
    {
        $params = array(
            'currentPage'=>(string)$page,
            'pageSize'=>$this->rows_num
        );
        return $this->call('djstore/getStoreInfoPageBean',$params);
    }

    /**
     * 店铺信息
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=4c48e347027146d5a103e851055cb1a7
     * @param $shop_id
     * @return mixed
     */
    public function get_shop_info($shop_id)
    {
        $params = array(
            'StoreNo'=>$shop_id
        );
        return $this->call('storeapi/getStoreInfoByStationNo',$params);
    }

    /**
     * 创建店铺
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=93acef27c3aa4d8286d5c8c26b493629
     * @param $params
     * @return mixed
     */
    public function create_shop($params)
    {
        return $this->call('store/createStore',$params);
    }

    /**
     * 更新店铺
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=2600369a456446f0921e918f3d15e96a
     * @param $params
     * @return mixed
     */
    public function update_shop($params)
    {
        return $this->call('store/updateStoreInfo4Open',$params);
    }

    /**
     * 是否自动接单
     * @param $shop_id
     * @param int $is_auto 0是1否
     * @return mixed
     */
    public function set_auto_order($shop_id,$is_auto=1)
    {
        $params = [
            'stationNo'=>$shop_id,
            'isAutoOrder'=>$is_auto,
        ];
        return $this->call('store/updateStoreConfig4Open',$params);
    }



}