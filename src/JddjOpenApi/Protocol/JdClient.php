<?php
namespace JddjOpenApi\Protocol;
use Exception;
use JddjOpenApi\Api\RequestService;
use JddjOpenApi\Config\Config;
use stdClass;

/**
 * Class JdClient
 */
class JdClient
{
	public $app_key;
    public $app_secret;
    public $token;
    public $api_request_url;

	protected $connectTimeout 	= 3000;
	protected $readTimeout 		= 60000;

    protected $charset 			= "UTF-8";
	protected $apiVersion 		= "2.0";
	protected $sdkVersion 		= "20160801";

    public function __construct($token, Config $config)
    {
        $this->app_key = $config->get_app_key();
        $this->app_secret = $config->get_app_secret();
        $this->api_request_url = $config->get_request_url();
        $this->log = $config->get_log();
        $this->token = $token;
    }

    protected function generateSign($params)
	{
        $stringToBeSigned = $this->paramsToString($params);
		return strtoupper(md5($stringToBeSigned));
	}

	protected function verifySign($params)
	{
        $stringToBeSigned = $this->paramsToString($params);

        $sign = $params["sign"];
        if(!empty($sign)){
        	return $sign == strtoupper(md5($stringToBeSigned));
        }
        return false;
	}

    protected function paramsToString($params)
    {
		ksort($params);

		$sortedString = $this->app_secret;
		foreach ($params as $k => $v)
		{
            $v = (string)$v;
			if("sign" !== $k /*&& strlen($v) > 0*/)
			{
				$sortedString .= "$k$v";
			}
		}

		$sortedString .= $this->app_secret;

        return $sortedString;
    }
	protected function query_str_fetch(array $fields, $encoder="")
	{
	    $qs = array();
	    foreach($fields as $key=>$val){
	        $qs[] = "{$key}=". (function_exists($encoder)? $encoder($val) : $val);
	    }
	    return implode("&",$qs);
	}

    /**
     * @param $url
     * @param null $postFields
     * @return mixed
     * @throws Exception
     */
    public function curl($url, $postFields = null)
	{
	    //var_dump($postFields);die;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ($this->readTimeout) {
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
		}
		if ($this->connectTimeout) {
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		}

		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		if (is_array($postFields) && 0 < count($postFields))
		{
			$header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields, '', '&'));
		}
		$reponse = curl_exec($ch);
		
		if (curl_errno($ch))
		{
			throw new Exception(curl_error($ch), 0);
		}
		else
		{
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode)
			{
				throw new Exception($reponse, $httpStatusCode);
			}
		}
		curl_close($ch);
		return $reponse;
	}

    /**
     * @param RequestService $request
     * @return stdClass
     * @throws Exception
     */
    public function execute(RequestService $request)
    {
        try 
        {
			$request->check();
        } 
        catch (Exception $e) 
        {
            $result = new StdClass();
			$result->code = $e->getCode();
			$result->message = $e->getMessage();
			return $result;
		}
		$apiParams = array();
		//应用级参数
		$apiParams['jd_param_json'] = json_encode($request->params(), JSON_UNESCAPED_UNICODE);
		//系统级别参数
		$apiParams['token'] 	  = $this ->token;
		$apiParams['app_key']     = $this ->app_key;
		$apiParams['timestamp']   = date('Y-m-d H:i:s');
		$apiParams['v']           = $this ->apiVersion;
		$apiParams['format']      = 'json';
		$apiParams["sign"] 		  = $this->generateSign($apiParams);
		try
		{
			//GET提交
			//$requestUrl = $this->connectUrl . $request->getApiPath().'?'.$this -> query_str_fetch($apiParams,'urlencode');
			//$resp = $this->curl($requestUrl);
			//POST提交
			$requestUrl = $this->api_request_url . $request->action();
			$resp = $this->curl($requestUrl,$apiParams);
            $log = $this->log;
            if ($log != null) {
                $log->info("request data: " . json_encode($apiParams, JSON_UNESCAPED_UNICODE));
                $log->info("response data: " . $resp);
            }
			/*echo "<br>url=>" . $requestUrl;
			echo "<br>params=>" . json_encode($apiParams, JSON_UNESCAPED_UNICODE);
            echo "<br>response=>" . $resp;*/
		}
		catch (Exception $e)
		{
			throw $e;
	    }

        return $this->onResponse($resp);
	}

    /**
     * @param $resp
     * @return mixed
     * @throws Exception
     */
    public function onResponse($resp)
    {
        try
        {
            $respObject = json_decode($resp, true);
            if($respObject === null)
            {
                throw new Exception('服务器繁忙');
            }
            if ($respObject["success"])
            {
                $result = json_decode($respObject['data']);
                if(isset($result->success))
                {
                    if($result->success)
                    {
                        return  $result->result;
                    }
                    $detail = isset($result->detail) ? ':'.$result->detail :'';
                    throw new Exception($result->msg.$detail, $result->code);
                }
                if(isset($result->ret))
                {
                    if($result->ret)
                    {
                        return  $result->data;
                    }
                    throw new Exception($result->retMsg, $result->retCode);
                }

                if(isset($result->code))
                {
                    if($result->code == 200) return $result->result;
                    throw new Exception($result->msg, $result->code);
                }

                throw new Exception('服务器返回参数异常');
            }
            else
            {
                throw new Exception($respObject["msg"], $respObject["code"]);
            }
        }
		catch (Exception $e)
        {
            throw $e;
        }


    }
}
