<?php
namespace ocr\baidu;

require_once __DIR__ . '/autoload.php';
use AipOcr;
use AipFace;

/**
 * 百度图片识别 接口类
 * Class Execute
 * @package service\ocr\baidu
 * @author ym
 * @date 2019/3/12
 */
class Execute {

    /** @var 图片识别 */
    private $image;

    /** @var 人脸识别 */
    private $face;

    public function __construct($options = [])
    {
        try {
            $this->image = new AipOcr($options['appid'],$options['secretId'],$options['secretKey']);
            $this->face = new AipFace($options['appid'],$options['secretId'],$options['secretKey']);
        } catch (\Exception $e) {
            return json_encode(array('code'=>'-1','msg'=>'options参数错误'));
        }

    }

    /**
     * @param $img
     * @param string $type 接口类型
     * @return array
     * @throws UserException
     * @author ym
     * @date 2019/3/12
     */
    public function run($data,$type = 'idcard_front'){

        switch ($type)
        {
            case "idcard_back": // 身份证背面
                return json_encode($this->image->idcard($data['img'],'back'));
                break;
            case "bankcard": // 银行卡
                return json_encode($this->image->bankcard($data['img']));
                break;
            case "idcard_front": // 身份证正面
                return json_encode($this->image->idcard($data['img'],'front'));
                break;
            case "driving": //驾驶证识别接口
                return json_encode($this->image->drivingLicense($data['img']));
                break;
            case "business": // 营业制造
                return json_encode($this->image->businessLicense($data['img']));
                break;
            case "face_match":
                return json_encode($this->face->match(array(
                    array(
                        'image' => base64_encode(file_get_contents($data['path'][0])),
                        'image_type' => 'BASE64',
                    ),
                    array(
                        'image' => base64_encode(file_get_contents($data['path'][1])),
                        'image_type' => 'BASE64',
                    ),
                )));
                break;
            default:
                return json_encode(array('code'=>'-1','msg'=>'查询接口不存在'));
        }
    }


}