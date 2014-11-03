<?php

    //可用ip
    $System = D('system_config');
    $ip = $System -> find("name='ip'");
    $ip = $ip['value'];

    if($ip)
    {
        //用户ip
        $userip = get_client_ip();
        if($userip == 'unknown') exit('<h1>Forbidden</h1>');
        $userip = explode('.', $userip);

        //检测ip
        $allow = false;
        $ip = explode(',', $ip);
        foreach($ip as $value)
        {
            $value = explode('--', $value);
            if(count($value) == 2)
            {
                $start = explode('.', $value[0]);
                if($start[0] != $userip[0] || $start[1] != $userip[1] || $start[2] != $userip[2])
                    continue;
                $end = explode('.', $value[1]);
                if($start[3] <= $userip[3] && $end[3] >= $userip[3])
                {
                    $allow = true;
                    break;
                }
            }else{
                if(implode('.', $userip) == $value[0]) $allow = true;
            }
        }
        if(!$allow)
            exit('<h1>Forbidden</h1>');
    }

?>