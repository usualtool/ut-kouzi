<?php
use usualtool\KouZi\KouZi;
use library\UsualToolInc\UTInc;
$workid="工作流ID";
$kouzi=new KouZi($workid);
$ref_token=$kouzi->GetRefToken();
$acc_token=$kouzi->GetAccToken($ref_token);
if(!empty($acc_token)):
    $result=$kouzi->RunKouZi(array(
	    "传递参数"=>"参数1"
	),$acc_token);
    $logid=$result["execute_id"];
    $logurl=$result["debug_url"];
    $data=array("error"=>0,"logid"=>$logid,"logurl"=>$logurl);
else:
    $data=array("error"=>1);   
endif;
echo json_encode($data,JSON_UNESCAPED_UNICODE);