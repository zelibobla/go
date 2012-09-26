<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_Table extends Zend_View_Helper_Abstract {

	public $view;

	public function table(
		$collection,
		$params = array()
	) {
		if( false == is_array( $params[ 'columns' ] ) ||
 			 false == count( $params[ 'columns' ] ) ) return $params[ 'empty_message' ];

		$html = "";
		if( false == empty( $collection ) ) {
			/**
			* merge defaults and saved user settings
			*/
			$settings = $this->view->user->getSettings();
			if( isset( $settings[ $params[ 'id' ] ][ 'columns' ] ) ){
				foreach( $settings[ $params[ 'id' ] ][ 'columns' ] as $column_name => $column_params ){
					$params[ 'columns' ][ $column_name ][ 'hidden' ] = $column_params[ 'hidden' ];
					if( true == $column_params[ 'filter' ] ){
						$params[ 'columns' ][ $column_name ][ 'filter' ] = $column_params[ 'filter' ];
					}
					if( true == $column_params[ 'order' ] ){
						foreach( $params[ 'columns' ] as $field => $col ){
							$params[ 'columns' ][ $field ][ 'order' ] = null;
						}
						$params[ 'columns' ][ $column_name ][ 'order' ] = $column_params[ 'order' ];
					}
				}
			}
			if( isset( $settings[ $params[ 'id' ] ][ 'sequence' ] ) &&
				 true == count( $settings[ $params[ 'id' ] ][ 'sequence' ] )  ){
				$params[ 'sequence' ] = $settings[ $params[ 'id' ] ][ 'sequence' ]; 
			}

			/**
			* emit to js
			* - hidden columns list
			* - sequence
			* - filterable columns 
			*/
			$hidden_columns = array();
			$filterable_columns = array();
			foreach( $params[ 'columns' ] as $field => $column ){
				if( true == $column[ 'hidden' ] ){
					$hidden_columns[ $field ] = $column[ 'name' ];
				}
				if( true == $column[ 'filter' ] ){
					$filterable_columns[ $field ] = $column[ 'filter' ];
				}
			}
			$html .= "<script type='text/javascript'>" .
						"var hidden_columns = " . ( false == empty( $hidden_columns ) ? json_encode( $hidden_columns ) . ";" : "{};" ) .
						"var filterable_columns = " . ( false == empty( $filterable_columns ) ? json_encode( $filterable_columns ) . ";" : "{};" ) .  
						"var sequence = " . json_encode( $params[ 'sequence' ] ) . "</script>";

			/**
			* caption and header row
			*/
			$html .= "<table" . ( $params[ 'id' ] ? " id='{$params[ 'id' ]}'" : "" )
							  . ( $params[ 'resource' ] ? " resource='{$params[ 'resource' ]}'" : "" )
							  . ( $params[ 'edit_url' ] ? " edit_url='{$params[ 'edit_url' ]}'" : "" )
							  . ( $params[ 'delete_url' ] ? " delete_url='{$params[ 'delete_url' ]}'" : "" )
							  . ( $params[ 'edit_class' ] ? " edit_class='{$params[ 'edit_class' ]}'" : "" )
							  . ( $params[ 'delete_class' ] ? " delete_class='{$params[ 'delete_class' ]}'" : "" )
			 		 . ">" .
					  ( $params[ 'caption' ] ? "<caption>{$params[ 'caption' ]}</caption>" : "" ) . "<thead><tr>";

			foreach( $params[ 'sequence' ] as $key => $field ){
				$column = $params[ 'columns' ][ $field ];
				if( true == $column[ 'hidden' ] ) continue;

				$name = false !== $column[ 'orderable' ] ? "<span class='change_order'>{$column[ 'name' ]}</span>" : $column[ 'name' ];
				if( "asc" == $column[ 'order' ] ){
					$order_icon .= "<div class='icon_asc'></div>";
				} elseif( "desc" == $column[ 'order' ] ){
					$order_icon .= "<div class='icon_desc'></div>";
				} else {
					$order_icon = "";
				}

				$html .= "<th id='$field' sequence='$key'><div class='name'>{$name}{$order_icon}</div>";
				if( true == $params[ 'flexible_columns' ] ){
					$html .= "<div class='remove_column_icon'></div>";
				}
				if( true == $column[ 'filter' ] ){
					$pinned = true == $column[ 'filter' ][ 'value' ] ? "pinned" : "";
					$html .= "<div class='filter_column_icon $pinned'></div>";
				}
				if( true == $params[ 'flexible_columns' ] ){
					$html .=  false == empty( $hidden_columns ) ? "<div class='add_column_icon'></div>" : "";
				}
				$html .= "</th>";
			}
			if( $this->view->allowed( $params[ 'resource' ], 'edit' ) ||
				 $this->view->allowed( $params[ 'resource' ], 'delete' ) ){
				$html .= '<th class="controls"></th>';
			}
			$html .= '</tr></thead>';

			/**
			* footer row
			*/
			$html .= "<tfoot><tr><td colspan='" . count( $params[ 'sequence' ] ) . "'>";
			$html .= $this->view->paginationControl( $collection );
			$html .= '<div class="quantity_handler">
						по
						<input type="text" value="' . $settings[ $params[ 'id' ] ][ 'quantity' ] . '" class="change_quantity">
						 на странице
					  </div>';
			$html .= $this->view->allowed( $params[ 'resource' ], 'add' )
				   ? '<br /><button class="' . $params[ 'edit_class' ] . ' add"><div class="icon"></div></button>'
				   : '';
			$html .= "</td></tr></tfoot>";
			
			/**
			* content row
			*/
			$html .= '<tbody>';
			foreach( $collection as $item ){
				$html .= '<tr item_name="' . $item->__toString() . '" item_id="' . $item->getId() . '">';
				foreach( $params[ 'sequence' ] as $field ){
					$column = $params[ 'columns' ][ $field ];
					if( true == $column[ 'hidden' ] ) continue;
					if( isset( $column[ 'getter' ] ) ){
						$args = $column[ 'getter' ][ 'methods' ];
						foreach( $args as $key => $arg ){
							if( "$" == substr( $arg, 0, 1 ) ||
							 	"(" == substr( $arg, 0, 1 ) ){
								$args[ $key ] = eval( 'return ' . $arg );
							} else {
								$args[ $key ] = $item->$arg();
							}
						}

						$html .= vsprintf( $column[ 'getter' ][ 'string' ], $args );
					} else {
						$getter = $this->fieldToGetter( $field );
						$html .= '<td>' . $item->$getter() . '</td>';
					}
				}
				
				$delete = true == $params[ 'delete_button_text' ] &&
                  $this->view->allowed( $params[ 'resource' ], 'delete' );
				if( true == ( $edit = $this->view->allowed( $params[ 'resource' ], 'edit' ) ) ||
					 true == $delete ){
					$html .= '<td class="controls">' 
									. ( $edit ? '<button class="' . $params[ 'edit_class' ] . ' edit"><div class="icon"></div></button>'
											  : '' )
									. ( $delete ? '<button class="' . $params[ 'delete_class' ] . ' delete"><div class="icon"></div></button>'
												: '' )
						   . '</td>';
				}
				$html .= '</tr>';
			}
			$html .= '</tbody></table>';
		}
		return $html;
	}
	
	private function fieldToGetter( $field ){
		$parts = explode( "_", $field );
		foreach( $parts as $key => $part ){
			$parts[ $key ] = ucfirst( $part );
		}
		return "get" . implode( $parts );
	}
}
