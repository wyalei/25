<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	echo "test";
    }

    public function hello($name="good"){
    	$this->assign('name', $name);
    	$this->display();
    	// echo "hello world";
    }

    // public function hello($name='thinkphp'){
    //     $this->assign('name',$name);
    //     $this->display();
    // }
}