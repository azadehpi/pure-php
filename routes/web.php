<?php


Router::get('','ProductsController@index');
Router::get('insertProduct','ProductsController@insertProduct');
Router::post('checkCodeProduct','ProductsController@checkCodeProduct');
Router::post('purchaseProduct','ProductsController@purchaseProduct');
?>