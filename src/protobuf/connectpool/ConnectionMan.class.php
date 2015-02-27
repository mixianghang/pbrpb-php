<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file ConnectionMan.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

require_once(dirname(__FILE__)."/ConnectionManInc.class.php");
require_once(dirname(__FILE__)."/Connection.class.php");
require_once(dirname(__FILE__)."/Strategy.class.php");
require_once(dirname(__FILE__)."/ConnectionMysqli.class.php");
require_once(dirname(__FILE__)."/ConnectionSocket.class.php");
require_once(dirname(__FILE__)."/StrategySimple.class.php");
require_once(dirname(__FILE__)."/StrategyState.class.php");
/*
 * @brief: ��������⣬�ṩ���⾲̬�����ӿ���
 *
 */
class ConnectionMan
{
    private static $_connection = null;
    private static $_strategy = null;
    private static $_errno = 0;

    /**
     * @brief ���mysqli���Ӿ��
     *
     * @param [out] selServer   : ���ӳɹ�ʱ��ѡ�еĻ�����Ϣ
     * @param [in] arrServers   :  һ���ѡ��Ļ����б�
     * @param [in] arrAuth   : ���ݿ���Ȩ��Ϣ
     * @param [in] intTimeout   : ��ʱʱ�䣬��λms����СΪ1��
     * @param [in] arrStrategy   : ����ѡ��
     * @param [in] intBalanceCode   : �������ӣ���ʹ��ʱ��ֵΪfalse
     * @return  object
     * @retval  false: ʧ�ܣ�object: mysqli��������ӳɹ�
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public static function getMysqli(&$selServer, $arrServers, $arrAuth, $intTimeout=1000, $arrStrategy=null,
        $intBalanceCode=false)
    {
        if(count($arrServers) == 0)
        {
            self::$_errno = ConnectionManInc::ERR_EMPTY_SERVER;
            return false;
        }
        $strConnectionType = ConnectionManInc::CONNECTION_TYPE_MYSQLI;
        $handle = self::getResource($selServer, $strConnectionType, $arrServers, $arrAuth, $intTimeout, $arrStrategy,
            $intBalanceCode);
        return $handle;
    }

    /**
     * @brief ���socket���Ӿ��
     *
     * @param [out] selServer   : ���ӳɹ�ʱ��ѡ�еĻ�����Ϣ
     * @param [in] arrServers   :  һ���ѡ��Ļ����б�
     * @param [in] intTimeout   : ��ʱʱ�䣬��λms
     * @param [in] arrStrategy   : ����ѡ��
     * @param [in] intBalanceCode   : �������ӣ���ʹ��ʱ��ֵΪfalse
     * @return  object stream
     * @retval  false: ʧ�ܣ�object: socket��������ӳɹ�
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public static function getSocket(&$selServer, $arrServers, $intTimeout=100, $arrStrategy=null,
        $intBalanceCode=false)
    {
        $arrAuth = array();
        if(count($arrServers) == 0)
        {
            self::$_errno = ConnectionManInc::ERR_EMPTY_SERVER;
            return false;
        }
        $strConnectionType = ConnectionManInc::CONNECTION_TYPE_SOCKET;
        $handle = self::getResource($selServer, $strConnectionType, $arrServers, $arrAuth, $intTimeout, $arrStrategy,
            $intBalanceCode);
        return $handle;
    }

    /**
     * @brief �����������͡�����������ԣ���ȡ���Ӿ��
     *
     * @param [out] selServer   : ���ӳɹ�ʱ��ѡ�еĻ�����Ϣ
     * @param [in] strConnectionType   : ʹ�������������
     * @param [in] arrServers   :  һ���ѡ��Ļ����б�
     * @param [in] arrAuth   : ��Ȩ��Ϣ, ��Щ����ʱ��Ҫʹ��
     * @param [in] intTimeout   : ��ʱʱ�䣬��λms
     * @param [in] arrStrategy   : ����ѡ��
     * @param [in] intBalanceCode   : �������ӣ���ʹ��ʱ��ֵΪfalse
     * @return  object
     * @retval  false: ʧ�ܣ�object: ָ�����ӵľ�������ӳɹ�
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public static function getResource(&$selServer, $strConnectionType, $arrServers, $arrAuth, $intTimeout,
        $arrStrategy, $intBalanceCode)
    {
        $handle = false;
        $selServer = false;
        if(!is_array($arrStrategy) || !array_key_exists("name", $arrStrategy))
        {
            $arrStrategy["name"] = "";
        }
        if(!is_array($arrStrategy) || !array_key_exists("config", $arrStrategy))
        {
            $arrStrategy["config"] = array();
        }
        //��ȡ���Զ���
        self::$_strategy = self::_getStrategyInstance($arrStrategy["name"], $arrStrategy["config"]);
        //��ȡ���Ӷ���
        self::$_connection = self::_getConnectionInstance($strConnectionType);

        if(!is_object(self::$_strategy))
        {
            self::$_errno = ConnectionManInc::ERR_NO_STRATEGY;
            return false;
        }
        if(!is_object(self::$_connection))
        {
            self::$_errno = ConnectionManInc::ERR_NO_CONNECTION;
            return false;
        }

        $intServerCount = count($arrServers);
        for($i=0; $i<$intServerCount; $i++)
        {
            $server = self::$_strategy->selectServer($arrServers, $intBalanceCode);
            if($server == false)
            {
                //��ǰkey��selectServerû��ѡ��������ѡ
                continue;
            }
            $handle = self::$_connection->connect($server, $intTimeout, $arrAuth);
            if($handle === false)  //����ʧ��
            {
                //������������ʧ�ܣ��´γ�������������
                self::$_strategy->markFail($server);
            }
            else //���ӳɹ����õ����
            {
                //�����������ӳɹ����������Ӿ��
                self::$_strategy->markSucc($server);
                $selServer = $server;
                return $handle;
            }
        }
        self::$_errno = ConnectionManInc::ERR_ALL_FAILED;
        return false;
    }

    /**
     * @brief ������������newһ����������󣬸���ʵ������
     *
     * @param [in] strConnectionType   : ʹ�������������
     * @return  class
     * @retval  false: ʧ�ܣ�class: ���������
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    private static function _getConnectionInstance($strConnectionType)
    {
        $strConnectionClass = (ConnectionManInc::CONNECTION_CLASS_PREFIX).$strConnectionType;
        if( class_exists($strConnectionClass) )
        {
            $objConn = new $strConnectionClass();
            return $objConn;
        }
        return false;
    }

    /**
     * @brief ���ݲ���ѡ��newһ����������󣬸������ѡ��
     *
     * @param [in] strStrategyName   : ʹ�ò����������
     * @param [in] arrConfig   : �Զ������ò���
     * @return  class
     * @retval  false: ʧ�ܣ�class: ���������
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    private static function _getStrategyInstance($strStrategyName, $arrConfig)
    {
        if(empty($strStrategyName))
        {
            $strStrategyName = ConnectionManInc::DEFAULT_STRATEGY;
        }
        $strStrategyClass = (ConnectionManInc::STRATEGY_CLASS_PREFIX).$strStrategyName;
        if( class_exists($strStrategyClass) )
        {
            $objStrategy = new $strStrategyClass($arrConfig);
            return $objStrategy;
        }
        return false;
    }

    /**
     * @brief �ֹ�����ĳ������һ������ʧ�ܣ������ӳɹ������Ƕ�дʧ��ʱ������ʹ�á�
     *
     * @param [in] server   : ������Ϣ
     * @return  bool
     * @retval  false: ʧ�ܣ�true: �ɹ�
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public static function setUnavailable($server)
    {
        return self::$_strategy->markFail($server);
    }

    /**
     * @brief ��ȡ���һ�δ����
     *
     * @return  int
     * @retval  
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public static function getLastErrno()
    {
        return self::$_errno;
    }
}
?>
