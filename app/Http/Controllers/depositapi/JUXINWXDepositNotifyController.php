<?php

class JUXINWXDepositNotifyController extends BaseDepositNotifyController {
	protected $platformIdentifier = 'juxinwx';
	protected $test               = false;

	protected function & mkTestData() {
		$data = [
				"merNo" => "Z00000000001104",
				"orderNo" => "1230172261597c605172b2c",
				"transAmt" => "1001",
				"realRequestAmt" => "1001",
				"orderDate" => "20170729",
				"respCode" => "0000",
				"respDesc" => "支付成功",
				"payId" => "ZT300120170729181546612418",
				"payTime" => "20170729181633",
				"signature" => "7f3669e6f4639fe2d032766e68eeb5cf"
		];

		return $data;
	}
}
