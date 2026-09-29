<?php
use usualtool\KouZi\KouZi;
use usualtool\Lib\Inc;
$kouzi=new KouZi(0);
if(empty($_GET["code"])):
    $kouzi->GetLogin();
else:
    $auth_code=$_GET["code"];
    $ref_token=$kouzi->GetNewRefToken($auth_code);
    if(!empty($ref_token)):
        Inc::GoUrl($config["APPURL"]);
    else:
        Inc::GoUrl("","No Token");
    endif;
endif;
