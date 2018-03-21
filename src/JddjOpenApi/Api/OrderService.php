<?php

namespace JddjOpenApi\Api;


class OrderService extends RequestService
{
    /**
     * 获取订单
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=180&apiid=de26f24a62aa47a49e5ab7579d638cb3
     * @param $order_id
     * @return mixed
     */
    public function get_order($order_id)
    {
        $params = array(
            'orderId'=>$order_id
        );
        return $this->call('order/es/query',$params);
    }

    /** 确认接单
     * @param string $order_id
     * @return mixed
     */
    public function confirm_order($order_id)
    {
        $params = array(
            'orderId'=>$order_id,
            'isAgreed'=>true,
            'operator'=>'小叮',
        );
        return $this->call("ocs/orderAcceptOperate", $params);
    }

    /** 取消接单
     * @param  string $order_id
     * @return mixed
     */
    public function cancel_order($order_id)
    {
        $params = array(
            'orderId'=>$order_id,
            'isAgreed'=>false,
            'operator'=>'小叮',
        );
        return $this->call("ocs/orderAcceptOperate", $params);
    }

    /** 同意退单/取消单
     * @param $order_id
     * @return mixed
     */
    public function agree_refund($order_id)
    {
        $params = array(
            'orderId'=>$order_id,
            'isAgreed'=>true,
            'operator'=>'小叮',
        );
        return $this->call("ocs/orderCancelOperate", $params);
    }

    /** 不同意退单/取消单
     * @param string $order_id 订单Id
     * @param string $reason 商家不同意退单原因
     * @return mixed
     */
    public function disagree_refund($order_id, $reason)
    {
        $params = array(
            'orderId'=>$order_id,
            'isAgreed'=>false,
            'operator'=>'小叮',
            'remark'=>$reason,
        );
        return $this->call("ocs/orderCancelOperate", $params);
    }

    /**
     * 缺货
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=169&apiid=a7378109fd7243eea9efbb6231a7401c
     * @param $order_id
     * @param $products
     * @return \stdClass
     */
    public function out_of_stock($order_id,$products)
    {
        $params = array(
            'orderId'=>$order_id,
            'operPin'=>'小叮',
            'remark'=>'商品缺货',
            'oaosAdjustDTOList'=>$products,
        );
        return $this->call("orderAdjust/adjustOrder", $params);
    }

    /**
     * 开始配送
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=169&apiid=0e08e71a45dc48b6a337e06a852ac33a
     * @param $order_id
     * @return \stdClass
     */
    public function began_delivery($order_id)
    {
        $params = array(
            'orderId'=>$order_id,
            'operator'=>'小叮'
        );
        return $this->call("bm/open/api/order/OrderSerllerDelivery", $params);
    }

    /** 订单确认送达
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=169&apiid=ecc80f06d35141979f4841f345001f74
     * @param string $order_id
     * @return mixed
     */
    public function received_order($order_id)
    {
        $params = array(
            'orderId'=>$order_id,
            'operPin'=>'小叮',
            'operTime'=>date('Y-m-d H:i:s'),
        );
        return $this->call("ocs/deliveryEndOrder", $params);
    }

    /**
     * 获取订单评价
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=bd23397725bb4e74b69e2f2fa1c88d43
     * @param $params
     * @return mixed
     */
    public function get_comment($order_number)
    {
        $params = array(
            'orderId'=>$order_number,
        );
        return $this->call('commentOutApi/getCommentByOrderId',$params);
    }

    /**
     * 商家门店差评信息回复
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=ea0b466a7fa8489b813e8b197efca2d4
     * @param $params
     * @return mixed
     */
    public function reply_comment($order_number,$shop_id,$content)
    {
        $params = array(
            'orderId'=>$order_number,
            'storeId'=>$shop_id,
            'content'=>$content,
            'replyPin'=>'小叮',
        );
        return $this->call('commentOutApi/orgReplyComment',$params);
    }

    /**
     * 获取退款详情
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=170&apiid=6805ed690b7b4776b058067312c57d98
     * @param $refund_id
     * @return mixed
     */
    public function get_refund($refund_id)
    {
        $params = array(
            'afsServiceOrder'=>$refund_id
        );
        return $this->call('afs/getAfsService',$params);
    }
    /**
     * 同意退款
     * @param $refund_id
     * @return mixed
     */
    public function agreed_refund($refund_id)
    {
        $params = array(
            'serviceOrder'=>$refund_id,
            'approveType'=>1,
            'optPin'=>'小叮',
        );
        return $this->call('afs/afsOpenApprove',$params);
    }
    /**
     * 不同意退款
     * @param $refund_id
     * @return mixed
     */
    public function disagreed_refund($refund_id,$reason)
    {
        $params = array(
            'serviceOrder'=>$refund_id,
            'approveType'=>3,
            'rejectReason'=>$reason,
            'optPin'=>'小叮',
        );
        return $this->call('afs/afsOpenApprove',$params);
    }

}