<?php

namespace JddjOpenApi\Api;


class ProductService extends RequestService
{
    /**
     * 获取分类
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=de26f24a62aa47a49e5ab7579d638cb3
     * @param $name
     * @param $pid
     * @return mixed
     */
    public function get_category()
    {
        $params = array(
            'fields'=>['ID','PID','SHOP_CATEGORY_NAME','SHOP_CATEGORY_LEVEL','SORT']
        );
        return $this->call('pms/queryCategoriesByOrgCode',$params);
    }

    /**
     * 创建商品分类
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=de26f24a62aa47a49e5ab7579d638cb3
     * @param $name
     * @param $pid
     * @return mixed
     */
    public function create_category($name,$pid=0)
    {
        $params = array(
            'pid'=>$pid,
            'shopCategoryName'=>$name
        );
        return $this->call('pms/addShopCategory',$params);
    }

    /**
     * 修改商品分类
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=de26f24a62aa47a49e5ab7579d638cb3
     * @param $id
     * @param $name
     * @return mixed
     */
    public function update_category($id,$name)
    {
        $params = array(
            'id'=>$id,
            'shopCategoryName'=>$name
        );
        return $this->call('pms/updateShopCategory',$params);
    }

    /**
     * 删除商品分类
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=c17b96e9fe254b2a8574f6d1bc0c1667
     * @param $id
     * @return mixed
     */
    public function delete_category($id)
    {
        $params = array(
            'id'=>$id
        );
        return $this->call('pms/delShopCategory',$params);
    }

    /**
     * 修改分类排序
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=2a8267602e814be9828f0c7ce307b872
     * @param $id_list
     * @param $pid
     * @return mixed
     */
    public function category_sorting($id_list,$pid=0)
    {
        $params = array(
            'pid'=>$pid,
            'childIds'=>$id_list
        );
        return $this->call('pms/changeShopCategoryOrder',$params);
    }


    /**
     * 创建商品
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=dfe6a5ca73fa421da1c9f969b848113e
     * @param $product_id
     * @param $params
     * @return mixed
     */
    public function create_product($product_id,$params)
    {
        $params['outSkuId'] = $product_id;
        return $this->call('pms/sku/addSku',$params);
    }

    /**
     * 修改商品
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=290bdb0ea8a44e10b86b05591254ad68
     * @param $product_id
     * @param $params
     * @return mixed
     */
    public function update_product($product_id,$params)
    {
        $params['outSkuId'] = $product_id;
        return $this->call('pms/sku/updateSku',$params);
    }

    /**
     * 删除商品
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=290bdb0ea8a44e10b86b05591254ad68
     * @param $product_id
     * @return mixed
     */
    public function delete_product($product_id)
    {
        $params = [
            'outSkuId'=>$product_id,
            'fixedStatus'=>4,
        ];
        return $this->call('pms/sku/updateSku',$params);
    }

    /**
     * 批量商品上下架
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=b783a508e2cf4aca94681e4eed9af5bc
     * @param $list
     * @return mixed
     */
    public function batch_product_shelf($list)
    {
        $params = array(
            'listBaseStockCenterRequest'=>$list,
        );
        return $this->call('stock/updateVendibility',$params);
    }

    /**
     * 批量商品价格
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=166&apiid=fcbf346648a54d03b92dec8fa62ea643
     * @param $station_id
     * @param $list
     * @return mixed
     */
    public function batch_product_price($station_id,$list)
    {
        $params = array(
            'outStationNo'=>$station_id,
            'skuPriceInfoList'=>$list,  //格式：["outSkuId":"100001","price"=>100] (价格单位：分)
        );
        return $this->call('venderprice/updateStationPrice',$params);
    }
    /**
     * 查询上下架与商品库存
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=165&apiid=bc6ad75e8fd34580856e06b5eb149aad
     * @param $shop_id
     * @param $list
     * @return mixed
     */
    public function queryShelfStock($list)
    {
        $params = array(
            'listBaseStockCenterRequest'=>$list,
        );
        return $this->call('stock/queryOpenUseable',$params);
    }

    /**
     * 批量更新锁定库存
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=165&apiid=8d45790b51864a2c98ad630cbe32ce76
     * @param $shop_id
     * @param $list
     * @return mixed
     */
    public function batch_product_Lock_stock($list)
    {
        $params = array(
            'listBaseStockCenterRequest'=>$list,
        );
        return $this->call('stock/updateOpenLockQtys',$params);
    }
    /**
     * 批量更新商品库存
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=165&apiid=10812f9fc7ee4564b552f19270a7e92e
     * @param $shop_id
     * @param $list
     * @return mixed
     */
    public function batch_product_stock($shop_id,$list)
    {
        $params = array(
            'stationNo'=>$shop_id,
            'userPin'=>'系统',
            'skuStockList'=>$list,
        );
        return $this->call('stock/batchUpdateCurrentQtys',$params);
    }

    //品牌
    public function brand($name='')
    {
        $params = array(
            'fields'=>'BRAND_ID,BRAND_NAME,BRAND_STATUS',
            'pageNo'=>1,
            'pageSize'=>50,
        );
        if($name) $params['brandName'] = $name;
        return $this->call('pms/queryPageBrandInfo',$params);
    }
    //类目
    public function categories($id=0)
    {
        $params = array(
            'id'=>$id,
            'fields'=>'ID,PID,CATEGORY_NAME,CATEGORY_LEVEL,CATEGORY_STATUS,FULLPATH'
        );
        return $this->call('api/queryChildCategoriesForOP',$params);
    }


    //查询图片是否上传成功
    public function images_result($sku_ids)
    {
        $params = array(
            'skuIds'=>$sku_ids
        );
        return $this->call('order/queryListBySkuIds',$params);
    }



}