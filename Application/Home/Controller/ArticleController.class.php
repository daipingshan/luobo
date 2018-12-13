<?php


namespace Home\Controller;



class ArticleController extends CommonController
{
    public $checkUser = false;
    public function detail(){
        $id = I('get.id',null,'int');
        if($id == null){

        }else{
            $item = M('article')->where('id = '.$id)->find();
            if(!$item){
                $item['title'] = '没有找到这篇文章';
            }
            $this->assign('title',$item['title']);
            $this->assign('item',$item);
            $this->display();
        }
    }


}