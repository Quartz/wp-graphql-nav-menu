<?php

namespace WPGraphQL\Extensions\NavMenu\Type;

/**
 * Acts as a registry and factory for Types.
 */
class Types {

	/**
	 * Store for all types
	 *
	 * @var array
	 */
	private static $types = [];

	/**
	 * Get type defintion, possible from cache / store.
	 * @param  string $type Type class name
	 * @return object
	 */
	private static function get_type( $type ) {
		if ( ! isset( self::$types[ $type ] ) ) {
			$TypeName = __NAMESPACE__ . "\\{$type}";
			self::$types[ $type ] = new $TypeName();
		}

		return self::$types[ $type ];
	}

	/**
	 * Get MenuLocationEnumType definition.
	 */
	public static function menu_location_enum_type() {
		return self::get_type( 'Enum\\MenuLocationEnumType' );
	}

	/**
	 * Get MenuType definition.
	 */
	public static function menu_type() {
		return self::get_type( 'Menu\\MenuType' );
	}

	/**
	 * Get MenuItemType definition.
	 */
	public static function menu_item_type() {
		return self::get_type( 'MenuItem\\MenuItemType' );
	}

	/**
	 * Get MenuItemObjectUnionType definition.
	 */
	public static function menu_item_object_union_type() {
		return self::get_type( 'Union\\MenuItemObjectUnionType' );
	}
}
