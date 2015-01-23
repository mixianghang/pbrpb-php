<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file Strategy.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: ���Գ�����
 *
 */
abstract class Strategy
{
    protected $_arrConfig = array();
    protected $_isFirstSelect = 1;
    protected $_currentIndex = false;

    public function __construct($arrConfig)
    {
        $this->_arrConfig = $arrConfig;
        return true;
    }
    /**
     * @brief ����selectServer����������ʵ�ָ÷�������һ������е�ѡ��
     *
     * @param [in] arrServers   : ��ѡ���һ������б�
     * @param [in] balanceCode   : ʹ���Ƿ�������ӡ�
     * @return
     * @retval
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    abstract function selectServer($arrServers, $balanceCode=false);

    /**
     * @brief Ĭ��markFail������������ʧ��ʱ����Ҫ�Ĳ��Խ��е��á���������ʵ�֡�
     *
     * @param [in] server   : ����ʧ���˵Ļ�����Ϣ��
     * @return
     * @retval
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public function markFail($server)
    {
        return true;
    }

    /**
     * @brief Ĭ��markSucc�����������ӳɹ�ʱ����Ҫ�Ĳ��Խ��е��á���������ʵ�֡�
     *
     * @param [in] server   : �����ӳɹ��˵Ļ�����Ϣ��
     * @return
     * @retval
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public function markSucc($server)
    {
        return true;
    }
}
?>
