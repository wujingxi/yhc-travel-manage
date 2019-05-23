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
use app\admin\model\User as userModel;//用户模型
use app\admin\model\UserVerify as UserVerifyModel;//用户认证模型
use app\admin\controller\Permissions;
class Subject extends Permissions
{
    /**
     * 科目列表
     * @return [type] [description]
     */
    public function index()
    {
        $subjectInfo = Db::name('system_config')->where('name','system_subject')->find();
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
     * 添加、编辑一级科目
     */
    public function editsubjectone()
    {
    	//获取文件id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $subjectInfo = Db::name('system_config')->where('name','system_subject')->find();
                $sujectArrs =  json_decode($subjectInfo['value']);
                $newArrs = [];
                foreach($sujectArrs as $k=>$v)
                {
                    $newArrs[$k]['id'] = $v->id;
                    $newArrs[$k]['name'] = $v->name;
                    if($id == $v->id)
                    {
                        $newArrs[$k]['name'] = $post['value'];
                    } 
                    $newArrs[$k]['sort'] = $v->sort;
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
                    $newArrs[$k]['sub'] = $subs;
                }               

                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($newArrs);
        		if(false == Db::name('system_config')->where('name','system_subject')->update($data)) {
        			return $this->error('修改失败');
        		} else {
        			return $this->success('修改成功');
        		}
        	} 
        } else {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $subjectInfo = Db::name('system_config')->where('name','system_subject')->find();
                $sujectArrs =  json_decode($subjectInfo['value']);
                $count = count($sujectArrs);
                $sujectArrs[$count]['id'] = $count + 1;
                $sujectArrs[$count]['name'] = $post['value'];
                $sujectArrs[$count]['sort'] = $count + 1;
                $sujectArrs[$count]['sub'] = [];
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($sujectArrs);
        		if(false == Db::name('system_config')->where('name','system_subject')->update($data)) {
        			return $this->error('添加失败');
        		} else {
        			return $this->success('添加成功');
        		}
        	} 
        }
    }

    /**
     * 添加、编辑二级科目
     */
    public function editsubjecttwo()
    {
    	//获取文件id
        $subid = $this->request->has('subid') ? $this->request->param('subid', 0, 'intval') : 0;
        if($subid > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $subjectInfo = Db::name('system_config')->where('name','system_subject')->find();
                $sujectArrs =  json_decode($subjectInfo['value']);
                $newArrs = [];
                foreach($sujectArrs as $k=>$v)
                {
                    $newArrs[$k]['id'] = $v->id;
                    $newArrs[$k]['name'] = $v->name;                    
                    $newArrs[$k]['sort'] = $v->sort;
                    $subs = [];
                    if(!empty($v->sub))
                    {                        
                        foreach($v->sub as $s=>$m)
                        {
                            $subs[$s]['sub_id'] = $m->sub_id;
                            $subs[$s]['pid'] = $m->pid;
                            if($subid == $m->sub_id && $post['pid'] == $m->pid)
                            {
                                $subs[$s]['name'] = $post['value'];
                            } 
                            else
                            {
                                $subs[$s]['name'] = $m->name;
                            }                            
                            $subs[$s]['sort'] = $m->sort;
                        }                        
                    }
                    $newArrs[$k]['sub'] = $subs;
                }               

                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($newArrs);
        		if(false == Db::name('system_config')->where('name','system_subject')->update($data)) {
        			return $this->error('修改失败');
        		} else {
        			return $this->success('修改成功');
        		}
        	} 
        } else {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $subjectInfo = Db::name('system_config')->where('name','system_subject')->find();
                $sujectArrs =  json_decode($subjectInfo['value']);
                $newArrs = [];
                foreach($sujectArrs as $k=>$v)
                {
                    $newArrs[$k]['id'] = $v->id;
                    $newArrs[$k]['name'] = $v->name;                    
                    $newArrs[$k]['sort'] = $v->sort;
                    $subs = [];
                    if(!empty($v->sub))
                    {                        
                        foreach($v->sub as $s=>$m)
                        {
                            $subs[$s]['sub_id'] = $m->sub_id;
                            $subs[$s]['pid'] = $m->pid;
                            $subs[$s]['name'] = $m->name;
                            if($subid == $m->sub_id && $post['pid'] == $m->pid)
                            {
                                $subs[$s]['name'] = $post['value'];
                            } 
                            else
                            {
                                $subs[$s]['name'] = $m->name;
                            }                            
                            $subs[$s]['sort'] = $m->sort;
                        }                        
                    }
                    if($post['pid'] == $m->pid)
                    {
                        $subcount = count($subs);
                        $subs[$subcount]['sub_id'] = $subcount + 1;
                        $subs[$subcount]['pid'] = $v->id;
                        $subs[$subcount]['name'] = $post['value'];
                        $subs[$subcount]['sort'] = $subcount + 1;     
                    }                                   
                    $newArrs[$k]['sub'] = $subs;
                }      
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($newArrs);
        		if(false == Db::name('system_config')->where('name','system_subject')->update($data)) {
        			return $this->error('添加失败');
        		} else {
        			return $this->success('添加成功');
        		}
        	} 
        }
    }

    /**
     * 删除科目
     */
    public function delsubject()
    {
    	//获取文件id
        $pid = $this->request->has('pid') ? $this->request->param('pid', 0, 'intval') : 0;
        if($pid > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
                $post = $this->request->post();
                $subjectInfo = Db::name('system_config')->where('name','system_subject')->find();
                $sujectArrs =  json_decode($subjectInfo['value']);
                $newArrs = [];
                $idx = 0;
                foreach($sujectArrs as $k=>$v)
                {    
                    //删除一级
                    if($post['subid'] == 0) 
                    {                           
                        if($pid != $v->id)
                        {
                            $newArrs[$idx]['id'] = $v->id;
                            $newArrs[$idx]['name'] = $v->name;                    
                            $newArrs[$idx]['sort'] = $v->sort;
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
                            $newArrs[$idx]['sub'] = $subs;
                            $idx++;  
                        }
                    }   
                    else
                    {
                        $newArrs[$idx]['id'] = $v->id;
                        $newArrs[$idx]['name'] = $v->name;                    
                        $newArrs[$idx]['sort'] = $v->sort;
                        $subs = [];
                        if(!empty($v->sub))
                        {            
                            $sidx = 0;            
                            foreach($v->sub as $s=>$m)
                            {                       
                                if($pid == $m->pid)         
                                {
                                    if($post['subid'] != $m->sub_id)
                                    {                                        
                                        $subs[$sidx]['sub_id'] = $m->sub_id;
                                        $subs[$sidx]['pid'] = $m->pid;
                                        $subs[$sidx]['name'] = $m->name;
                                        $subs[$sidx]['sort'] = $m->sort;
                                        $sidx++;  
                                    } 
                                }
                                else
                                {
                                    $subs[$s]['sub_id'] = $m->sub_id;
                                    $subs[$s]['pid'] = $m->pid;
                                    $subs[$s]['name'] = $m->name;                         
                                    $subs[$s]['sort'] = $m->sort;
                                }                                                        
                                
                            }                        
                        }
                        $newArrs[$idx]['sub'] = $subs;
                        $idx++;  
                    }          
                }
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                $data['value'] = json_encode($newArrs);
        		if(false == Db::name('system_config')->where('name','system_subject')->update($data)) {
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
