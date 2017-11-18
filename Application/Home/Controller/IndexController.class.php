<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $bannerDb = M('banner');
        $list = $bannerDb->where('showHide=1')->select();
        $this->assign("bannerList", $list);
        $this->display('index');
    }
}
?>