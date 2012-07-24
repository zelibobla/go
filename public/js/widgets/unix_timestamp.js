/******************************************************
*
*   Cистема для управления грузоперевозками
*   версия 1.0.0
*
*   собственность компании ООО «ТрансКарго-Менеджер»
*
*   программист Антон Зеленский zelibobla@gmail.com
*   май - июнь 2011
*
*	 JQuery плагин для работы полем формы «дата»
*	 плагин вешается на скрытый элемент формы, рядом рисует поле для ввода даты, все изменения которого переводит в unix timestamp
*	 и записывает в скрытый элемент формы, на который был повешен
*
*	 Зависимости:
*	 jquery-1.5.2
*	 jquery-ui-1.8.12
*
******************************************************/

(function( $ ){

		var settings = {
		};

		var methods = {
		
			init : function( options ) {

				if( options ){
					$.extend( settings, options );
				}

				settings[ 'index' ] = this.attr( 'id' );
				settings[ 'input' ] = this;

					
				// поскольку нас не волнуют микросекунды, откажемся от longint в БД
				// но объект date в javascript хочет на входе ещё три нуля к int, что и скармливаем
				var now = new Date();
				date = this.val() ? new Date( parseInt( this.val() + '000' ) ) : parseInt( now.getTime().toString().substr( 0, 10 ) );
				
				$( this ).after( '<input type="text" id="' + settings[ 'index' ] + '__datepicker" value="">' );

				$( '#' + settings[ 'index' ] + '__datepicker' ).datepicker({
					firstDay: 1,
					dateFormat: 'dd M yy',
					gotoCurrent: true,
					showAnim: 'slideDown',
					monthNames: [ 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сенябрь', 'октябрь', 'ноябрь', 'декабрь' ],
					monthNamesShort: [ 'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек' ],
					dayNamesMin: [ 'вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб' ]
				} );
				$( '#' + settings[ 'index' ] + '__datepicker' ).datepicker( 'setDate', date );

				$( '#' + settings[ 'index' ] + '__datepicker' ).bind( 'change.unixTimestamp', methods.update );

				return this;
	
			},
			
			update : function(){

				changed_id = $( this ).attr( 'id' );
				hidden_id = '#' + changed_id.substr( 0, changed_id.indexOf( '__' ) );
				date = $( this ).datepicker( "getDate" );
				$( hidden_id ).val( date.getTime().toString().substr( 0, 10 ) );

			}

		};

		$.fn.unixTimestamp = function( method ) {

			// Method calling logic
			if ( methods[ method ] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || !method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on jQuery.unixTimestamp' );
			}    

	 	};

})( jQuery );
