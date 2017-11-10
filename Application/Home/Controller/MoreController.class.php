<?php
class MoreAction extends Action{
    public function index(){
        $type=$_GET['link_id'];             //获取超级链接传递的ID值
        $ele=M('elementarytype');           //实例化模型类
        $eledata=$ele->where('middleid='.$type)->select();  //根据超级链接传递的ID值执行查询语句
        $com=M('common');                   //实例化模型类
        $result=array();                    //定义新数组
        for($i=0; $i<=count($eledata);$i++){                //循环读取初级类别中的数据
            $search=$eledata[$i]['id'];                     //获取初级类别的ID
            $result[]=$eledata[$i]['ChineseName'];          //将初级类别的名称存储到数组中
            $lis=$com->where('elementaryid='.$search)->select();    //根据初级类别的ID，从common表中查询出数据
            $result[]=$lis;                                 //将查询结果存储到数组中
        }
        $this->assign('listdata',$result);                  //将数组赋给模板变量
        $this->display('index');                            //指定模板页
    }
    public function clime(){
        $type=$_GET['link_id'];             //获取超级链接传递的ID值
        $high=M('common');          //实例化模型类
        $highdata=$high->where('highid='.$type)->select();  //根据超级链接传递的ID值执行查询语句
        $this->assign('listdata',$highdata);                    //将数组赋给模板变量
        $this->display('clime');                            //指定模板页
    }
    public function city(){
        $type=$_GET['link_id'];             //获取超级链接传递的ID值
        $ele=M('elementarytype');           //实例化模型类
        $eledata=$ele->where('middleid='.$type)->select();  //根据超级链接传递的ID值执行查询语句
        $com=M('common');                   //实例化模型类
        $result=array();                    //定义新数组
        for($i=0; $i<=count($eledata);$i++){                //循环读取初级类别中的数据
            $search=$eledata[$i]['id'];                     //获取初级类别的ID
            $result[]=$eledata[$i]['ChineseName'];          //将初级类别的名称存储到数组中
            $lis=$com->where('elementaryid='.$search)->select();    //根据初级类别的ID，从common表中查询出数据
            $result[]=$lis;                                 //将查询结果存储到数组中
        }
        $this->assign('listdata',$result);                  //将数组赋给模板变量
        $this->display('city');                         //指定模板页
    }
}
?>