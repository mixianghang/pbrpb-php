<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file ConnectionManInc.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: ��������
 *
 */
class ConnectionManInc
{
    const CONNECTION_TYPE_SOCKET = "Socket";
    const CONNECTION_TYPE_MYSQLI = "Mysqli";

    const CONNECTION_CLASS_PREFIX = "Connection";
    const STRATEGY_CLASS_PREFIX = "Strategy";

    const DEFAULT_STRATEGY = "Simple";

    const ERR_EMPTY_SERVER = -1;          /**< ���������б�Ϊ��       */
    const ERR_NO_STRATEGY = -2;           /**< ���������       */
    const ERR_NO_CONNECTION = -3;         /**< ���������       */
    const ERR_ALL_FAILED = -4;            /**< ���л������Ӿ�ʧ��       */
}

?>
