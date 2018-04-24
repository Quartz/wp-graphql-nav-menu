<?php
/**
 * WPGraphQL NavMenu MenuType
 */

namespace WPGraphQL\Extensions\NavMenu\Type\MenuItem;

use GraphQLRelay\Relay;
use WPGraphQL\Type\WPObjectType;
use WPGraphQL\Types;

use WPGraphQL\Extensions\NavMenu\Type\MenuItem\Connection\MenuItemConnectionDefinition;
use WPGraphQL\Extensions\NavMenu\Type\Types as NavMenuTypes;

/**
 * Class MenuItemType
 *
 * @package WPGraphQL\Type
 */
class MenuItemType extends WPObjectType {

	/**
	 * Holds the type name
	 *
	 * @var string $type_name
	 */
	private static $type_name;

	/**
	 * This holds the field definitions
	 *
	 * @var array $fields
	 */
	private static $fields;

	/**
	 * MenuItemType constructor.
	 */
	public function __construct() {

		self::$type_name = 'MenuItem';

		$config = [
			'name'        => self::$type_name,
			'description' => __( 'Navigation menu items are the individual items assigned to a menu. These are rendered as the links in a navigation menu.', 'wp-graphql' ),
			'fields'      => self::fields(),
		];

		parent::__construct( $config );

	}

	/**
	 * fields
	 *
	 * This defines the fields that make up the MenuLocationType
	 *
	 * @return array|\Closure|null
	 */
	private static function fields() {

		if ( null === self::$fields ) {

			$local_id_name = lcfirst( self::$type_name ) . 'Id';

			self::$fields = function() {
				$fields = [
					'id' => [
						'type'        => Types::non_null( Types::id() ),
						'description' => __( 'Relay ID of the menu item.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return ( ! empty( $menu_item->post_type ) && ! empty( $menu_item->ID ) ) ? Relay::toGlobalId( $menu_item->post_type, $menu_item->ID ) : null;
						},
					],
					'childItems' => MenuItemConnectionDefinition::connection(),
					'connectedObject' => [
						'type'        => NavMenuTypes::menu_item_object_union_type(),
						'description' => __( 'The object connected to this menu item.', 'wp-graphql' ),
						'resolve' => function( \WP_Post $menu_item ) {
							$object_id   = get_post_meta( $menu_item->ID, '_menu_item_object_id', true );
							$object_type = get_post_meta( $menu_item->ID, '_menu_item_type', true );

							switch ( $object_type ) {
								// Custom link ... resolve with menu item.
								case 'custom':
									return $menu_item;

								// Post object
								case 'post_type':
									return get_post( $object_id );

								// Taxonomy term
								case 'taxonomy':
									return get_term( $object_id );
							}

							return null;
						},
					],
					'cssClasses' => [
						'type'        => Types::list_of( Types::string() ),
						'description' => __( 'Class attribute for the menu item link', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							$classes = get_post_meta( $menu_item->ID, '_menu_item_classes', true );

							// If all we have is a non-array or an array with one empty
							// string, return an empty array.
							if ( ! is_array( $classes ) || empty( $classes ) || empty( $classes[0] ) ) {
								return [];
							}

							return $classes;
						},
					],
					'description' => [
						'type'        => Types::string(),
						'description' => __( 'Description of the menu item.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return ( ! empty( $menu_item->post_content ) ) ? $menu_item->post_content : null;
						},
					],
					'label' => [
						'type'        => Types::string(),
						'description' => __( 'Label or title of the menu item.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return ( ! empty( $menu_item->post_title ) ) ? $menu_item->post_title : null;
						},
					],
					'linkRelationship' => [
						'type'        => Types::string(),
						'description' => __( 'Link relationship (XFN) of the menu item.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return get_post_meta( $menu_item->ID, '_menu_item_xfn', true );
						},
					],
					'menuItemId' => [
						'type'        => Types::int(),
						'description' => __( 'WP ID of the menu item.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return ! empty( $menu_item->ID ) ? $menu_item->ID : null;
						},
					],
					'target' => [
						'type'        => Types::string(),
						'description' => __( 'Target attribute for the menu item link.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return get_post_meta( $menu_item->ID, '_menu_item_target', true );
						},
					],
					'title' => [
						'type'        => Types::string(),
						'description' => __( 'Title attribute for the menu item link', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							return ( ! empty( $menu_item->post_excerpt ) ) ? $menu_item->post_excerpt : null;
						},
					],
					'url' => [
						'type'        => Types::string(),
						'description' => __( 'URL or destination of the menu item.', 'wp-graphql' ),
						'resolve'     => function( \WP_Post $menu_item ) {
							$url = get_post_meta( $menu_item->ID, '_menu_item_url', true );

							if ( ! empty( $url ) ) {
								return $url;
							}

							// Get the permalink of the connected object, if available.
							$object_id = get_post_meta( $menu_item->ID, '_menu_item_object_id', true );
							if ( ! empty( $object_id ) ) {
								return get_permalink( $object_id );
							}

							return null;
						},
					],
				];

				return self::prepare_fields( $fields, self::$type_name );
			};

		} // End if().

		return ! empty( self::$fields ) ? self::$fields : null;

	}

}
