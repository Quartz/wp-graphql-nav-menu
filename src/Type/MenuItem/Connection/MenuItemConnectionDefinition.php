<?php

namespace WPGraphQL\Extensions\NavMenu\Type\MenuItem\Connection;

use GraphQLRelay\Relay;
use WPGraphQL\Types;

use WPGraphQL\Extensions\NavMenu\Type\Types as NavMenuTypes;

/**
 * Class MenuItemConnectionDefinition
 */
class MenuItemConnectionDefinition {

	/**
	 * Stores some date for the Relay connection for term objects
	 *
	 * @var array $connection
	 * @access private
	 */
	private static $connection;

	/**
	 * Method that sets up the relay connection for term objects
	 *
	 * @return mixed
	 */
	public static function connection() {

		if ( null === self::$connection ) {

			$connection = Relay::connectionDefinitions( [
				'nodeType' => NavMenuTypes::menu_item_type(),
				'name'     => 'MenuItems',
			] );

			self::$connection = [
				'type'        => $connection['connectionType'],
				'description' => __( 'A collection of menu item objects', 'wp-graphql' ),
				'args'        => Relay::connectionArgs(),
				'resolve'     => [ __NAMESPACE__ . '\\MenuItemConnectionResolver', 'resolve' ],
			];
		}

		return ! empty( self::$connection ) ? self::$connection : null;
	}
}
