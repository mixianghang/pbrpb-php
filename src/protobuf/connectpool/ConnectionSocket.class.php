<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file ConnectionSocket.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: socket������,��Ҫʵ��connect������
 *
 */
class ConnectionSocket extends Connection
{
    /**
     * @brief ʵ��Connection������ӷ���������socketʵ�����ӡ�
     *
     * @param [in] server   : �����ӻ�����Ϣ
     * @param [in] intTimeout   : ��ʱʱ�䣬��λms��
     * @param [in] arrAuth   :  ĳЩ���ӿ����õ���Ȩ��Ϣ
     * @return
     * @retval
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    function connect($server, $intTimeout, $arrAuth)
    {
        $host = $server['host'];
        $port = $server['port'];
        $floatTimeoutSec = (float)($intTimeout / 1000.0);
        $socket = @fsockopen ($host,$port,$intErrno,$strError,$floatTimeoutSec);
        if(is_resource($socket) === false)
        {
            return false;
        }
        return $socket;
    }
}
?>
