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
class User extends Permissions
{
    /**
     * 用户列表
     * @return [type] [description]
     */
    public function index()
    {
        //实例化管理员模型
        $model = new userModel();

        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['nickname'] = ['like', '%' . $post['keywords'] . '%'];
        }
       
 
        if(isset($post['reg_time']) and !empty($post['reg_time'])) {
            $max_time = date("Y-m-d",strtotime("+1 day"));
            $where['reg_time'] = [['>=',$post['reg_time']],['<=',$max_time]];
        }
        
        $user = empty($where) ? $model->order('reg_time desc')->paginate(10) : $model->where($where)->order('reg_time desc')->paginate(10,false,['query'=>$this->request->param()]);
        
        $this->assign('user',$user);
        return $this->fetch();
    }
    
    /**
     * 企业列表
     */
    public function qiye()
    {
        //实例化管理员模型
        $model = new UserVerifyModel();
        $where['user_type'] = '企业';
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['name|contact_name'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['status']) and !empty($post['status'])) {
            $where['status'] = $post['status'];
        }
 
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $max_time = date("Y-m-d",strtotime("+1 day"));
            $where['create_time'] = [['>=',$post['create_time']],['<=',$max_time]];
        }
        
        $user = empty($where) ? $model->order('create_time desc')->paginate(10) : $model->where($where)->order('create_time desc')->paginate(10,false,['query'=>$this->request->param()]);
        
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 专家列表
     */
    public function zhuanjia()
    {
        //实例化管理员模型
        $model = new UserVerifyModel();
        $where['user_type'] = '专家';
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['name|contact_name'] = ['like', '%' . $post['keywords'] . '%'];
        }
       
        if (isset($post['status']) and !empty($post['status'])) {
            $where['status'] = $post['status'];
        }
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $max_time = date("Y-m-d",strtotime("+1 day"));
            $where['create_time'] = [['>=',$post['create_time']],['<=',$max_time]];
        }
        
        $user = empty($where) ? $model->order('create_time desc')->paginate(10) : $model->where($where)->order('create_time desc')->paginate(10,false,['query'=>$this->request->param()]);
        
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 企业或专家审核
     */
    public function audit()
    {
    	//获取文件id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id > 0) {
        	if($this->request->isPost()) {
        		//是提交操作
        		$post = $this->request->post();
                $data['update_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                if($post['type'] == 'refuse')
                {
                    $data['status'] = '审核拒绝';
                    $data['refused_reason'] = $post['reason'];
                    $data['refused_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                }
                else{
                    $data['status'] = '审核通过';
                }
        		if(false == Db::name('user_verify')->where('id',$id)->update($data)) {
        			return $this->error('审核提交失败');
        		} else {
        			return $this->success('审核提交成功');
        		}
        	} 
        } else {
        	return $this->error('id不正确');
        }
    }

    public function getzizhiimgs()
    {
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id > 0) {
            $verifyInfo = Db::name('user_verify')->where('id',$id)->find();
            $zizhi_cert =  json_decode($verifyInfo['zizhi_cert']);
            $imgsArr = [];
            foreach($zizhi_cert as $k=>$v)
            {
                $imgsArr[$k]['alt'] = '';
                $imgsArr[$k]['pid'] = $k + 1;
                $imgsArr[$k]['src'] = $v;
                $imgsArr[$k]['thumb'] = $v;
                
            }
            //var_dump($imgsArr);
            //exit;
            $title = "专家".$verifyInfo['name'].'的资质证书';            
            $data = array(
                "title"=>$title,
                "id"=>$id,
                "start"=>0,
                "data"=>$imgsArr
            );
            echo json_encode($data);
        } else {
        	return $this->error('id不正确');
        }
    }

}
