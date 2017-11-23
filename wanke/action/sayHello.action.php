<?php
/**
 * User: win7
 * Date: 2016/7/1
 * Time: 16:15
 */

/** 注册需要被客户端访问的程序，类型对应值：bool->"xsd:boolean"   string->"xsd:string"int->"xsd:int"    float->"xsd:float"*/


$server->register( 'sayHello',
    array("name"=>"xsd:string"),
    array("return"=>"xsd:string")
);
function sayHello($name) {
    return "Hello, {$name}!";
}


?>