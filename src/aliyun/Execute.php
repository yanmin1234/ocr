<?php
/**
 * Created by PhpStorm.
 * User: liuliping
 * Date: 2019/3/13
 * Time: 13:26
 */
namespace ocr\aliyun;

class Execute {

    /** @var string SecretKey */
    private $appcode = '';

    public function __construct($options = [])
    {
        $options['appcode'] ? $this->appcode = $options['appcode'] : false;

    }


    public function run($data,$type = 'idcard_front'){

        if ($type != 'face_match'){
            if($fp = fopen($data['path'], "rb", 0)) {
                $binary = fread($fp, filesize($data['path'])); // 文件读取
                fclose($fp);
                $base64 = base64_encode($binary); // 转码
            }
        }

        switch ($type)
        {
            case "idcard_back":
                return $this->idCard($base64,array("side" => "back"));
                break;
            case "bankcard":
                return $this->bankcard($base64);
                break;
            case "idcard_front":
                return $this->idCard($base64,array("side" => "face"));
                break;
            case "driving": //驾驶证识别接口
                return $this->drivingLicence($base64,array("side" => "face"));
                break;
            case "business":
                return $this->bizlicense($base64);
                break;
            case "face_match":
                return $this->faceCompare($data['path']);
                break;
            default:
                return json_encode(array('code'=>'-1','msg'=>'查询接口不存在'));
        }
    }


    /**
     * 身份证
     * @param $image
     * @param array $config
     * @return bool|string
     * @author ym
     * @date 2019/3/13
     */
    private function idCard($image,$config = array()){
        $url = "https://dm-51.data.aliyun.com/rest/160601/ocr/ocr_idcard.json";

        $request = array(
            "image" => "$image"
        );
        if(count($config) > 0){
            $request["configure"] = json_encode($config);
        }

        $body = json_encode($request);

        return $this->_http($url,'POST',$body);
    }


    /**
     * 身份证
     * @param $image
     * @param array $config
     * @return bool|string
     * @author ym
     * @date 2019/3/13
     */
    private function bankcard($image,$config = array()){
        $url = "https://yhk.market.alicloudapi.com/rest/160601/ocr/ocr_bank_card.json";

        $request = array(
            "image" => "$image"
        );
        if(count($config) > 0){
            $request["configure"] = json_encode($config);
        }
        $body = json_encode($request);

        return $this->_http($url,'POST',$body);
    }


    private function drivingLicence($image,$config = array()){
        $url = "https://dm-52.data.aliyun.com/rest/160601/ocr/ocr_driver_license.json";

        $request = array(
            "image" => "$image"
        );
        if(count($config) > 0){
            $request["configure"] = json_encode($config);
        }
        $body = json_encode($request);

        return $this->_http($url,'POST',$body);
    }


    private function bizlicense($image,$config = array()){
        $url = "https://dm-58.data.aliyun.com/rest/160601/ocr/ocr_business_license.json";

        $request = array(
            "image" => "$image"
        );
        if(count($config) > 0){
            $request["configure"] = json_encode($config);
        }
        $body = json_encode($request);

        return $this->_http($url,'POST',$body);
    }


    private function faceCompare($image = array(),$config = array()){

        $bodys = '';
        foreach ($image as $key=>$val){
            if($fp = fopen($val, "rb", 0)) {
                $binary = fread($fp, filesize($val)); // 文件读取
                fclose($fp);
                $bodys .= "&srcb=".base64_encode($binary); // 转码
            }
        }

        $url = "https://face.xiaohuaerai.com/face";

        $body = substr($bodys,1);

        return $this->_http($url,'POST',$body);
    }


    private function _http($url,$method = 'GET',$data = array()){
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$url, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader = substr($result, 0, $header_size);
        $rbody = substr($result, $header_size);

        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);

        return $rbody;

    }
}
