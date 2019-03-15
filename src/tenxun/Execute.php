<?php
namespace ocr\tenxun;


require_once __DIR__ . '/autoload.php';
use QcloudImage\CIClient;

/**
 * 腾讯图片识别 接口类
 * Class Execute
 * @package service\ocr\tenxun
 * @author ym
 * @date 2019/3/12
 */
class Execute {
    /** @var string APPID */
    private $appid = '';

    /** @var string SecretId */
    private $secretId = '';

    /** @var string SecretKey */
    private $secretKey = '';

    /** @var string BUCKET 为历史遗留字段，无需修改。 */
    private $bucket = 'YOUR_BUCKET';

    public function __construct($options = [])
    {
        $options['appid'] ? $this->appid = $options['appid'] : false;
        $options['secretId'] ? $this->secretId = $options['secretId'] : false;
        $options['secretKey'] ? $this->secretKey = $options['secretKey'] : false;
        $this->bucket = 'YOUR_BUCKET';

    }


    public function run($data,$type = 'idcard_front'){

        $client = new CIClient($this->appid, $this->secretId, $this->secretKey, $this->bucket);

        //推荐使用https
        $client->useHttps();

        // 设置超时
        $client->setTimeout(30);

        // 选择服务器域名, 推荐使用新域名 useNewDomain ( recognition.image.myqcloud.com )
        //
        // 如果你:
        //      1.正在使用人脸识别系列功能( https://cloud.tencent.com/product/FaceRecognition/developer )
        //      2.并且是通过旧域名访问的
        // 那么: 请继续使用旧域名
        $client->useNewDomain();

        switch ($type)
        {
            case "idcard_back":
                return $client->idcardDetect(array('buffers'=>array($data['img'])), 1/*0为正面,1为反面*/);
                break;
            case "bankcard":
                return $client->bankcard(array('buffer'=>$data['img']));
                break;
            case "idcard_front":
                return $client->idcardDetect(array('buffers'=>array($data['img'])), 0/*0为正面,1为反面*/);
                break;
            case "driving": //驾驶证识别接口
                return $client->drivingLicence(array('buffer'=>$data['img']),1/*0表示行驶证，1表示驾驶证*/);
                break;
            case "business":
                return $client->bizlicense(array('buffer'=>$data['img']));
                break;
            case "face_match":
                return $client->faceCompare( array('buffer'=>file_get_contents($data['path'][0])), array('buffer'=>file_get_contents($data['path'][1])));
                break;
            default:
                return json_encode(array('code'=>'-1','msg'=>'查询接口不存在'));
        }
    }

}