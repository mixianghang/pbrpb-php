<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file Connection.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: ���ӳ�����
 *
 */
abstract class Connection
{
    protected $_curConnection = '';
    protected $_server = array();

    /**
     * @brief �������ӷ���, ������̳�ʵ�֡�
     *
     * @param [in] server   : �����ӻ�����Ϣ
     * @param [in] intTimeout   : ��ʱʱ�䣬��λms
     * @param [in] arrAuth   :  ĳЩ���ӿ����õ���Ȩ��Ϣ
     * @return
     * @retval
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    abstract function connect($server, $intTimeout, $arrAuth);
}
?>
