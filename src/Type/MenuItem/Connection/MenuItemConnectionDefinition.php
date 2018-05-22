<?php

namespace WPGraphQL\Extensions\NavMenu\Type\MenuItem\Connection;

use GraphQLRelay\Relay;
use WPGraphQL\Types;
use WPGraphQL\Type\WPInputObjectType;

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
	 * Stores the where_args Input Object Type
	 *
	 * @var \WPGraphQL\Type\WPInputObjectType $where_args
	 */
	private static $where_args;

	/**
	 * Stores the fields for the $where_args
	 *
	 * @var array $where_fields
	 */
	private static $where_fields;

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

			$args = [
				'where' => [
					'name' => 'where',
					'type' => self::where_args(),
				],
			];

			self::$connection = [
				'type'        => $connection['connectionType'],
				'description' => __( 'A collection of menu item objects', 'wp-graphql' ),
				'args'        => array_merge( Relay::connectionArgs(), $args ),
				'resolve'     => [ __NAMESPACE__ . '\\MenuItemConnectionResolver', 'resolve' ],
			];
		}

		return ! empty( self::$connection ) ? self::$connection : null;
	}

	/**
	 * Defines the "where" args that can be used to query menuItems
	 *
	 * @return WPInputObjectType
	 */
	private static function where_args() {

		if ( null === self::$where_args ) {
			
			self::$where_args = new WPInputObjectType( [
				'name'   => 'MenuItemQueryArgs',
				'fields' => function() {
					return self::where_fields();
				},
			] );
		}

		return ! empty( self::$where_args ) ? self::$where_args : null;

	}

	/**
	 * This defines the fields to be used in the $where_args input type
	 *
	 * @return array|mixed
	 */
	private static function where_fields() {
		if ( null === self::$where_fields ) {
			$fields = [
				'id' => [
					'type'        => Types::int(),
					'description' => __( 'The ID of the object', 'wp-graphql' ),
				],
				'location' => [
					'type'        => NavMenuTypes::menu_location_enum_type(),
					'description' => __( 'The menu location for the menu being queried', 'wp-graphql' ),
				],
			];

			self::$where_fields = WPInputObjectType::prepare_fields( $fields, 'MenuItemQueryArgs' );
		}

		return self::$where_fields;
	}
}
