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


    public function checkLogin(){			//后台登录处理方法
        $_SESSION['MR'] = "mr";
        $_SESSION['MRKJ'] = "111111";
        $username=$_POST['text'];		//获取用户名
        $userpwd=$_POST['pwd'];
        if($username=="" || $userpwd==""){				//判断用户名和密码是否为空
            $this->assign('hint','用户名或者密码不能为空');
            $this->assign('url','__URL__');
            $this->display('information');				//指定提示信息模板页
        }else{
            // if($username!=Session::get(MR)||$userpwd!=Session::get(MRKJ)){	//验证登录用户是否正确
            if($username!=$_SESSION['MR']||$userpwd!=$_SESSION['MRKJ']){	//验证登录用户是否正确
                // if($username!="user0"||$userpwd!="111111"){	//验证登录用户是否正确
                $this->assign('hint','您不是权限用户');
                $this->assign('url','__URL__/');
                $this->display('information');
            }else{
                $_SESSION['username']=$username;		//将登录用户名赋给SESSION变量
                $_SESSION['userpwd']=$userpwd;
                $this->assign('hint','欢迎管理员回归');
                // $this->assign('url','__URL__/adminIndex');	//设置后台管理主页链接
                $this->assign('url','home?type_link=imagelist-style&handle=show');	//设置后台管理主页链接
                $this->display('information');
            }
        }
    }

    public function isLogin(){
        if(!IndexController::isDataEmpty($_SESSION['username']) and !IndexController::isDataEmpty($_SESSION['userpwd'])
            and $_SESSION['username']==$_SESSION['MR'] and $_SESSION['userpwd']==$_SESSION['MRKJ']){	//判断用户名和密码是否正确
            $login=true;
        }else{
            $login=false;
        }
        return $login;		//返回判断结果
    }

    public function checkLoginState(){
        if(!IndexController::isLogin()){	//判断用户名和密码是否正确
            $this->assign('hint','您不是权限用户');
            $this->assign('url','login');
            $this->display('information');
            $login=false;
        }else{
            $login=true;
        }
        return $login;		//返回判断结果
    }

    public function logout(){
        if($_SESSION['username']==$_SESSION['MR'] and $_SESSION['userpwd']==$_SESSION['MRKJ']){
            session_destroy();
            $this->assign('hint','管理员退出');
            $this->assign('url','login');
            $this->display('information');
        }
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
        }

        $this->display();
    }

    public function uploadImageList(){
        $upload = new \Think\Upload();
        $upload->maxSize = 1024*1024;
        $upload->allowExts = array('jpg','png','jpeg');
        $upload->savePath = "Public/thumb";
        $upload->replace = true;
//        $upload->saveName = array('date', 'y-m-d');
        $info = $upload->upload();
//        if(!$info){
//            $this->error($upload->getErrorMsg());
//        }

        return $info;
    }

    public function handleImageList($type){
        $dbname = "image_list";
        $full_dbname = "a_" . $dbname;
        $com = M($dbname);
        if($_GET['action']=='add'){
            $info = IndexController::uploadImageList();
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
                if($data!=false){
                    IndexController::showImageList($type,"数据添加成功");

                }else{
                    IndexController::showImageList($type,"添加失败");
                }
            }

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

            $info = IndexController::uploadImageList();
            if($info){
                foreach ($info as $v){
                    $imageResultArr[] = $v['savepath'].$v['savename'];
                }
            }

            $data['image_list'] = json_encode($imageResultArr);
            $data = $com->where('id=' . $id)->save($data);
            if($data!=false){
                IndexController::showImageList($type,"数据更新成功");
            }else{
                IndexController::showImageList($type,"添加失败");
            }
        }else if($_GET['handle'] == 'show'){
            if($type == 'imagelist-style'){
                $list = $com->where('space_type = -1')->select();
            }else if($type == 'imagelist-space'){
                $list = $com->where('space_type <> -1')->select();
            }else{
                $list = $com->select();
            }

            $list = IndexController::addImageStyleSpaceName($list);
            $this->assign('list', $list);

        }else if($_GET['handle'] == 'showDetail'){
            $id = $_GET['id'];
            $list = $com->where('id=' . $id)->select();
            $list = IndexController::addImageStyleSpaceName($list);
            $this->assign('item', $list[0]);
        }else if($_GET['handle'] == 'delete'){
            $id = $_GET['id'];
            $result = $com->execute("delete from " . $full_dbname ." where id = " . $id);
            if($result){
                IndexController::showStyle($type,"数据删除成功");
            }else{
                IndexController::showStyle($type,"数据删除失败");
            }
        }else if($_GET['handle'] == 'showHide'){
            $id = $_GET['id'];
            $data['showHide'] = $_GET['show'];
            $data = $com->where('id=' . $id)->save($data);

            if($data!=false){
                IndexController::showImageList($type,"数据更新成功");
            }else{
                IndexController::showImageList($type,"添加失败");
            }
        }else if($_GET['handle'] == 'add'){
            IndexController::assignStyleSpaceList();
        }else if($_GET['handle'] == 'edit'){
            $id = $_GET['id'];
            $list = $com->where('id=' . $id)->select();
            $list = IndexController::addImageStyleSpaceName($list);
            IndexController::assignStyleSpaceList();
            $this->assign('dataItem', $list[0]);
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

    public function handleStyle($type){
        $dbname = "style";
        if($type == "space"){
            $dbname = "space";
        }
        $full_dbname = "a_" . $dbname;
        $com = M($dbname);
        if($_GET['handle']=='add'){
            $data['show_name'] = $_POST['name'];
            $data['show_order'] = $_POST['order'];
            $data['modify_time'] = NOW_TIME;
            $data = $com->data($data)->add();
            if($data!=false){
                IndexController::showStyle($type,"数据添加成功");

            }else{
                IndexController::showStyle($type,"添加失败");
            }
        }else if($_GET['handle'] == 'show'){
            $list = $com->select();
            $this->assign('list', $list);
        }else if($_GET['handle'] == 'delete'){
            $id = $_GET['id'];
            $result = $com->execute("delete from " . $full_dbname ." where id = " . $id);
            if($result){
                IndexController::showStyle($type,"数据删除成功");
            }else{
                IndexController::showStyle($type,"数据删除失败");
            }
        }else if($_GET['handle'] == 'modify'){
            $id = $_GET['id'];
            $new_name = $_GET['name'];
            $new_order = $_GET['order'];
            if(isset($new_name)){
                $result = $com->execute("update " . $full_dbname. " set show_name='" . $new_name . "' where id=" . $id);
                if($result){
                    IndexController::showStyle($type,"数据删除成功");
                }else{
                    IndexController::showStyle($type,"更新失败");
                }
            }else if(isset($new_order)){
                $result = $com->execute("update " . $full_dbname. " set show_order=" . $new_order . " where id=" . $id);
                if($result){
                    IndexController::showStyle($type,"数据删除成功");
                }else{
                    IndexController::showStyle($type,"更新失败");
                }
            }
        }
    }

    public function showImageList($type, $msg){
        $this->assign('hint',$msg);
        $this->assign('url','home?type_link=' .$type. '&handle=show');
        $this->display('information');
    }

    public function showStyle($type, $msg){
        $this->assign('hint',$msg);
        $this->assign('url','home?type_link=' .$type. '&handle=show');
        $this->display('information');
    }


    //------------------------------------------------------------
    public function admin(){			//后台登录处理方法
        // Load('admin');					//载入配置文件中设置的用户名和密码
        // session::set("MR", "mr");
        // session::set("MRKJ", "111111");
        $_SESSION['MR'] = "mr";
        $_SESSION['MRKJ'] = "111111";
        $username=$_POST['text'];		//获取用户名
        $userpwd=$_POST['pwd'];
        if($username==""||$userpwd==""){				//判断用户名和密码是否为空
            $this->assign('hint','用户名或者密码不能为空');
            $this->assign('url','__URL__');
            $this->display('information');				//指定提示信息模板页
        }else{
            // if($username!=Session::get(MR)||$userpwd!=Session::get(MRKJ)){	//验证登录用户是否正确
            if($username!=$_SESSION['MR']||$userpwd!=$_SESSION['MRKJ']){	//验证登录用户是否正确
                // if($username!="user0"||$userpwd!="111111"){	//验证登录用户是否正确
                $this->assign('hint','您不是权限用户');
                $this->assign('url','__URL__/');
                $this->display('information');
            }else{
                $_SESSION['username']=$username;		//将登录用户名赋给SESSION变量
                $_SESSION['userpwd']=$userpwd;
                $this->assign('hint','欢迎管理员回归');
                // $this->assign('url','__URL__/adminIndex');	//设置后台管理主页链接
                $this->assign('url','adminIndex');	//设置后台管理主页链接
                $this->display('information');
            }
        }
    }
    public function high(){									//高级类别处理方法
        header("Content-Type:text/html;charset=utf-8");		//设置编码格式
        $com=M('hightype');									//实例化模型类，a_hightype表
        if($_GET['handle']=='insert'){						//判断超级链接的参数值，是添加语句还是管理数据
            if(IndexController::checkEnv()){					//判断用户是否具有添加权限
                if(isset($_POST['button'])){
                    $data['ChineseName']=$_POST['ChineseName'];	//获取表单提交的数据
                    $data['EnglishName']=$_POST['EnglishName'];
                    $data=$com->data($data)->add();				//执行添加操作
                    if($data!=false){
                        $this->assign('hint','数据添加成功！');
                        $this->assign('url','adminIndex?type_link=high&handle=admin');
                        $this->display('information');
                    }else{
                        $this->assign('hint','添加失败！');
                        $this->assign('url','adminIndex?type_link=high&handle=insert');
                        $this->display('information');
                    }
                }
            }
        }else{
            // import("ORG.Util.Page");	//载入分页类
            $count=$com->count();		//统计总的记录数
            // $Page=new Page($count,8);	//实例化分页类，设置每页显示8条记录
            $Page=new \Think\Page($count,8);
            $show= $Page->show();		//输出分页超级链接
            $list = $com->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();	//执行分页查询
            $this->assign('list',$list);	//将查询结果赋给模板变量
            $this->assign('page',$show); 	//将获取的分页超级链接赋给模板变量

            // $list = $com->select();
            // $this->assign('list',$list);	//将查询结果赋给模板变量
            // $show = 1;
            // $this->assign('page',$show); 	//将获取的分页超级链接赋给模板变量
        }
    }
    public function middle(){
        header("Content-Type:text/html;charset=utf-8");
        $com=M('middletype');
        $hightype =M('hightype');
        $highdata=$hightype ->select();
        $this->assign('highdata',$highdata);
        if($_GET['handle']=='insert'){
            if(IndexController::checkEnv()){
                if(isset($_POST['button'])){
                    $data['highid']=$_POST['highid'];
                    $data['ChineseName']=$_POST['ChineseName'];
                    $data['EnglishName']=$_POST['EnglishName'];
                    $data=$com->data($data)->add();
                    if($data!=false){
                        $this->assign('hint','数据添加成功！');
                        $this->assign('url','adminIndex?type_link=middle&handle=admin');
                        $this->display('information');
                    }else{
                        $this->assign('hint','添加失败！');
                        $this->assign('url','adminIndex?type_link=middle&handle=insert');
                        $this->display('information');
                    }
                }
            }
        }else{
            // import("ORG.Util.Page");
            $count=$com->count();
            // $Page=new Page($count,8);
            $Page=new \Think\Page($count,8);
            $show= $Page->show();
            $list = $com->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('list',$list);
            $this->assign('page',$show);
        }
    }
    public function elementary(){
        header("Content-Type:text/html;charset=utf-8");
        $com=M('elementarytype');
        $middletype =M('middletype');
        $middledata=$middletype ->select();
        $this->assign('middledata',$middledata);
        if($_GET['handle']=='insert'){
            if(IndexController::checkEnv()){
                if(isset($_POST['button'])){
                    $data['middleid']=$_POST['middleid'];
                    $data['ChineseName']=$_POST['ChineseName'];
                    $data['EnglishName']=$_POST['EnglishName'];
                    $data=$com->data($data)->add();
                    if($data!=false){
                        $this->assign('hint','数据添加成功！');
                        $this->assign('url','adminIndex?type_link=elementary&handle=admin');
                        $this->display('information');
                    }else{
                        $this->assign('hint','添加失败！');
                        $this->assign('url','adminIndex?type_link=elementary&handle=insert');
                        $this->display('information');
                    }
                }
            }
        }else{
            // import("ORG.Util.Page");
            $count=$com->count();
            // $Page=new Page($count,8);
            $Page=new \Think\Page($count,8);
            $show= $Page->show();
            $list = $com->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('list',$list);
            $this->assign('page',$show);
        }
    }
    public function small(){
        header("Content-Type:text/html;charset=utf-8");
        $com=M('smalltype');
        $elementarytype =M('elementarytype');
        $elementarydata=$elementarytype ->select();
        $this->assign('elementarydata',$elementarydata);
        if($_GET['handle']=='insert'){
            if(IndexController::checkEnv()){
                if(isset($_POST['button'])){
                    $data['elementaryid']=$_POST['elementaryid'];
                    $data['ChineseName']=$_POST['ChineseName'];
                    $data['EnglishName']=$_POST['EnglishName'];
                    $data=$com->data($data)->add();
                    if($data!=false){
                        $this->assign('hint','数据添加成功！');
                        $this->assign('url','adminIndex?type_link=small&handle=admin');
                        $this->display('information');
                    }else{
                        $this->assign('hint','添加失败！');
                        $this->assign('url','adminIndex?type_link=small&handle=insert');
                        $this->display('information');
                    }
                }
            }
        }else{
            // import("ORG.Util.Page");
            $count=$com->count();
            // $Page=new Page($count,8);
            $Page=new \Think\Page($count,8);
            $show= $Page->show();
            $list = $com->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('list',$list);
            $this->assign('page',$show);
        }
    }

    public function common(){					//数据的处理方法
        $com=M('common');						//实例化模型
        if($_GET['handle']=='insert'){			//判断是否是添加操作
            if(IndexController::checkEnv()){		//判断用户的权限
                $middletype =M('middletype');		//实例化中级类别
                $data2=$middletype ->select();		//执行查询操作
                $this->assign('data2',$data2);		//将查询结果赋给模板变量
                $elementarytype=M('elementarytype');
                $data3=$elementarytype->select();
                $this->assign('data3',$data3);
                $smalltype=M('smalltype');
                $data4=$smalltype->select();
                $this->assign('data4',$data4);
                if(isset($_POST['button'])){		//判断提交按钮是否被设置
                    $data['middleid']=$_POST['middleid'];				//获取表单提交的数据
                    $data['elementaryid']=$_POST['elementaryid'];
                    $data['smallid']=$_POST['smallid'];
                    $data['title']=$_POST['title'];
                    $data['href']=$_POST['href'];
                    $data=$com->data($data)->add();						//执行数据的添加操作
                    if($data!=false){
                        $this->assign('hint','数据添加成功！');
                        $this->assign('url','adminIndex?type_link=data&handle=admin');
                        $this->display('information');
                    }else{
                        $this->assign('hint','添加失败！');
                        $this->assign('url','adminIndex?type_link=data&handle=insert');
                        $this->display('information');
                    }
                }
            }
        }else{
            // import("ORG.Util.Page");
            // import("Think.Page");
            $count=$com->count();					//统计数据库中的记录数
            $Page=new \Think\Page($count,8);				//实例化分页类
            $show= $Page->show();					//获取分页超级链接
            $list = $com->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();	//执行分页查询
            $this->assign('list',$list);			//将分页查询结果赋给模板变量
            $this->assign('page',$show);			//将获取的分页超级链接赋给模板变量

        }
    }
    public function adminIndex(){				//后台管理系统主页
        if(IndexController::checkEnv()){			//判断是否具有访问权限
            switch($_GET['type_link']){			//根据超级链接传递的变量值输出对应的内容
                case "high":
                    IndexController::high();		//执行high方法
                    break;
                case "middle":
                    IndexController::middle();
                    break;
                case "elementary":
                    IndexController::elementary();
                    break;
                case "small":
                    IndexController::small();
                    break;
                case "data":
                    IndexController::common();
                    break;
                default:						//默认输出数据管理内容
                    IndexController::common();
            }
            $this->display('adminIndex');		//指定模板页
        }
    }

    function deletedata(){						//删除数据方法
        if(IndexController::checkEnv()){			//判断是否具有删除权限
            $cl=urldecode($_GET['link_id']);	//获取超级链接传递的ID值
            $new=M('common');					//实例化模型类
            $new=$new->execute("delete from a_common where id in (".$cl.")");	//执行删除语句
            if($new!=false){
                $this->assign('hint','数据删除成功！');
                $this->assign('url','adminIndex?type_link=data&handle=admin');
                $this->display('information');
            }else{
                $this->assign('hint','出现未知错误！');
                $this->assign('url','adminIndex?type_link=data&handle=admin');
                $this->display('information');
            }
        }
    }
    function deletetype(){
        if(IndexController::checkEnv()){				//判断当前用户是否具备删除权限
            $cl=urldecode($_GET['link_id']);		//获取超级链接传递的ID值
            $new=M('hightype');						//实例化模型类
            $new=$new->execute("delete from a_hightype where id in (".$cl.")");	//以ID值为条件，执行删除操作
            if($new!=false){
                $new=M('middletype');				//实例化中级类别表
                $new=$new->execute("delete from a_middletype where highid in (".$cl.")");	//删除中级类别中数据
                $newe=M('elementarytype');
                $newe=$newe->execute("delete from a_elementarytype where middleid in (".$cl.")");
                $news=M('smalltype');
                $news=$news->execute("delete from a_smalltype where elementaryid in (".$cl.")");
                $this->assign('hint','数据删除成功！');
                $this->assign('url','adminIndex?type_link=high&handle=admin');
                $this->display('information');
            }else{
                $this->assign('hint','出现未知错误！');
                $this->assign('url','adminIndex?type_link=high&handle=admin');
                $this->display('information');
            }
        }
    }
    function deletemiddle(){
        if(IndexController::checkEnv()){
            $cl=urldecode($_GET['link_id']);
            $new=M('middletype');
            $new=$new->execute("delete from a_middletype where id in (".$cl.")");
            if($new!=false){
                $newe=M('elementarytype');
                $newe=$newe->execute("delete from a_elementarytype where middleid in (".$cl.")");
                $news=M('smalltype');
                $news=$news->execute("delete from a_smalltype where elementaryid in (".$cl.")");
                $this->assign('hint','数据删除成功！');
                $this->assign('url','adminIndex?type_link=middle&handle=admin');
                $this->display('information');
            }else{
                $this->assign('hint','出现未知错误！');
                $this->assign('url','adminIndex?type_link=middle&handle=admin');
                $this->display('information');
            }
        }
    }
    function deleteelementary(){
        if(IndexController::checkEnv()){
            $cl=urldecode($_GET['link_id']);
            $new=M('elementarytype');
            $new=$new->execute("delete from a_elementarytype where id in (".$cl.")");
            if($new!=false){
                $news=M('smalltype');
                $news=$news->execute("delete from a_smalltype where elementaryid in (".$cl.")");
                $this->assign('hint','数据删除成功！');
                $this->assign('url','adminIndex?type_link=elementary&handle=admin');
                $this->display('information');
            }else{
                $this->assign('hint','出现未知错误！');
                $this->assign('url','adminIndex?type_link=elementary&handle=admin');
                $this->display('information');
            }
        }
    }
    function deletesmall(){
        if(IndexController::checkEnv()){
            $cl=urldecode($_GET['link_id']);
            $new=M('smalltype');
            $new=$new->execute("delete from a_smalltype where id in (".$cl.")");
            if($new!=false){
                $this->assign('hint','数据删除成功！');
                $this->assign('url','adminIndex?type_link=small&handle=admin');
                $this->display('information');
            }else{
                $this->assign('hint','出现未知错误！');
                $this->assign('url','adminIndex?type_link=small&handle=admin');
                $this->display('information');
            }
        }
    }

    public function checkEnv(){
        if($_SESSION['username']!=$_SESSION['MR'] and $_SESSION['userpwd']!=$_SESSION('MRKJ')){	//判断用户名和密码是否正确
            $this->assign('hint','您不是权限用户');
            $this->assign('url','__URL__/');
            $this->display('information');
            $login=false;
        }else{
            $login=true;
        }
        return $login;		//返回判断结果
    }

    public function uploadimage(){

        //demo 上传文件测试
        $img  = M("Photo");
        $list = $img->order('id desc')->select();

        $this->assign('list',$list);
        $this->display();
    }

    //------------   处理上传文件 控制器		----------------//
    public function upload(){
        header("content-type:text/html;charset=utf-8");

        $upload = new \Think\Upload(); // 实例化上传类

        $upload->maxSize  	= 1024*1024 ;// 设置附件上传大小 (-1) 是不限值大小
        $upload->allowExts  = array(
            'jpg', 'gif', 'png', 'jpeg'
        );// 设置附件上传类型

        $upload->savePath   = 'Public/thumb/';// 设置附件上传目录

        $upload->replace = true; //存在同名文件是否是覆盖

        // 是否使用子目录保存上传文件
        $upload->autoSub = true;
        // 采用date函数生成命名规则 传入Y-m-d参数
        $upload->saveName = array('date','Y-m-d');
        //如果有多个参数需要传入的话 可以使用数组
        $upload->saveName = array('myFun',array('__FILE__','val1','val2'));

        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息

            // 保存表单数据 包括附件数据
            $up = M("Photo"); // 实例化upload对象
            foreach ($info as $v){

                //缩略图 文件保存地址
                $timage          ="./Uploads/".$v['savepath'].$v['savename'];
                //上传数据库
                $arr['image']		 = $v['savepath'].$v['savename'];//保存图片路径
                $arr['create_time']  = NOW_TIME;//创建时间

                //----- 创建缩略图 -----//
                if ($_POST['thum'] == 1){

                    // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
                    $spath = "./Uploads/".$v['savepath']."s_".$v['savename']; //缩略图名称 地址
                    $this->thumbs($timage,$spath,$_POST['hejpg'],$_POST['wijpg']);
                    $arr['simage'] = $v['savepath']."s_".$v['savename'];//保存缩略图片路径
                }

                //------- 添加水印 ------//
                $pos   = isset($_POST['pos'])?$_POST['pos']:'1';		//默认左上
                $text  = isset($_POST['text'])?$_POST['text']:'think';
                $color = isset($_POST['color'])?$_POST['color']:'#ccc';
                $size  = empty($_POST['size'])?'50':$_POST['size'];

//                if ($_POST['statu'] == 1 && $_POST['text'] != ''){	//文字水印
//
//                    echo "文字水印";
//                    $Image = new \Think\Image();
//                    // 在图片右下角添加水印文字    (入口文件下放置字库文件 1.ttf)
//                    $Image->open($timage)->text($text,'./1.ttf',$size,$color,$pos)->save($timage);
//                }
//                else if($_POST['statu'] == 1 && $_POST['text'] == ''){//图片水印
//                    echo "图片水印";
//                    $this->photowater($timage,$pos);
//                }

                if(!$up->create($arr)){ // 创建数据对象

                    $this->error($up->getError());
                    exit();
                }
                if($up->add() === false){ // 写入用户数据到数据库

                    $this->error('数据保存失败');
                    exit();
                }
            }

            $this->success('数据保存成功',"../",10);
        }

    }

    /*   ---------- 编辑图片 ------------ *
     * 	 $image		原有图片
     * 	 $spath		修改后的编辑图片
     * 	 $height    高度
     * 	 $width     宽度
     * 	 $thumbname 缩略名
     */
    public function thumbs($image,$spath,$height=150,$width=150){ //传入图片

        $Image = new \Think\Image(); // 给avator.jpg 图片添加logo水印
        $Image->open($image);
        // 生成一个固定大小为150*150的缩略图并保存为thumb.jpg
        $Image->thumb($height, $width,\Think\Image::IMAGE_THUMB_FIXED)->save($spath);
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
        //$image->thumb($height,$width)->save($tpath.$thumbname);
        //将图片裁剪为400x400并保存为corp.jpg
        //$image->crop($height,$width)->save('./crop.jpg');
        return  $spath;//时间戳加后缀

    }

    /*	 ----------------  检验 验证码  ----------------- *
     * 	$code 	用户验证码
     */
    public function check_verify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }
    /*   -------------  图片加图片logo水印  ------------- *
     *	$image  操作图片
     * 	$pos 	水印位置
     */
    public function photowater($image,$pos){//重复名字 水印覆盖图片

        $Image = new \Think\Image();
        // 在图片左上角添加水印（水印文件位于./logo.png） 水印图片的透明度为50 并保存为water.jpg (入口文件下放置水印图片)
        $Image->open($image)->water('./logo.png',$pos,80)->save($image);
    }
}
?>