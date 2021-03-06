<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Client\Html\Checkout\Standard\Order\Address;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Order\Address\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();
		unset( $this->object );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertNotNull( $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY;
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$addrManager = $manager->getSubManager( 'address' );

		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $customerItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer item found' );
		}

		$addrItem = $customerItem->getPaymentAddress();
		$addrItem->setId( null );

		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$basketCntl->setAddress( $type, $addrItem );

		$view = \TestHelperHtml::getView();
		$view->orderBasket = $basketCntl->get();
		$view->orderBasket->setCustomerId( $customerItem->getId() );
		$this->object->setView( $view );

		$this->object->process();

		$orderAddress = $view->orderBasket->getAddress( $type );
		$actual = $addrManager->getItem( $orderAddress->getAddressId() );
		$addrManager->deleteItem( $actual->getId() );

		$this->assertEquals( $addrItem->getFirstname(), $actual->getFirstname() );
		$this->assertEquals( $addrItem->getLastname(), $actual->getLastname() );
		$this->assertEquals( $addrItem->getPostal(), $actual->getPostal() );
		$this->assertEquals( $addrItem->getTelephone(), $actual->getTelephone() );
		$this->assertEquals( $addrItem->getTelefax(), $actual->getTelefax() );
	}
}
