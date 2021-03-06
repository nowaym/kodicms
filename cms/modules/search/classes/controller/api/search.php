<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Search
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Search extends Controller_System_Api {
	
	public function get_update_index()
	{		
		Observer::notify('update_search_index');
		
		$this->message('Search index updated');
	}
}