<?php
namespace app\api\model;


class Textbook extends Base
{
    public function setSuitBids($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function getSuitBids($value)
    {
        return split_int_array($value);
    }

    public function textbookSection()
    {
        return $this->hasMany('TextbookSection','tb_id','tb_id');
    }

    /**
     * 新增一个教材
     * @param $data
     * @return bool
     */
    public function addTextbook($data)
    {
        if (!isset($data['tb_name']) || $data['tb_name'] == ''){
            return $this->input_param_error('tb_name');
        }
        if (!isset($data['tb_org_name']) || $data['tb_org_name'] == ''){
            return $this->input_param_error('tb_org_name');
        }

        $result = $this->isUpdate(false)->allowField(true)->save($data);
        if (false === $result){
            return $this->sql_add_error('textbook');
        }

        return true;
    }

    /**
     * 修改教材信息
     * @param $tb_id
     * @param $data
     */
    public function updateTextbook($tb_id,$data)
    {
        if (!isset($data['tb_name']) || $data['tb_name'] == ''){
            return $this->input_param_error('tb_name');
        }
        if (!isset($data['tb_org_name']) || $data['tb_org_name'] == ''){
            return $this->input_param_error('tb_org_name');
        }

        $m_text_book = $this->get($tb_id);
        if (empty($m_text_book)){
            return $this->user_error('教材不存在');
        }

        $w['tb_id'] = $tb_id;
        $result = $this->allowField(true)->save($data,$w);

        if (false === $result){
            return $this->sql_save_error('textbook');
        }

        return true;
    }

    /**
     * 删除一个教材
     * @param $tb_id
     * @return bool
     */
    public function delOneTextbook($tb_id)
    {
        $m_text_book = $this->get($tb_id);
        if (empty($m_text_book)){
            return $this->user_error('教材不存在');
        }

        $result = $m_text_book->delete();
        if (false === $result){
            return $this->sql_delete_error('textbook');
        }
        $mTbs = new TextbookSection();
        $textbook_section_list = $mTbs->where('tb_id',$tb_id)->select();
        foreach ($textbook_section_list as $textbook_section){
            $result = $textbook_section->delete();
            if (false === $result){
                return $this->sql_delete_error('textbook_section');
            }
        }
        return true;
    }





}