<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file StrategyState.class.php
 * @author niuzp(niuzhangpeng@baidu.com)
 * @brief 
 *  
 **/

/*
 * @brief: ��״̬�㷨���Է�����д�֣����ݵ�ǰ�����������server��ѡ��
 *         ����㷨ʹ��eacc�ṩ��get��put�ȷ�����������Ϣ�洢�ڹ����ڴ��У�
 *         ʹ�õ�ʱ����Ҫ��װeacc����չ
 *
 */
class StrategyState extends Strategy
{
    /*
     * @var MIN_SCORE_DEFAULT  server��ͷ���
     * @var MAX_SCORE_DEFAULT  server��߷���
     * @var SCORE_ADD_DEFAULT  ���ӳɹ��ӷ�ֵ
     * @var SCORE_SUB_DEFAULT  ����ʧ�ܼ���ֵ
     * @var CONNTIME_INTERVAL_DEFAULT  ����崻���೤ʱ�䲻��������,��λ��
     *
     */
    const MIN_SCORE_DEFAULT = 1000;
    const MAX_SCORE_DEFAULT = 1500;
    const SCORE_ADD_DEFAULT = 10;
    const SCORE_SUB_DEFAULT = 100;
    const CONNTIME_INTERVAL_DEFAULT = 120;

    private $_maxScore;
    private $_minScore;
    private $_scoreAdd;
    private $_scoreSub;
    private $_timeIntervalSec;

    /*
     * @brief: ���캯�����������������飬���Ұ�����Ӧ�������ֶΣ���ʹ���û�ָ���Ĳ�����
     *           ����ʹ��Ĭ�ϲ�����
     */
    public function __construct($arrConfig)
    {
        $this->_arrConfig = $arrConfig;
        $this->_minScore = isset($arrConfig["StrategyState_MIN_SCORE"]) ? 
            intval($arrConfig["StrategyState_MIN_SCORE"]) : self::MIN_SCORE_DEFAULT;
        $this->_maxScore = isset($arrConfig["StrategyState_MAX_SCORE"]) ? 
            intval($arrConfig["StrategyState_MAX_SCORE"]) : self::MAX_SCORE_DEFAULT;
        $this->_scoreAdd = isset($arrConfig["StrategyState_SCORE_ADD"]) ? 
            intval($arrConfig["StrategyState_SCORE_ADD"]) : self::SCORE_ADD_DEFAULT;
        $this->_scoreSub = isset($arrConfig["StrategyState_SCORE_SUB"]) ? 
            intval($arrConfig["StrategyState_SCORE_SUB"]) : self::SCORE_SUB_DEFAULT;
        $this->_timeIntervalSec = isset($arrConfig["StrategyState_TIME_INTERVAL_SEC"]) ?
            intval($arrConfig["StrategyState_TIME_INTERVAL_SEC"]) : self :: CONNTIME_INTERVAL_DEFAULT;
        return true;
    }

    /*
     * @brief: ��һ�������ѡ��һ�����������ݷ�������Ϣ��
     *
     */
    public function selectServer($arrServers, $intBalanceCode=false)
    {
        $serverCount = count($arrServers);
        if($serverCount == 0)
        {
            return false;
        }
        $index = $this->_currentIndex;
        //�����ָ��balanceCode�����һ��ʹ��balanceCode
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
        $arrInfo = $this->_getServerInfo($server);
        if ($arrInfo['score'] < $this->_minScore)
        {
            //���ϴ����Ӽ���϶�ʱ�����������ӳ���
            if (time() - $arrInfo['time'] < $this->_timeIntervalSec)
            {
                $server = false;
            }
            else
            {
                $this->_initServerInfo($server); //����ʱ������³���
            }
        }
        $this->_currentIndex = $index;
        $this->_isFirstSelect = 0;
        return $server;
    }

    /*
     * @brief: �ӹ����ڴ��л�ȡ�����������ķ�����Ϣ
     *
     */
    private function _getServerInfo($arrServer)
    {
        $strKey = $arrServer['host'].':'.$arrServer['port'];
        $arrValue = eaccelerator_get($strKey);
        if (NULL === $arrValue) 
        {  
            // the key doesn't exist or the key was expired.
            $this->_initServerInfo($arrServer);
        }
        $arrValue = eaccelerator_get($strKey);
        if (NULL === $arrValue)
        {
            $arrValue = array (
                'score'  =>  $this->_minScore,
                'time'   =>  time(),
                );
        }
        return $arrValue;
    }

    /*
     * @brief: ��ʼ��������������Ϣ
     *
     */
    private function _initServerInfo($arrServer)
    {
        $strKey = $arrServer['host'].':'.$arrServer['port'];
        $arrValue = array (
            'score'  =>  $this->_minScore + $this->_scoreAdd * 10,
            'time'   =>  time (),
            );
        $bolRes = eaccelerator_put ($strKey,$arrValue);
        return $bolRes;
    }

    /*
     * @brief: ��Ǹû�����һ������ʧ��
     *
     */
    public function markFail($arrServer)
    {
        $arrInfo = $this->_getServerInfo($arrServer);
        $arrInfo['score'] -= $this->_scoreSub;
        $arrInfo['time'] = time ();
        $strKey = $arrServer['host'].':'.$arrServer['port'];
        $bolRes = eaccelerator_put($strKey,$arrInfo);
        return $bolRes;
    }

    /*
     * @brief: ��Ǹû�����һ�����ӳɹ�
     *
     */
    public function markSucc($arrServer)
    {
        $arrInfo = $this->_getServerInfo ($arrServer);
        if ($arrInfo['score'] < $this->_maxScore)
        {
            $arrInfo['score'] += $this->_scoreAdd;
            $arrInfo['time'] = time ();
            $strKey = $arrServer['host'].':'.$arrServer['port'];
            $bolRes = eaccelerator_put ($strKey,$arrInfo);
            return $bolRes;
        }
        else
        {
            return true;
        }
    }
}
?>
