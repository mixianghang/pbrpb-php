<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file ConnectionMysqli.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: mysqli������,��Ҫʵ��connect������
 *
 */
class ConnectionMysqli extends Connection
{
    /**
     * @brief ʵ��Connection������ӷ���������mysqliʵ�����ӡ�
     *
     * @param [in] server   : �����ӻ�����Ϣ
     * @param [in] intTimeout   : ��ʱʱ�䣬��λms, ��СΪ1�롣
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
        $dbname = $arrAuth['dbname'];
        $dbuser = $arrAuth['dbuser'];
        $dbpass = $arrAuth['dbpass'];

        $mysqli = mysqli_init();
        $intTimeoutSec = $intTimeout > 1000 ? $intTimeout/1000 : 1;
        $bolRet = mysqli_options($mysqli,MYSQLI_OPT_CONNECT_TIMEOUT,$intTimeoutSec);
        if(!$bolRet)
        {
            return false;
        }
        $bolRet = @mysqli_real_connect($mysqli,$host,$dbuser,$dbpass,$dbname,$port);
        if(!$bolRet)
        {
            //����ʧ��
            $errno = mysqli_connect_errno();
            return false;
        }
        return $mysqli;
    }
}
?>
