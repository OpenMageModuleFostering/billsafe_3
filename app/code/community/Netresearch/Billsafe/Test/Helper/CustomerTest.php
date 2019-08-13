<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael
 * Date: 20.09.13
 * Time: 12:07
 * To change this template use File | Settings | File Templates.
 */

class Netresearch_Billsafe_Test_Helper_CustomerTest
    extends EcomDev_PHPUnit_Test_Case
{


    public function testGetCustomerDob()
    {
        $helper = Mage::helper('billsafe/customer');
        $this->assertEquals('1970-01-01', $helper->getCustomerDob(null, null));

        $customer = new Varien_Object();
        $this->assertEquals(
            '1970-01-01', $helper->getCustomerDob($customer, null)
        );

        $customer->setDob('1980-01-01');
        $this->assertEquals(
            '1980-01-01', $helper->getCustomerDob($customer, null)
        );

        $order = Mage::getModel('sales/order');
        $order->setCustomerDob('1990-01-01');

        $this->assertEquals(
            '1990-01-01', $helper->getCustomerDob(null, $order)
        );
        $this->assertEquals(
            '1990-01-01', $helper->getCustomerDob($customer, $order)
        );

    }

    public function testGetCustomerGender()
    {
        $address = new Varien_Object();
        $customer = new Varien_Object();
        $order = new Varien_Object();
        $address->setPrefix('');
        $customer->setPrefix('');
        $helperMock = $this->getMockedGenderTextHelper(null);
        $this->assertEquals(
            'm', $helperMock->getCustomerGender($address, $order, $customer)
        );
        $femaleValues = array('mrs.', 'mrs', 'frau', 'fr.', 'fr',
                                          'fräulein', 'frau dr.', 'female');
        foreach ($femaleValues as $femaleValue) {
            $helperMock = $this->getMockedGenderTextHelper($femaleValue);
            $this->assertEquals(
                'f', $helperMock->getCustomerGender($address, $order, $customer)
            );
        }
        $maleValues = array('mr.', 'mr', 'herr', 'herr dr.',
                                             'male');
        foreach ($maleValues as $maleValue) {
            $helperMock = $this->getMockedGenderTextHelper($maleValue);
            $this->assertEquals(
                'm', $helperMock->getCustomerGender($address, $order, $customer)
            );
        }
    }

    public function testGetGenderText()
    {
        $helper = Mage::helper('billsafe/customer');
        $method = $this->runProtectedMethod('getGenderText', $helper);
        $address = new Varien_Object();
        $address->setData('gender', 'MR');
        $this->assertFalse($method->invoke($helper, $address, 'gender'));
    }

    protected function getMockedGenderTextHelper($returnValue)
    {
        $helperMock = $this->getHelperMock(
            'billsafe/customer', array('getGenderText')
        );
        $helperMock->expects($this->any())
            ->method('getGenderText')
            ->will($this->returnValue($returnValue));
        return $helperMock;
    }


    protected static function runProtectedMethod($name, $object)
    {
        $class = new ReflectionClass(get_class($object));
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}