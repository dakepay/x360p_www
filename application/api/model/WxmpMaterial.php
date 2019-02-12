<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/19
 * Time: 12:18
 */
namespace app\api\model;

use app\common\Wechat;
use think\Exception;
use think\Log;

class WxmpMaterial extends Base
{
    protected $wxapp;

    protected $material;

    protected $material_type = ['image', 'voice', 'video', 'news'];

    protected $material_pagesize = 25;

    protected $wechat_material_media_id = [];

    protected static $appid = null;

    protected $skip_og_id_condition = true;

    public function items()
    {
        return $this->hasMany('WxmpMaterialNews', 'material_id', 'material_id')->order('displayorder', 'asc');
    }

    public function sync_material($type, $appid)
    {
        self::$appid = $appid;
        $this->wxapp = Wechat::getApp($appid);
        $this->material = $this->wxapp->material;
        $stats = $this->material->stats();//获取素材的统计信息
        $w = [];
        $w['status'] = 0;
        $w['authorizer_appid'] = $appid;
        $wxmp = Wxmp::get($w);
        if (empty($wxmp)) {
            throw new Exception('invalid appid：' . $appid);
        }
        $wxmp_id = $wxmp['wxmp_id'];

        if (!empty($type) && in_array($type, $this->material_type)) {
            $method = 'sync_' . $type;
            $count = $type . '_count';
            $this->$method($wxmp_id, $stats[$count]);
        } else {
            $this->error = 'invalid parameter type';
            return false;
        }
//        foreach ($this->material_type as $type) {
//            $method = 'sync_' . $type;
//            $count = $type . '_count';
//            $this->$method($wxmp_id, $stats[$count]);
//        }
        $this->delete_expired_materials($wxmp_id, $type);
        return true;
    }

    public function delete_expired_materials($wxmp_id, $type)
    {
        $where = [];
        $where['wxmp_id'] = $wxmp_id;
        $where['type'] = $type;
        $local_media_ids = WxmpMaterial::where($where)->column('media_id');
        $diff = array_diff($local_media_ids, $this->wechat_material_media_id);
        if ($diff) {
            $material_ids = WxmpMaterial::whereIn('media_id', $diff)->column('material_id');
            WxmpMaterial::destroy($material_ids, true);
            WxmpMaterialNews::destroy(function ($query) use ($material_ids) {
                $query->whereIn('material_id', $material_ids);
            }, true);
        }
    }

    protected function sync_image($wxmp_id, $count)
    {
        $lists = $this->generator('image', $count);

        foreach ($lists as $list) {
            if (empty($list['item'])) {
                continue;
            }
            foreach ($list['item'] as $item) {
                $where = [];
                $where['type'] = 'image';
                $where['media_id'] = $item['media_id'];
                $this->wechat_material_media_id[] = $item['media_id'];
                $material = WxmpMaterial::get($where);

                $data = [];
                $data['type'] = 'image';
                $data['media_id'] = $item['media_id'];
                $data['url']  = $item['url'];
                $data['name'] = $item['name'];
                $data['model']   = 'wechat';
                $data['wxmp_id'] = $wxmp_id;
                $data['appid']  = self::$appid;
                $data['update_time'] = $item['update_time'];
                if ($material) {
                    $material->save($data);
                    continue;
                } else {
                    WxmpMaterial::create($data);
                }
            }
        }

    }

    protected function sync_voice($wxmp_id, $count)
    {
        $lists = $this->generator('voice', $count);

        foreach ($lists as $list) {
            if (empty($list['item'])) {
                continue;
            }
            foreach ($list['item'] as $item) {
                $where = [];
                $where['type'] = 'voice';
                $where['media_id'] = $item['media_id'];
                $this->wechat_material_media_id[] = $item['media_id'];
                $material = WxmpMaterial::get($where);

                $data = [];
                $data['type'] = 'voice';
                $data['media_id'] = $item['media_id'];
                $data['name']    = $item['name'];
                $data['model']   = 'wechat';
                $data['wxmp_id'] = $wxmp_id;
                $data['appid']   = self::$appid;
                $data['update_time'] = $item['update_time'];
                if ($material) {
                    $material->save($data);
                    continue;
                } else {
                    WxmpMaterial::create($data);
                }
            }
        }
    }

    protected function sync_video($wxmp_id, $count)
    {
        $lists = $this->generator('video', $count);

        foreach ($lists as $list) {
            if (empty($list['item'])) {
                continue;
            }
            foreach ($list['item'] as $item) {
                $where = [];
                $where['type'] = 'video';
                $where['media_id'] = $item['media_id'];
                $this->wechat_material_media_id[] = $item['media_id'];
                $material = WxmpMaterial::get($where);

                $data = [];
                $data['type'] = 'video';
                $data['media_id'] = $item['media_id'];
                $data['name']    = $item['name'];
                $data['model']   = 'wechat';
                $data['wxmp_id'] = $wxmp_id;
                $data['appid']   = self::$appid;
                $data['update_time'] = $item['update_time'];
                if ($material) {
                    $material->save($data);
                    continue;
                } else {
                    $video_info = $this->material->get($item['media_id']);
                    $data['url'] = $video_info['down_url'];
                    WxmpMaterial::create($data);
                }
            }
        }
    }

    protected function sync_news($wxmp_id, $count)
    {
        $lists = $this->generator('news', $count);

        foreach ($lists as $list) {
            foreach ($list['item'] as $item) {
                if (empty($list['item'])) {
                    continue;
                }
                $data = [];
                $data['type'] = 'news';
                $data['media_id'] = $item['media_id'];
                $this->wechat_material_media_id[] = $item['media_id'];
                $material = WxmpMaterial::get($data);
                if ($material) {
                    WxmpMaterialNews::destroy(['material_id' => $material['material_id']], true);
                } else {
                    $data['model']   = 'wechat';
                    $data['wxmp_id'] = $wxmp_id;
                    $data['appid']   = self::$appid;
                    $data['update_time'] = $item['update_time'];
                    $material = WxmpMaterial::create($data);
                }

                $news_data = $item['content']['news_item'];
                foreach ($news_data as $key => &$value) {
                    $value['displayorder'] = ++$key;
                    $value['wxmp_id']      = $wxmp_id;
                    $value['material_id']  = $material['material_id'];
                    $value['create_time'] = $item['content']['create_time'];
                    $value['update_time'] = $item['content']['update_time'];
                }
                (new WxmpMaterialNews)->allowField(true)->saveAll($news_data);
            }
        }
    }

    protected function generator($type, $total_count)
    {
        $pagesize = $this->material_pagesize;
        for ($offset = 0; $total_count > $offset; $offset += $pagesize){
            yield $this->material->lists($type, $offset, $pagesize);
        }
    }

    public function delete_material()
    {
        $media_id = $this->getData('media_id');
        $wxmp_id  = $this->getData('wxmp_id');//todo
        $material_id = $this->getData('material_id');
        $material = Wechat::getApp()->material;
        try {
            $material->delete($media_id);
            $this->delete(true);
            WxmpMaterialNews::destroy(['material_id' => $material_id], true);
        } catch (\Exception $exception) {
            $this->user_error($exception->getMessage());
            return false;
        }
        return true;
    }
}