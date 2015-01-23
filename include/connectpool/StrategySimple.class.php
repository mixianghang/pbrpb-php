<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file StrategySimple.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: ��״̬�򵥵ľ��������㷨��
 *
 */
class StrategySimple extends Strategy
{
    /**
     * @brief �򵥵�ѡ���㷨�����ѡ��ʧ�ܺ�˳����������ѡ��
     *
     * @param [in] arrServers   : ��ѡ���һ������б�
     * @param [in] balanceCode   : ʹ���Ƿ�������ӡ�
     * @return mixed
     * @retval !=false: ѡ��ɹ���=false: ѡ��ʧ�ܡ�
     * @see 
     * @note 
     * @author niuzhangpeng
     * @date 2009/08/29 17:15:52
     **/ 
    public function selectServer($arrServers, $intBalanceCode=false)
    {
        $serverCount = count($arrServers);
        if($serverCount == 0)
        {
            return false;
        }
        $index = $this->_currentIndex;
        //�����ȡһ̨�����������ָ��balanceCode�����һ��ʹ��balanceCode
        if($this->_isFirstSelect == 1)
        {
            if($intBalanceCode !== false)
            {
                $index = intval($intBalanceCode);
                if($index >= $serverCount)
                {
                    $index = 0;
                }
            }
            else
            {
                $index = rand(0, $serverCount-1);
            }
        }
        else  //����ʧ�ܺ����ѡ
        {
            $index = ($index + 1) % $serverCount;
        }
        $server = $arrServers[$index];
        $this->_currentIndex = $index;
        $this->_isFirstSelect = 0;
        return $server;
    }
}
?>
