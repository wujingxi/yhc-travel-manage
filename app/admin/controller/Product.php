<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------


namespace app\admin\controller;

use \think\Db;
use \think\Cookie;
use \think\Session;
use app\admin\model\ProductAttribute as ProductAttributeModel;//产品属性模型
use app\admin\controller\Permissions;
/**
 * 产品模块
 */
class Product extends Permissions
{
    /**
     * 产品属性设置
     */
    public function attrset()
    {
        $prolist = Db::name('product_attribute')->field('id,attr_name,attr_type')->where('pid','0')->select();
        $data = [];
        foreach($prolist as $k=>$v)
        {
            $subs = Db::name('product_attribute')->field('id,attr_name,attr_type,pid')->where('pid',$v['id'])->select();            
            $prolist[$k]['sub'] = $subs;
        }
        $this->assign('prolist',$prolist);
        return $this->fetch();
    }
    /**
     * 产品管理列表
     * @return [type] [description]
     */
    public function index()
    {
        $subjectInfo = Db::name('product')->where('name','system_subject')->find();
        $sujectArrs =  json_decode($subjectInfo['value']);
        $data = [];
        foreach($sujectArrs as $k=>$v)
        {
            $data[$k]['id'] = $v->id;
            $data[$k]['name'] = $v->name;
            $data[$k]['sort'] = $v->sort;
            $subs = [];
            if(!empty($v->sub))
            {                
                foreach($v->sub as $s=>$m)
                {
                    $subs[$s]['sub_id'] = $m->sub_id;
                    $subs[$s]['pid'] = $m->pid;
                    $subs[$s]['name'] = $m->name;
                    $subs[$s]['sort'] = $m->sort;
                }                
            }
            $data[$k]['sub'] = $subs;
        }
        //print_r($data); exit;
        $this->assign('sujectArrs',$data);
        return $this->fetch();
    }

    
    /**
     * 添加、编辑产品属性
     */
    public function editproduct()
    {
        $productAttributeModel = new ProductAttributeModel();
    	//获取文件id
        $subid = $this->request->has('subid') ? $this->request->param('subid', 0, 'intval') : 0;
        if($subid > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['attr_name'] = $post['name'];
        		if(false == $productAttributeModel->where('id',$subid)->update($data)) {
        			return $this->error('修改失败');
        		} else {
        			return $this->success('修改成功');
        		}
        	} 
        } else {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $data['create_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $parentInfo = $productAttributeModel->where('id',$post['pid'])->find();   
                $data['pid'] = $post['pid'];
                $data['attr_type'] = $parentInfo['attr_type'];
                $data['attr_name'] = $post['name'];
        		if(false == $productAttributeModel->allowField(true)->save($data)) {
        			return $this->error('添加失败');
        		} else {
        			return $this->success('添加成功');
        		}
        	} 
        }
    }

    /**
     * 删除属性
     */
    public function delsubject()
    {
    	//获取文件id
        $pid = $this->request->has('pid') ? $this->request->param('pid', 0, 'intval') : 0;
        $sub_id = $this->request->has('subid') ? $this->request->param('subid', 0, 'intval') : 0;
        if($sub_id > 0) {
        	if($this->request->isPost()) {
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['is_del'] = 1;
        		if(false == $productAttributeModel->where('id',$sub_id)->update($data)) {
        			return $this->error('删除失败');
        		} else {
        			return $this->success('删除成功');
        		}
        	} 
        } else {
        	return $this->error('id不正确');
        }
    }

    /**
     * 职称列表
     * @return [type] [description]
     */
    public function professional()
    {
        $data = $this->GetProfessionalValueReturnArrs();
        $this->assign('professionalArrs',$data);
        return $this->fetch();
    }
    
    private function GetProfessionalValueReturnArrs()
    {
        $professionalInfo = Db::name('system_config')->where('name','expert_professional')->find();
        $professionalArrs =  json_decode($professionalInfo['value']);
        $data = [];
        foreach($professionalArrs as $k=>$v)
        {
            $data[$k]['id'] = $v->id;
            $data[$k]['name'] = $v->name;
            $data[$k]['sort'] = $v->sort;
        }
        return $data;
    }
    /**
     * 职称编辑
     */
    public function editprofessional()
    {
    	//获取文件id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $professionalInfo = Db::name('system_config')->where('name','expert_professional')->find();
                $professionalArrs =  json_decode($professionalInfo['value']);
                $newArrs = [];
                foreach($professionalArrs as $k=>$v)
                {
                    $newArrs[$k]['id'] = $v->id;
                    if($id == $v->id)
                    {
                        $newArrs[$k]['name'] = $post['value'];
                    } 
                    else
                    {
                        $newArrs[$k]['name'] = $v->name;
                    } 
                    $newArrs[$k]['sort'] = $v->sort;
                }

                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($newArrs);
        		if(false == Db::name('system_config')->where('name','expert_professional')->update($data)) {
        			return $this->error('修改失败');
        		} else {
        			return $this->success('修改成功');
        		}
        	} 
        } else {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $professionalInfo = Db::name('system_config')->where('name','expert_professional')->find();
                $professionalArrs =  json_decode($professionalInfo['value']);
                $count = count($professionalArrs);
                $professionalArrs[$count]['id'] = $count + 1;
                $professionalArrs[$count]['name'] = $post['value'];
                $professionalArrs[$count]['sort'] = $count + 1;

                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($professionalArrs);
        		if(false == Db::name('system_config')->where('name','expert_professional')->update($data)) {
        			return $this->error('添加失败');
        		} else {
        			return $this->success('添加成功');
        		}
        	} 
        }
    }
    /**
     * 职称删除
     */
    public function delprofessional()
    {
    	//获取文件id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $professionalInfo = Db::name('system_config')->where('name','expert_professional')->find();
                $professionalArrs =  json_decode($professionalInfo['value']);
                $newArrs = [];
                $idx = 0;
                foreach($professionalArrs as $k=>$v)
                {                    
                    if($id != $v->id)
                    {
                        $newArrs[$idx]['id'] = $v->id;
                        $newArrs[$idx]['name'] = $v->name;
                        $newArrs[$idx]['sort'] = $v->sort;
                        $idx++;  
                    }  
                                    
                }
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($newArrs);
        		if(false == Db::name('system_config')->where('name','expert_professional')->update($data)) {
        			return $this->error('删除失败');
        		} else {
        			return $this->success('删除成功');
        		}
        	} 
        } else {
        	return $this->error('id不正确');
        }
    }


}
