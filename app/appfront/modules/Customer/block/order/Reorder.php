<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fecshop\app\appfront\modules\Customer\block\order;
use Yii;
use fec\helpers\CModule;
use fec\helpers\CRequest;
use yii\base\InvalidValueException;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Reorder {
	
	public function getLastData(){
		$order_id = Yii::$app->request->get('order_id');
		if(!$order_id){
			$this->errorMessage('The order id is empty');
		}
		$order = Yii::$service->order->getByPrimaryKey($order_id);
		if(!$order['increment_id']){
			$this->errorMessage('The order is not exist');
		}
		$customer_id = Yii::$app->user->identity->id;
		if(!$order['customer_id'] || ($order['customer_id'] != $customer_id)){
			$this->errorMessage('The order does not belong to you');
		}
		$this->addOrderProductToCart($order_id);
		Yii::$service->url->redirectByUrlKey('checkout/cart');
	}
	public function addOrderProductToCart($order_id){
		$items = Yii::$service->order->item->getByOrderId($order_id);
		//var_dump($items);
		if(is_array($items) && !empty($items)){
			foreach($items as $one){
				$item = [
					'product_id' 		=> $one['product_id'],
					'custom_option_sku' => $one['custom_option_sku'],
					'qty' 				=> (int)$one['qty'],
				];
				//var_dump($item);exit;
				Yii::$service->cart->addProductToCart($item);
			}
		}
	}
	
	/**
	 * @property $message | String
	 * ���ӱ�����Ϣ
	 */
	public function errorMessage($message){
		Yii::$service->page->message->addError($message);
		Yii::$service->url->redirectByUrlKey('customer/order');
		exit;
	}
	
	
	
}