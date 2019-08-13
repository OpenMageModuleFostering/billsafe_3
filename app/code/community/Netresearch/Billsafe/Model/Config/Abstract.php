<?php

/**
 * Netresearch_Billsafe_Model_Config_Abstract
 *
 * @author Thomas Birke <thomas.birke@netresearch.de>
 */
class Netresearch_Billsafe_Model_Config_Abstract extends Mage_Core_Model_Config_Data
{
    protected $_realConfigData = array();

    /**
     * apply new settings temporarily
     *
     * @return void
     */
    protected function getTempConfig()
    {
        $storeId = Mage::app()->getStore()->getId();
        $config = Mage::getSingleton('billsafe/config');
        $newConfigData = $this->getFieldsetData();
        if (is_array($newConfigData)
            && 1 == $newConfigData['active']
        ) {
            $this->_realConfigData['active']           = $config->isActive($storeId);
            $this->_realConfigData['fee_active']       = $config->getPaymentFeeEnabled($storeId);
            $this->_realConfigData['merchant_id']      = $config->getMerchantId($storeId);
            $this->_realConfigData['merchant_license'] = $config->getMerchantLicense($storeId);

            $config->setActive($newConfigData['active'])
                ->setPaymentFeeEnabled($newConfigData['fee_active'])
                ->setMerchantId($newConfigData['merchant_id'])
                ->setMerchantLicense($newConfigData['merchant_license']);
        }
        return $config;
    }

    protected function restoreConfig()
    {
        $config = Mage::getSingleton('billsafe/config');
        foreach (array('merchant_id', 'merchant_license', 'fee_active', 'active') as $key) {
            if (array_key_exists($key, $this->_realConfigData)) {
                $config->setData($key, $this->_realConfigData['merchant_id']);
            }
        }
    }
}
