<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin;


/**
 * Common factory for JQAdm clients
 *
 * @package Admin
 * @subpackage JQAdm
 */
class JQAdm
{
	private static $objects = [];


	/**
	 * Creates a new client object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Shop context instance with necessary objects
	 * @param \Aimeos\Bootstrap $aimeos Aimeos object
	 * @param string $path Type of the client, e.g 'product' for \Aimeos\Admin\JQAdm\Product\Standard
	 * @param string|null $name Admin name (default: "Standard")
	 * @return \Aimeos\Admin\JQAdm\Iface admin client implementing \Aimeos\Admin\JQAdm\Iface
	 * @throws \Aimeos\Admin\JQAdm\Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function create( \Aimeos\MShop\ContextIface $context, \Aimeos\Bootstrap $aimeos, $path, $name = null )
	{
		if( empty( $path ) ) {
			throw new \Aimeos\Admin\JQAdm\Exception( $context->translate( 'admin', 'Admin JQAdm type is empty' ) );
		}

		$view = $context->view();
		$config = $context->config();
		$parts = explode( '/', $path );

		foreach( $parts as $idx => $part )
		{
			if( ctype_alnum( $part ) === false )
			{
				$msg = $context->translate( 'admin', 'Invalid characters in client name "%1$s"' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $path ) );
			}

			$parts[$idx] = ucwords( $part );
		}

		if( $view->access( $config->get( 'admin/jqadm/resource/' . $path . '/groups', [] ) ) !== true )
		{
			$msg = $context->translate( 'admin', 'Not allowed to access JQAdm "%1$s" client' );
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $path ) );
		}

		if( empty( $name ) ) {
			$name = $context->config()->get( 'admin/jqadm/' . $path . '/name', 'Standard' );
		}

		$interface = '\\Aimeos\\Admin\JQAdm\\Iface';
		$classname = '\\Aimeos\\Admin\\JQAdm\\' . implode( '\\', $parts ) . '\\' . $name;

		if( class_exists( $classname ) === false )
		{
			$msg = $context->translate( 'admin', 'Class "%1$s" not available' );
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $classname ) );
		}

		$client = self::createAdmin( $context, $classname, $interface );
		$client = self::addClientDecorators( $context, $client, $path );

		return $client->setAimeos( $aimeos )->setView( $view )->setObject( $client );
	}


	/**
	 * Injects a client object.
	 * The object is returned via create() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Admin\JQAdm\Iface|null $client ExtJS client object
	 */
	public static function injectClient( string $classname, \Aimeos\Admin\JQAdm\Iface $client = null )
	{
		self::$objects[$classname] = $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param \Aimeos\Admin\JQAdm\Iface $client Admin object
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Admin\JQAdm\Catalog\Decorator\"
	 * @return \Aimeos\Admin\JQAdm\Iface Admin object
	 */
	protected static function addDecorators( \Aimeos\MShop\ContextIface $context,
		\Aimeos\Admin\JQAdm\Iface $client, array $decorators, string $classprefix ) : \Aimeos\Admin\JQAdm\Iface
	{
		foreach( $decorators as $name )
		{
			$classname = $classprefix . $name;

			if( ctype_alnum( $name ) === false )
			{
				$msg = $context->translate( 'admin', 'Invalid class name "%1$s"' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $classname ) );
			}

			if( class_exists( $classname ) === false )
			{
				$msg = $context->translate( 'admin', 'Class "%1$s" not found' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $classname ) );
			}

			$client = new $classname( $client, $context );
			$interface = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\Iface';

			if( !( $client instanceof $interface ) )
			{
				$msg = $context->translate( 'admin', 'Class "%1$s" does not implement "%2$s"' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $classname, $interface ) );
			}
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param \Aimeos\Admin\JQAdm\Iface $client Admin object
	 * @param string $path Path of the client in lower case, e.g. "catalog/detail"
	 * @return \Aimeos\Admin\JQAdm\Iface Admin object
	 */
	protected static function addClientDecorators( \Aimeos\MShop\ContextIface $context,
		\Aimeos\Admin\JQAdm\Iface $client, string $path ) : \Aimeos\Admin\JQAdm\Iface
	{
		if( empty( $path ) )
		{
			$msg = $context->translate( 'admin', 'Invalid domain "%1$s"' );
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $path ) );
		}

		$localClass = str_replace( '/', '\\', ucwords( $path, '/' ) );
		$config = $context->config();

		/** admin/jqadm/common/decorators/default
		 * Configures the list of decorators applied to all jqadm clients
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  admin/jqadm/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" and
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'admin/jqadm/common/decorators/default', [] );
		$excludes = $config->get( 'admin/jqadm/' . $path . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\';
		$client = self::addDecorators( $context, $client, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\';
		$decorators = $config->get( 'admin/jqadm/' . $path . '/decorators/global', [] );
		$client = self::addDecorators( $context, $client, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\' . $localClass . '\\Decorator\\';
		$decorators = $config->get( 'admin/jqadm/' . $path . '/decorators/local', [] );
		$client = self::addDecorators( $context, $client, $decorators, $classprefix );

		return $client;
	}


	/**
	 * Creates a client object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param string $classname Name of the client class
	 * @param string $interface Name of the client interface
	 * @return \Aimeos\Admin\JQAdm\Iface Admin object
	 * @throws \Aimeos\Admin\JQAdm\Exception If client couldn't be found or doesn't implement the interface
	 */
	protected static function createAdmin( \Aimeos\MShop\ContextIface $context,
		string $classname, string $interface ) : \Aimeos\Admin\JQAdm\Iface
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false )
		{
			$msg = $context->translate( 'admin', 'Class "%1$s" not available' );
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $classname ) );
		}

		$client = new $classname( $context );

		if( !( $client instanceof $interface ) )
		{
			$msg = $context->translate( 'admin', 'Class "%1$s" does not implement "%2$s"' );
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, $classname, $interface ) );
		}

		return $client;
	}
}