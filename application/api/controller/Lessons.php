<?php
/**
 * Author: luo
 * Time: 2017-10-17 16:23
**/

namespace app\api\controller;

use app\api\model\Lesson;
use app\api\model\LessonMaterial;
use think\Request;
use app\api\model\Lesson as LessonModel;
use app\api\model\Chapter;
use app\api\model\Attachment;

/**
 * Class Lessons
 * @title 课程管理接口
 * @url lessons
 * @desc  课程的添加、编辑、删除
 * @version 1.0
 * @readme
 */
class Lessons extends Base
{

    public function post(Request $request)
    {
        $input = $request->post();
        $checkRet = $this->validate($input, 'Lesson');
        if (true !== $checkRet) {
            return $this->sendError(400, $checkRet);
        }

        $result = model('Lesson')->addLesson($input);
        if (!$result) {
            return $this->sendError(400, model('Lesson')->getError());
        }
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $lid = $request->param('id/d');
        $lesson = model('Lesson')->find($lid);
        if (!$lesson) {
            return $this->sendError(400, '该课程不在或已删除');
        }
        $input = $request->put();
        $checkRet = $this->validate($input, 'Lesson');
        if (true !== $checkRet) {
            return $this->sendError(400, $checkRet);
        }
        $result = $lesson->editLesson($input);
        if (!$result) {
            return $this->sendError(400, $lesson->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @title 获取课程信息
     * @desc  根据条件获取单个课程信息
     * @url lessons/:id
     * @method GET
     */
    protected function get_detail(Request $request, $id = 0){
        //$data = m('Lesson')
        //    ->with(['attachments'])
        //    ->with(['goods' => function ($query) {
        //        $query->where('status', 1);
        //    }])
        //    ->with(['chapters.attachments'])
        //    ->withCount(['classes' => function ($query) {
        //        $query->where('status', 1);
        //    }])
        //    ->findOrFail($id);

        $m_lesson = new Lesson();
        $data = $m_lesson->where('lid', $id)->with('lessonMaterial')->find();
        $data = $data->toArray();

        return $this->sendSuccess($data);
    }

    /**
     * @title 获取所有课程列表
     * @desc  根据条件获取课程列表
     * @url lessons
     * @method GET
     * @return  返回字段描述
     * @readme 详细说明
     */
    protected function get_list(Request $request)
    {
        $input = $request->param();
        unset($input['bids']);
        if(isset($input['og_id'])) {
            gvar('og_id', $input['og_id']);
        }
        $input['bid'] = -1;
        $model = model('Lesson');
        $result = $model->scope('bids')->with(['chapters', 'lessonMaterial'])->getSearchResult($input);

        return $this->sendSuccess($result);
    }

    /**
     * @title 删除课程
     * @desc  根据课程ID删除一个课程
     * @url lessons/:id
     * @method  DELETE
     * @return  返回字段描述
     * @return [type] [description]
     */
    public function delete(Request $request)
    {
        $id = input('id');

        $lesson = LessonModel::get($id);
        if (!$lesson) {
            return $this->sendError(400, '该课程不存在或已删除');
        }
        $result = $lesson->deleteLesson();
        if ($result === false) {
            return $this->sendError(400, $lesson->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @title 获取课程的章节列表
     * @desc  根据课程ID获取课程的章节列表
     * @url /api/lessons/:id/chapters
     * @method  GET
     * @return  返回字段描述
     * @return [type] [description]
     */
    public function get_list_chapters(Request $request)
    {
        $id = $request->param('id');
        $chapters = Chapter::field('is_delete,delete_time,delete_uid', 'true')
            ->order('chapter_index', 'asc')
            ->where('lid', $id)
            ->with(['chapterAttachments'])
            ->select();
        foreach ($chapters as &$chapter) {
            $temp = [];
            foreach ($chapter['chapter_attachments'] as $item) {
                $temp[] = $item['la_type'];
            }
            $chapter->la_types = $temp;
        }

        return $this->sendSuccess($chapters);
    }

    /**
     * 获取课程的班级列表
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_list_classes(Request $request)
    {
        $lid = $request->param('id');
        $input = [];
        if($lid > 0){
            $input['lid'] = $lid;
        }
        $result = model('Lesson')->scope('bids')->getSearchResult($input,[],false);
        if(!empty($result['list'])){
            $bids = $request->header('x-bid');
            $bids = explode(',',$bids);
            $w_class['bid'] = ['in',$bids];
            foreach($result['list'] as $k=>$r){
                $w_class['lid'] = $r['lid'];
                $class_list = get_table_list('class',$w_class);
                $result['list'][$k]['classes'] = $class_list;
            }
        }
        return $this->sendSuccess($result);
    }

    public function get_list_attachments(Request $request)
    {
        $id = $request->param('id');
        $lesson = Lesson::get($id);
        $ret['list'] = $lesson['attachments'];
        return $this->sendSuccess($ret);
    }

    public function post_attachments(Request $request)
    {
        $input = $request->param();
        $la_types = config('lesson.attachment_type');
        $attach = new Attachment();
        if (empty($input['la_type'])) {
            $result = $attach->addGeneralAttach($input);
        } elseif (array_key_exists($input['la_type'] - 1, $la_types)) {
            $result = $attach->addStdAttach($input);
        } else {
            return $this->sendError(400, '上传附件类型不符合lesson.php配置文件的要求');
        }
        if (!$result) {
            return $this->sendError(400, $attach->getError());
        }
        return $this->sendSuccess($attach);
    }

    public function post_chapters(Request $request)
    {
        $input = input();
        $input['lid'] = $input['id'];
        $lesson = Lesson::get($input['id']);
        if (!$lesson) {
            return $this->sendError(400, '课程ID非法或课程已被删除');
        }
        if ($input['chapter_index'] > $lesson->chapter_nums) {
            return $this->sendError(400, '章节数量已超过课程所规定的章节数目');
        }
        $chapter = new Chapter();
        $indexes = $chapter->where('lid', $input['lid'])->column('chapter_index');
        if (in_array($input['chapter_index'], $indexes)) {
            return $this->sendError(400, '章节序号不能重复');
        }

        $result = $this->validate($input, 'LessonChapter');
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $result = $chapter->allowField(true)->isUpdate(false)->save($input);
        if (!$result) {
            return $this->sendError(400, $chapter->getError());
        }
        $chapter = $chapter->toArray();
        $chapter['chapter_attachments'] = [];
        return $this->sendSuccess($chapter);
    }

    public function delete_chapters(Request $request)
    {
        $lc_id = $request->param('subid/d');
        $lid = $request->param('id/d');
        $chapter = Chapter::get(function($query) use ($lc_id, $lid) {
            $query->where('lc_id', $lc_id)->where('lid', $lid);
        });
        if (!$chapter) {
            return $this->sendError(400, '该章节不存在或章节ID与课程ID不匹配.');
        }
        //todo
        foreach ($chapter->attachments as $attach) {
            $attach->delete();
        }
        $chapter->delete();
        return $this->sendSuccess();
    }

    //解除所有绑定的物品
    public function delete_materials(Request $request)
    {
        $lid = input('id');
        $rs = LessonMaterial::destroy(['lid' => $lid], true);
        if($rs === false) return $this->sendError(400,'解除失败');

        return $this->sendSuccess();
    }

    /**
     * 参数规则
     * @name 字段名称
     * @type 类型
     * @require 是否必须
     * @default 默认值
     * @desc 说明
     * @range 范围
     * @return array
     */
    public static function getRules()
    {
        $rules = [
            //共用参数
            'all' => [

            ],
            'get' => [
                'page' => ['name' => 'page', 'type' => 'int', 'require' => 'false', 'default' => '1', 'desc' => '当前分页', 'range' => '',],
                'pagesize'	=>['name' => 'pagesize', 'type' => 'int', 'require' => 'false', 'default' => '20', 'desc' => '分页显示条数', 'range' => '']
            ],
            'post' => [
                'lesson_name'       => ['name' => 'lesson_name', 'type' => 'string', 'require' => 'true', 'default' => '', 'desc' => '课程名称', 'range' => '',],
                'parent_lid'        => ['name' => 'parent_lid', 'type' => 'int', 'require' => 'true', 'default' => '0', 'desc' => '父级课程id', 'range' => '',],
                'level'             => ['name' => 'level', 'type' => 'array', 'require' => 'true', 'default' => '', 'desc' => '课程级别范围(1-9级别)', 'range' => '1-9',],
                'fit_age'           => ['name' => 'fit_age', 'type' => 'array', 'require' => 'true', 'default' => '', 'desc' => '适合年龄范围', 'range' => '',],
                'fit_grade'         => ['name' => 'fit_grade', 'type' => 'array', 'require' => 'true', 'default' => '', 'desc' => '适合年级范围', 'range' => '',],
                'ability_ids'       => ['name' => 'ablity_ids', 'type' => 'array|string', 'require' => 'true', 'default' => '', 'desc' => '能力ID（7大能力)', 'range' => '',],
                'product_level'     => ['name' => 'product_level', 'type' => 'int', 'require' => 'true', 'default' => '', 'desc' => '产品等级（１:体验课,2:标准课,3:高端课,4:助力课,5:定制课)', 'range' => '1-5',],
                'short_desc'        => ['name' => 'short_desc', 'type' => 'string', 'require' => 'true', 'default' => '', 'desc' => '简短介绍', 'range' => '',],
                'public_content[content1]'    => ['name' => 'public_content[content1]', 'type' => 'string|json', 'require' => '', 'default' => '', 'desc' => '宣传介绍(JSON格式)', 'range' => '',],
                'public_content[content2]'    => ['name' => 'public_content[content2]', 'type' => 'string|json', 'require' => '', 'default' => '', 'desc' => '宣传介绍(JSON格式)', 'range' => '',],
                'lesson_cover_picture'    => ['name' => 'lesson_cover_picture', 'type' => 'string', 'require' => 'true', 'default' => '', 'desc' => '课程封面图片路径', 'range' => '',],
                'chapter_nums'      => ['name' => 'chapter_nums', 'type' => 'int', 'require' => 'true', 'default' => '', 'desc' => '章节数量', 'range' => '',],
                'unit_price'        => ['name' => 'unit_price', 'type' => 'decimal', 'require' => 'true', 'default' => '', 'desc' => '课时单价', 'range' => '',],
                'std_hours'         => ['name' => 'std_hours', 'type' => 'decimal', 'require' => 'true', 'default' => '', 'desc' => '标准课时数', 'range' => '',],
                'sale_price'        => ['name' => 'sale_price', 'type' => 'decimal', 'require' => 'true', 'default' => '', 'desc' => '课程售价', 'range' => '',],
                'ext_lid'           => ['name' => 'ext_lid', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '外部课程ID(对接浪腾系统)', 'range' => '',],
                'version'           => ['name' => 'version', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '版本号', 'range' => '',],
                'files[][title]'    => ['name' => 'files[][title]', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '上传文件原文件名', 'range' => '',],
                'files[][la_type]'  => ['name' => 'files[][la_type]', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '上传文件类型', 'range' => '',],
                'files[][file_id]'  => ['name' => 'files[][file_id]', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '上传文件id', 'range' => '',],
                'files[][file_url]' => ['name' => 'files[][file_url]', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '上传文件url', 'range' => '',],
                'files[][file_size]'=> ['name' => 'files[][file_size]', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '上传文件大小', 'range' => '',],
                'files[][file_ext]' => ['name' => 'files[][file_ext]', 'type' => 'string', 'require' => '', 'default' => '', 'desc' => '上传文件后缀', 'range' => '',],
            ],
        ];
        //可以合并公共参数
        return $rules;
    }
}