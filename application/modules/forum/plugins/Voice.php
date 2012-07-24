<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Plugin_Voice extends Core_Plugin_Voice {
	
	public static function undefinedCategory(){
		self::pushNotification(
			"Неверная категория форума, вы переадресованы в начало раздела «Форум»",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);
	}

	public static function undefinedPost(){
		self::pushNotification(
			"Неверный номер поста, вы переадресованы в начало раздела «Форум»",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);
	}

	public static function postEdited( Forum_Model_Post $post ){
		$text = $post->getId() ? 'Сообщение отредактировано' : 'Сообщение добавлено';
		self::pushNotification(
			$text,
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function postDeleted(){
		self::pushNotification(
			'Сообщение удалено',
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function topicEdited( $topic ){
		$action = true == $topic->getId() ? "отредактирован" : "создан";
		self::pushNotification(
			"Топик «{$topic->getTeaser()}» $action",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function topicDeleted( $topic ){
		self::pushNotification(
			"Топик «{$topic->getTeaser()}» удалён",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function categoryEdited( Forum_Model_Category $category ){
		$text = $category->getId() ? 'Категория отредактирована' : 'Категория добавлена';
		self::pushNotification(
			$text,
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function categoryDeleted( $category ){
		self::pushNotification(
			"Категория «{$category->getName()}» удалена",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}


	public static function bulletinEdited( $bulletin ){
		$action = true == $bulletin->getId() ? "отредактировано" : "подано";
		self::pushNotification(
			"Объявление «{$bulletin->getTeaser()}» $action",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

  public static function bulletinDeleted( $bulletin ){
      self::pushNotification(
        "Объявление «{$bulletin->getTeaser()}» удалено",
        array( "class" => Core_Model_Notification::SUCCESS_CLASS )
      );
  }

}
?>
