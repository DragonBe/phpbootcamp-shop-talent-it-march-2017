<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Lists\Items;


/**
 * Default implementation of catalog list item section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/lists/items/standard/subparts
	 * List of HTML sub-clients rendered within the catalog list items section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $subPartPath = 'client/html/catalog/lists/items/standard/subparts';
	private $subPartNames = array();
	private $tags = array();
	private $expire;
	private $cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
		}
		$view->itemsBody = $html;

		/** client/html/catalog/lists/items/standard/template-body
		 * Relative path to the HTML body template of the catalog list items client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * It's also possible to create a specific template for each type, e.g.
		 * for the grid, list or whatever view you want to offer your users. In
		 * that case, you can configure the template by adding "-<type>" to the
		 * configuration key. To configure an alternative list view template for
		 * example, use the key
		 *
		 * client/html/catalog/lists/items/standard/template-body-list = catalog/lists/items-body-list.php
		 *
		 * The argument is the relative path to the new template file. The type of
		 * the view is determined by the "l_type" parameter (allowed characters for
		 * the types are a-z and 0-9), which is also stored in the session so users
		 * will keep the view during their visit. The catalog list type subpart
		 * contains the template for switching between list types.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/lists/items/standard/template-header
		 * @see client/html/catalog/lists/type/standard/template-body
		 */
		$tplconf = 'client/html/catalog/lists/items/standard/template-body';
		$default = 'catalog/lists/items-body-default.php';

		return $view->render( $this->getTemplatePath( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
		}
		$view->itemsHeader = $html;

		/** client/html/catalog/lists/items/standard/template-header
		 * Relative path to the HTML header template of the catalog list items client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the templates directory (usually
		 * in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * It's also possible to create a specific template for each type, e.g.
		 * for the grid, list or whatever view you want to offer your users. In
		 * that case, you can configure the template by adding "-<type>" to the
		 * configuration key. To configure an alternative list view template for
		 * example, use the key
		 *
		 * client/html/catalog/lists/items/standard/template-header-list = catalog/lists/items-header-list.php
		 *
		 * The argument is the relative path to the new template file. The type of
		 * the view is determined by the "l_type" parameter (allowed characters for
		 * the types are a-z and 0-9), which is also stored in the session so users
		 * will keep the view during their visit. The catalog list type subpart
		 * contains the template for switching between list types.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/lists/items/standard/template-body
		 * @see client/html/catalog/lists/type/standard/template-body
		 */
		$tplconf = 'client/html/catalog/lists/items/standard/template-header';
		$default = 'catalog/lists/items-header-default.php';

		return $view->render( $this->getTemplatePath( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** client/html/catalog/lists/items/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog list items html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/catalog/lists/items/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/lists/items/decorators/global
		 * @see client/html/catalog/lists/items/decorators/local
		 */

		/** client/html/catalog/lists/items/decorators/global
		 * Adds a list of globally available decorators only to the catalog list items html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/catalog/lists/items/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/lists/items/decorators/excludes
		 * @see client/html/catalog/lists/items/decorators/local
		 */

		/** client/html/catalog/lists/items/decorators/local
		 * Adds a list of local decorators only to the catalog list items html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
		 *
		 *  client/html/catalog/lists/items/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/lists/items/decorators/excludes
		 * @see client/html/catalog/lists/items/decorators/global
		 */

		return $this->createSubClient( 'catalog/lists/items/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames()
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( $content, $uid )
	{
		$content = parent::modifyBody( $content, $uid );

		return $this->replaceSection( $content, $this->getView()->csrf()->formfield(), 'catalog.lists.items.csrf' );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function setViewParams( \Aimeos\MW\View\Iface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->cache ) )
		{
			$context = $this->getContext();
			$config = $context->getConfig();
			$products = $view->get( 'listProductItems', array() );


			if( $config->get( 'client/html/catalog/lists/basket-add', false ) )
			{
				$domains = array( 'media', 'price', 'text', 'attribute', 'product' );
				$controller = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );


				$productIds = $this->getProductIds( $products );
				$productManager = $controller->createManager( 'product' );
				$productItems = $this->getDomainItems( $productManager, 'product.id', $productIds, $domains );
				$this->addMetaItems( $productItems, $this->expire, $this->tags );


				$attrIds = $this->getAttributeIds( $productItems );
				$attributeManager = $controller->createManager( 'attribute' );
				$attributeItems = $this->getDomainItems( $attributeManager, 'attribute.id', $attrIds, $domains );
				$this->addMetaItems( $attributeItems, $this->expire, $this->tags );


				$mediaIds = $this->getMediaIds( $productItems );
				$mediaManager = $controller->createManager( 'media' );
				$mediaItems = $this->getDomainItems( $mediaManager, 'media.id', $mediaIds, $domains );
				$this->addMetaItems( $mediaItems, $this->expire, $this->tags );


				$view->itemsAttributeItems = $attributeItems;
				$view->itemsProductItems = $productItems;
				$view->itemsMediaItems = $mediaItems;
			}


			if( !empty( $products ) && (bool) $config->get( 'client/html/catalog/lists/stock/enable', true ) == true ) {
				$view->itemsProductCodes = $this->getProductCodes( $products );
			}

			$view->itemPosition = ( $this->getProductListPage( $view ) - 1 ) * $this->getProductListSize( $view );

			$this->cache = $view;
		}

		return $this->cache;
	}


	/**
	 * Returns the config, custom, hidden and variant attribute IDs of the given products
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $productItems List of product items
	 * @return string[] List of attribute IDs
	 */
	protected function getAttributeIds( array $productItems )
	{
		$attrIds = array();
		$types = array( 'config', 'custom', 'hidden', 'variant' );

		foreach( $productItems as $product ) {
			$attrIds = array_merge( $attrIds, array_keys( $product->getRefItems( 'attribute', null, $types ) ) );
		}

		return $attrIds;
	}


	/**
	 * Returns the media IDs of the selection products
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $productItems List of product items
	 * @return string[] List of media IDs
	 */
	protected function getMediaIds( array $productItems )
	{
		$mediaIds = array();

		foreach( $productItems as $product )
		{
			if( $product->getType() === 'select' ) {
				$mediaIds = array_merge( $mediaIds, array_keys( $product->getRefItems( 'media', 'default', 'default' ) ) );
			}
		}

		return $mediaIds;
	}


	/**
	 * Returns the product IDs of the selection products
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $productItems List of product items
	 * @return string[] List of product IDs
	 */
	protected function getProductIds( array $productItems )
	{
		$prodIds = array();

		foreach( $productItems as $product )
		{
			if( $product->getType() === 'select' ) {
				$prodIds = array_merge( $prodIds, array_keys( $product->getRefItems( 'product', 'default', 'default' ) ) );
			}
		}

		return $prodIds;
	}
}
