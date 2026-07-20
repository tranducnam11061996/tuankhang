<?php
class XXX_Nav_Walker extends Walker_Nav_Menu {
public function start_lvl( &$output, $depth = 0, $args = array() ) {
	$output .= '<ul class="uk-nav-sub">';
}
public function start_el(&$output , $item , $depth = 0, $args = array() , $id = 0){
        $class_li = $class_a = '';
        if($args->has_children && $depth === 0){
            $class_li .= ' uk-parent ';
        }
        $class_li = !empty($class_li) ? ' class="'.esc_attr($class_li) .'" ':'';
        $class_a = !empty($class_a) ? ' class="'.esc_attr($class_a) .'" ':'';

        // ghép nối thẻ li
        $output .= '<li'.$class_li.'>';

        // chỉnh sửa thẻ a
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		if ( '_blank' === $item->target && empty( $item->xfn ) ) {
			$atts['rel'] = 'noopener';
		} else {
			$atts['rel'] = $item->xfn;
		}
        if ( $args->has_children ) {
            $atts['href']   		= '#';
            $atts['data-toggle']	= 'dropdown';
            $atts['class']			= 'dropdown-toggle';
        } else {
		$atts['href']         = ! empty( $item->url ) ? $item->url : '';
        }
		$atts['aria-current'] = $item->current ? 'page' : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
}
    public function end_el(&$output, $item, $depth = 0, $args = array(), $id = 0){
    $output .= '</li>';
}
public function end_lvl(&$output, $depth = 0, $args = array() ){
    $output .= '</ul>';
}
public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
   if ( ! $element )
      return;

   $id_field = $this->db_fields['id'];

   // Display this element.
   if ( is_object( $args[0] ) )
      $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

   parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
}
}
 // end XXX_Nav_Walker
 ?>