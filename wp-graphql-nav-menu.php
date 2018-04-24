<?php
/**
 * WPGraphQL NavMenu support. Borrowing heavily from:
 *
 * https://github.com/jasonbahl/wp-graphql/tree/%23126-menu-support
 *
 * Eventually this should get merged into WPGraphQL core, so we'll add a short-
 * circuit to prevent this plugin from overriding core.
 */

namespace WPGraphQL\Extensions\NavMenu;

require_once( __DIR__ . '/src/Type/Enum/MenuLocationEnumType.php' );
require_once( __DIR__ . '/src/Type/Menu/Connection/MenuConnectionDefinition.php' );
require_once( __DIR__ . '/src/Type/Menu/Connection/MenuConnectionResolver.php' );
require_once( __DIR__ . '/src/Type/Menu/MenuType.php' );
require_once( __DIR__ . '/src/Type/MenuItem/Connection/MenuItemConnectionDefinition.php' );
require_once( __DIR__ . '/src/Type/MenuItem/Connection/MenuItemConnectionResolver.php' );
require_once( __DIR__ . '/src/Type/MenuItem/MenuItemType.php' );
require_once( __DIR__ . '/src/Type/Union/MenuItemObjectUnionType.php' );
require_once( __DIR__ . '/src/Type/Types.php' );

use WPGraphQL\Extensions\NavMenu\Type\Menu\Connection\MenuConnectionDefinition;
use WPGraphQL\Extensions\NavMenu\Type\MenuItem\Connection\MenuItemConnectionDefinition;

function add_root_queries( $root ) {
	// Don't override if something else (core) has already definied our types.
	if ( ! isset( $root['menus'], $root['menuItems'] ) ) {
		$root['menus'] = MenuConnectionDefinition::connection();
		$root['menuItems'] = MenuItemConnectionDefinition::connection();
	}

	return $root;
}
add_filter( 'graphql_root_queries', __NAMESPACE__ . '\\add_root_queries', 50, 1 );
