<?php

namespace App\Models;

use mysqlBuilder\Builder;

class ProductsModel extends Model
{
    static function getAll()
    {
        return Builder::table('products')
            ->get();
    }

    static function checkDiscountProducts($mobile, $productCode)
    {
        $list_old_discount = [];
        $list_error_code_product = [];

        if (count($productCode)) {
            foreach ($productCode as $row) {
                if (!empty($row)) {
                    $list_discount = Builder::table('products')//تشخیص کدهای تخفیف دار کاربر
                    ->where('discount', true)
                        ->andWhere('id', $row)
                        ->get();
                    $obj = json_decode($list_discount);

                    if ($obj->code == 200) {    //اگر تخفیف دار است قبلا کاربر انتخاب کرده یا خیر
                        $result_discount = Builder::table('product_purchase')
                            ->where('phone_number', $mobile)
                            ->andWhere('product_id', $row)
                            ->get();
                        $result_discount = json_decode($result_discount);

                        if ($result_discount->code == 200)
                            $list_old_discount[] = $row; //کدی که قبلا خریداری شده
                    } else {
                        $not_discount = Builder::table('products')
                            ->where('id', $row)
                            ->get();
                        $obj = json_decode($not_discount);

                        if ($obj->code != 200)
                            $list_error_code_product[] = $row; //لیست کدهای ناموجود
                    }
                }
            }
        }

        $result = [
            "list_old_discount" => $list_old_discount, //کدهای تخفیف دار ک قبلا کاربر خرید کرده
            "list_error_code_product" => $list_error_code_product //کدهای اشتباه
        ];
        return $result;
    }

    static function purchaseProduct($mobile, $productCode)
    {
        foreach ($productCode as $row) {
            if (!empty($row)) {
                $price_product = Builder::table('products')
                    ->Where('id', $row)
                    ->select('price', 'discount')
                    ->get();

                $obj = json_decode($price_product);
                if ($obj->response[0]->discount == true) {
                    $final_price = (((int)$obj->response[0]->price / 100) * (int)$_ENV["DiscountPercent"]);
                }else{
                    $final_price=$obj->response[0]->price;
                }

                Builder::table('product_purchase')
                    ->insert($row, $mobile, $final_price)
                    ->into('product_id', 'phone_number', 'price');
            }
        }
    }
}