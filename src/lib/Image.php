<?php
/**
 * Copyright (c) 2018.
 * Author: YiiMan
 * Programmer: gholamreza beheshtian
 * mobile: 09353466620
 * WebSite:http://yiiman.ir
 *
 *
 */

/**
 * Created by PhpStorm.
 * User: amintado
 * Date: 7/29/2018
 * Time: 4:52 PM
 */

namespace YiiMan\LibUploadManager\lib;


use yii\base\Component;
use yii\web\View;

class Image extends Component {

	public function begin($view){

		$js=<<<JS
				function init() {
					var imgDefer = document.getElementsByTagName('img');
					for (var i=0; i<imgDefer.length; i++) {
							if(imgDefer[i].getAttribute('data-src')) {
								imgDefer[i].setAttribute('src',imgDefer[i].getAttribute('data-src'));
							} 
					} 
				}
				window.onload = init;

JS;


		/**
		 * @var $view \yii\web\View
		 */
		$view->registerJs( $js,View::POS_BEGIN);
	}

	public function img($url){
		$sign="data:image/png;base64,";
		$tag='<img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="your-image-is-here">';
	}
}