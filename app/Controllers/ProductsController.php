<?php

/**
 * All rights reserved.
 * User: mimidots
 * Date: 06-Aug-17
 * Time: 16:50
 */

namespace App\Controllers;

use App\Models\ProductsModel;

class ProductsController
{
    public static function index()
    {
        $result = ProductsModel::getAll();
        $obj = json_decode($result);
        if ($obj->status == 'success')
            view('home', ['product' => $obj->response]);
        else
            view('home', ['product' => null]);
    }

    public static function insertProduct()
    {
        view('insertProduct');
    }

    public function checkCodeProduct()
    {
        $mobile = $_POST['mobile'];
        $productCode = $_POST['productCode'];

        $result = ProductsModel::checkDiscountProducts($mobile, $productCode);

        echo json_encode($result);
    }

    public function purchaseProduct()
    {
        $mobile = $_POST['mobile'];
        $productCode = $_POST['productCode'];
        ProductsModel::purchaseProduct($mobile, $productCode);

        echo json_encode(true);
    }

}