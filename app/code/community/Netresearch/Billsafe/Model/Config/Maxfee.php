<?php

/**
 * Netresearch_Billsafe_Model_Config_Maxfee
 *
 * @uses   Mage
 * @uses   _Core_Model_Config_Data
 * @author Stephan Hoyer <stephan.hoyer@netresearch.de>
 */
class Netresearch_Billsafe_Model_Config_Maxfee
    extends Netresearch_Billsafe_Model_Config_Abstract
{
    /**
     * Set maximum fee to billsafe maximum if its larger then allowed.
     *
     * @return void
     */
    public function _afterLoad()
    {
        $storeId = Mage::app()->getStore()->getId();
        $config = $this->getTempConfig();
        if ($config->isPaymentFeeEnabled($storeId)) {
            $max = Mage::getModel('billsafe/config')->getMaxFee();
            if ($max < $this->getValue()) {
                $this->setValue($max);
            }
        }
        $this->restoreConfig();
        parent::_afterLoad();
    }

    /**
     * Check if maximum fee does not exceed billsafe maximum.
     *
     * @return void
     */
    public function _beforeSave()
    {
        $storeId = Mage::app()->getStore()->getId();
        $config = $this->getTempConfig();
        if ($config->isActive($storeId)
            && $config->isPaymentFeeEnabled($storeId)
            && strlen($config->getMerchantId($storeId))
            && strlen($config->getMerchantLicense($storeId))
        ) {
            if ($this->getValue() == '') {
                $msg = 'Maximum/Default fee is required entry!';
                throw new Exception(Mage::helper('billsafe')->__($msg));
            }
            $max = Mage::getModel('billsafe/config')->getMaxFee();
            if (is_null($max)) {
                throw new Exception(Mage::helper('billsafe')->__(
                    'No connection to BillSAFE. Please check your credentials.'
                ));
            }
            if ($max < $this->getValue()) {
                $msg
                    = 'Maximum/Default fee %s exceeded the allowed maximum by BillSAFE of %s.';
                throw new Exception(Mage::helper('billsafe')->__(
                    $msg, $this->getValue(), $max
                ));
            }
        }
        $this->restoreConfig();

        parent::_beforeSave();
    }
}
