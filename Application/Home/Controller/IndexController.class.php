<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $bannerDb = M('banner');
        $list = $bannerDb->where('showHide=1')->select();
        $this->assign("bannerList", $list);

        $imageDb = M('image_list');
        $imageList = $imageDb->where('space_type = -1')->limit(8)->select();
        $this->assign("imageLibraryList", $imageList);

        $this->display('index');
    }
}
?>