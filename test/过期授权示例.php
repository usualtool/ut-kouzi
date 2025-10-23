<?php
use usualtool\KouZi\KouZi;
use library\UsualToolInc\UTInc;
$config=UTMysql::GetConfig();
$kouzi=new KouZi(0);
if(empty($_GET["code"])):
    $kouzi->GetLogin();
else:
    $auth_code=$_GET["code"];
    $ref_token=$kouzi->GetNewRefToken($auth_code);
    if(!empty($ref_token)):
        UTInc::GoUrl($config["APPURL"]);
    else:
        UTInc::GoUrl("","No Token");
    endif;
endif;