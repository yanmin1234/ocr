<?php
namespace ocr;

class Ocr{

    /**
     * @return array
     * @throws UserException
     * @author 晏敏
     * @date 2019/3/12
     */
    public function run($code,$path,$type = 'idcard_front',$options = array()){

        if (empty($code) || !in_array($code, array('baidu','tenxun','aliyun'))){
            return json_encode(array('code'=>'-1','msg'=>'code错误,请检查code'));
        }

        if (empty($options)){
            return json_encode(array('code'=>'-1','msg'=>'option不能为空'));
        }
        /** img 不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式 */
        try {
            if ($type != 'face_match'){
                $img = file_get_contents($path);
            } else {
                $img = file_get_contents($path[0]);
            }
        } catch (\Exception $e) {
            return json_encode(array('code'=>'-1','msg'=>'图片不存在,请检查路径'));
        }


        /** 检查图片是否存在 */
        /** 去查询数据如果存在返回数据
         * imgId = md5($img)
         * data json
         * type 证件类型 （idcard_back 身份证背面  idcard_front 身份证正面  bankcard 银行卡  driving 驾驶证  business 营业制造）
         * TODO...
         * */


        /** @var 百度云接口 人脸识别和图文识别要申请不同的appid API Key  Secret Key*/
//        $bd_img_options = [  // 图文
//            'appid'     => '你的AppID',
//            'secretId'  => '你的API Key',
//            'secretKey' => '你的Secret Key'
//        ];
//
//        $bd_face_options = [ // 人脸
//            'appid'     => '你的AppID',
//            'secretId'  => '你的API Key',
//            'secretKey' => '你的Secret Key'
//        ];
//        $client = new \service\ocr\baidu\Execute($bd_face_options);


        /** @var 腾讯云接口 $tx_options */
//        $tx_options = [
//            'appid'     => '你的AppID',
//            'secretId'  => '你的SecretId',
//            'secretKey' => '你的SecretKey'
//        ];
//        $client = new \service\ocr\tenxun\Execute($tx_options);



        /** @var 阿里云接口 $al_options */
//        $al_options = [
//            'appcode' => '你的appcode'
//        ];

        require_once __DIR__ .'/'. $code .'/Execute.php';
        $newpath = "\\ocr\\".$code."\\Execute";

        $client = new $newpath($options);
        /** @var 执行接口 $res */
        $res = $client->run(array('img'=>$img,'path'=>$path),$type);

        /** @var 完成后保存数据到数据库
         *  TODO...
         * $data */
        $data = [ // 保存数据
            'img_id' => md5($img),
            'data' => $res
        ];

        return $res;
    }


}