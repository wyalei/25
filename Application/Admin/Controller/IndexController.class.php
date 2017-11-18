<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Upload;

session_start();
class IndexController extends Controller{

    public function index(){			//默认后台登录页面
        $this->display();
    }

    public function login(){
        $this->display();
    }

    public function register(){
        $this->display();
    }


    public function actionRegister(){
        $data['name'] = $_GET['name'];
        $data['pwd'] = md5($_GET['pwd']);
        $data['level'] = 0;
        $data['passed'] = 0;
        $data['register_time'] = NOW_TIME;
        $data['last_login_time'] = NOW_TIME;

        $com = M('admin_user');

        $result = $com->where("name='" . $data['name'] . "'")->select();

        if(sizeof($result) == 1){
            $status = 101;
            $msg = '该用户名已经存在';

            $this->ajaxReturn(array(
                'status' => $status,
                'msg' => $msg,
            ));
            return;
        }

        $data = $com->data($data)->add();
        if($data != false){
            $status = 0;
            $msg = '注册成功';
        }else{
            $status = 100;
            $msg = '注册失败';
        }

        $this->ajaxReturn(array(
            'status' => $status,
            'msg' => $msg,
        ));
    }

    public function actionLogin(){
        $username = $_GET['username'];
        $pwd = $_GET['pwd'];

        $com = M('admin_user');
        $data = $com->where("name='" . $username . "'")->select();

        if($data != false and sizeof($data) == 1){
            if(md5($pwd) == $data[0]['pwd']){
                if($data[0]['passed'] == 1){
                    $_SESSION['username'] = $username;
                    $_SESSION['level'] = $data[0]['level'];
                    $status = 0;
                    $msg = "登录成功";
                }else{
                    $status = 104;
                    $msg = "账户审核中";
                }
            }else{
                $status = 102;
                $msg = "密码错误";
            }
        }else{
            $status = 103;
            $msg = "该用户未注册";
        }

        $this->ajaxReturn(array(
            'status' => $status,
            'msg' => $msg,
        ));
    }

    public function isLogin(){
        if(!IndexController::isDataEmpty($_SESSION['username'])){	//判断用户名和密码是否正确
            $login=true;
        }else{
            $login=false;
        }
        return $login;		//返回判断结果
    }

    public function checkLoginState(){
        if(!IndexController::isLogin()){
            $this->display('login');
            $login=false;
        }else{
            $login=true;
        }
        return $login;		//返回判断结果
    }

    public function logout(){
        session_destroy();
        $this->display('login');
    }

    public function home(){
        if(!IndexController::checkLoginState()){
            return;
        }
        switch ($_GET['type_link']){
            case 'style':
                IndexController::handleStyle($_GET['type_link']);
                break;
            case 'space':
                IndexController::handleStyle($_GET['type_link']);
                break;
            case 'imagelist-style':
                IndexController::handleImageList($_GET['type_link']);
                break;
            case 'imagelist-space':
                IndexController::handleImageList($_GET['type_link']);
                break;
            case 'imagelist-all':
                IndexController::handleImageList($_GET['type_link']);
                break;
            case 'users-manage':
                IndexController::showUsersManagePage();
                break;
            case 'banner':
                IndexController::manageBanner();
                break;
            default:
                $this->display();
                break;
        }
    }

    public function manageBanner(){
        $dbname = "banner";
        $dbname_full = "a_" . $dbname;
        $com = M($dbname);

        if($_GET['handle'] == 'show'){
            IndexController::showBanners($com);
        }else if($_GET['handle'] == 'add'){
            $this->display("home");
        }else if($_GET['handle'] == 'edit'){
            $id = $_GET['id'];
            $list = $com->where('id=' . $id)->select();
            if($list != false && sizeof($list) == 1){
                $this->assign('dataItem', $list[0]);
            }
            $this->display("home");
        }else if($_GET['handle'] == 'delete'){
            $id = $_GET['id'];
            $result = $com->execute("delete from " . $dbname_full ." where id = " . $id);
            IndexController::showBanners($com);
        }else if($_GET['handle'] == 'showHide'){
            $id = $_GET['id'];
            $data['showHide'] = $_GET['show'];
            $data = $com->where('id=' . $id)->save($data);
            IndexController::showBanners($com);
        }else if($_GET['action'] == 'add'){
            $info = IndexController::uploadImageList(true);
            if($info){
                $data['name'] = $_POST['name'];
                $data['link_url']= $_POST['link_url'];
                $data['showHide'] = $_POST['showHide'];
                $data['modify_time'] = NOW_TIME;

                foreach ($info as $v){
                    $data['image_url'] = $v['savepath'].$v['savename'];
                }

                $data = $com->data($data)->add();
            }
            IndexController::showBanners($com);
        }else if($_GET['action'] == 'edit'){
            $id = $_POST['id'];
            $data['name'] = $_POST['name'];
            $data['link_url']= $_POST['link_url'];
            $data['modify_time'] = NOW_TIME;

            $info = IndexController::uploadImageList(false);
            foreach ($info as $v){
                $data['image_url'] = $v['savepath'].$v['savename'];
            }

            $data = $com->where('id=' . $id)->save($data);
            IndexController::showBanners($com);
        }
    }

    public function showBanners($com){
        $list = $com->select();
        $_GET['handle'] = 'show';
        $this->assign("list", $list);
        $this->display("home");
    }

    public function getAllUsers(){
        $com = M('admin_user');
        $data = $com->select();
        return$data;
    }

    public function userRegisterPass(){
        $com = M('admin_user');
        $id = $_GET['id'];
        $data['passed'] = 1;
        $com->where('id=' . $id)->save($data);
        IndexController::showUsersManagePage();
    }

    public function deleteUser(){
        $com = M('admin_user');
        $id = $_GET['id'];
        $com->where('id=' . $id)->delete();
        IndexController::showUsersManagePage();
    }

    public function showUsersManagePage(){
        $data = IndexController::getAllUsers();
        if($data != false){
            $this->assign('userArr', $data);
        }
        $_GET['type_link'] = 'users-manage';
        $this->display("home");
    }

    //--------------------------
    public function showImageListData($type, $com){
        if($type == 'imagelist-style'){
            $list = $com->where('space_type = -1')->select();
        }else if($type == 'imagelist-space'){
            $list = $com->where('space_type <> -1')->select();
        }else{
            $list = $com->select();
        }

        $list = IndexController::addImageStyleSpaceName($list);
        $this->assign('list', $list);
        $this->display("home");
    }

    public function uploadImageList($showError){
        $upload = new \Think\Upload();
        $upload->maxSize = 1024*1024;
        $upload->allowExts = array('jpg','png','jpeg');
        $upload->savePath = "Public/thumb";
        $upload->replace = true;
//        $upload->saveName = array('date', 'y-m-d');
        $info = $upload->upload();
        if($showError and !$info){
            $this->error($upload->getError());
        }

        return $info;
    }

    public function handleImageList($type){
        $dbname = "image_list";
        $full_dbname = "a_" . $dbname;
        $com = M($dbname);
        if($_GET['action']=='add'){
            $info = IndexController::uploadImageList(true);
            if($info){
                $data['name'] = $_POST['name'];
                $data['style_type']= $_POST['style'];
                $data['space_type'] = $_POST['space'];
                $data['hot'] = $_POST['hot'];
                $data['showHide'] = $_POST['showHide'];
                $data['modify_time'] = NOW_TIME;

                $pathArr = array();
                foreach ($info as $v){
                    $path = $v['savepath'].$v['savename'];
                    $pathArr[] = $path;
                }

                $pathJson = json_encode($pathArr);
                $data['image_list'] = $pathJson;
                $data = $com->data($data)->add();
            }
            IndexController::showImageListData($type, $com);
        }else if($_GET['action']=='edit'){
            $id = $_POST['id'];
            $data['name'] = $_POST['name'];
            $data['style_type']= $_POST['style'];
            $data['space_type'] = $_POST['space'];
            $data['hot'] = $_POST['hot'];
            $data['modify_time'] = NOW_TIME;
            $deleteStr = $_POST['deleteImageIndex'];

            $list = $com->where('id=' . $id)->select();
            $imageArrJson = $list[0]['image_list'];
            $imageArr = json_decode($imageArrJson);
            $imageResultArr = array();

            if(!IndexController::isDataEmpty($deleteStr)){
                $deleteArr = explode(",", $deleteStr);
                $size = sizeof($imageArr);
                for($i = 0; $i < $size; $i++){
                    if(!in_array($i, $deleteArr)){
                        $imageResultArr[] = $imageArr[$i];
                    }
                }
            }else{
                $imageResultArr = $imageArr;
            }

            $info = IndexController::uploadImageList(false);
            if($info){
                foreach ($info as $v){
                    $imageResultArr[] = $v['savepath'].$v['savename'];
                }
            }

            $data['image_list'] = json_encode($imageResultArr);
            $data = $com->where('id=' . $id)->save($data);
            IndexController::showImageListData($type, $com);
        }else if($_GET['handle'] == 'show'){
            IndexController::showImageListData($type, $com);
        }else if($_GET['handle'] == 'showDetail'){
            $id = $_GET['id'];
            $list = $com->where('id=' . $id)->select();
            $list = IndexController::addImageStyleSpaceName($list);
            $this->assign('item', $list[0]);
            $this->display("home");
        }else if($_GET['handle'] == 'delete'){
            $id = $_GET['id'];
            $result = $com->execute("delete from " . $full_dbname ." where id = " . $id);
            IndexController::showImageListData($type, $com);
        }else if($_GET['handle'] == 'showHide'){
            $id = $_GET['id'];
            $data['showHide'] = $_GET['show'];
            $data = $com->where('id=' . $id)->save($data);

            IndexController::showImageListData($type, $com);
        }else if($_GET['handle'] == 'add'){
            IndexController::assignStyleSpaceList();
            $this->display("home");
        }else if($_GET['handle'] == 'edit'){
            $id = $_GET['id'];
            $list = $com->where('id=' . $id)->select();
            $list = IndexController::addImageStyleSpaceName($list);
            IndexController::assignStyleSpaceList();
            $this->assign('dataItem', $list[0]);
            $this->display("home");
        }
    }

    public function isDataEmpty($str){
        if($str == "" || $str == null || $str == undefined){
            return true;
        }
        return false;
    }

    public function assignStyleSpaceList(){
        $styleDb = M("style");
        $spaceDb = M("space");
        //ssh test
        $styleList = $styleDb->select();
        $spaceList = $spaceDb->select();
        $this->assign("styleList", $styleList);
        $this->assign("spaceList", $spaceList);
    }

    public function addImageStyleSpaceName($list){
        $styleDb = M("style");
        $spaceDb = M("space");

        $styleList = $styleDb->select();
        $spaceList = $spaceDb->select();

        $styleMap = array();
        $spaceMap = array();

        foreach ($styleList as $item){
            $styleMap[strval($item['id'])] = $item['show_name'];
        }

        foreach ($spaceList as $item){
            $spaceMap[strval($item['id'])] = $item['show_name'];
        }
        $spaceMap['-1'] = '无';
        $size = sizeof($list);
        for($i = 0; $i < $size; $i++){
            $item = $list[$i];
            $item['style_name'] = $styleMap[strval($item['style_type'])];
            $item['space_name'] = $spaceMap[strval($item['space_type'])];

            $list[$i] = $item;
        }

        return $list;
    }

    public function showStyleSpaceData($com){
        $list = $com->select();
        $this->assign('list', $list);
        $this->display("home");
    }

    public function handleStyle($type){
        $dbname = "style";
        if($type == "space"){
            $dbname = "space";
        }
        $com = M($dbname);
        if($_GET['handle']=='add'){
            $data['show_name'] = $_POST['name'];
            $data['show_order'] = $_POST['order'];
            $data['modify_time'] = NOW_TIME;
            $data = $com->data($data)->add();
            IndexController::showStyleSpaceData($com);
        }else if($_GET['handle'] == 'show'){
            IndexController::showStyleSpaceData($com);
        }else if($_GET['handle'] == 'delete'){
            $id = $_GET['id'];
            $full_dbname = "a_" . $dbname;
            $result = $com->execute("delete from " . $full_dbname ." where id = " . $id);
            IndexController::showStyleSpaceData($com);
        }else if($_GET['handle'] == 'modify'){
            $id = $_GET['id'];
            $new_name = $_GET['name'];
            $new_order = $_GET['order'];
            $full_dbname = "a_" . $dbname;
            if(isset($new_name)){
                $result = $com->execute("update " . $full_dbname. " set show_name='" . $new_name . "' where id=" . $id);
            }else if(isset($new_order)){
                $result = $com->execute("update " . $full_dbname. " set show_order=" . $new_order . " where id=" . $id);
            }
            IndexController::showStyleSpaceData($com);
        }
    }
}
?>