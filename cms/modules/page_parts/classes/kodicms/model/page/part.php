<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Page_Part extends ORM
{
	const PART_NOT_PROTECTED = 0;
	const PART_PROTECTED = 1;
	
	protected $_reload_on_wakeup = FALSE;

	protected $_belongs_to = array(
		'page' => array(
			'model'			=> 'page',
			'foreign_key'	=> 'page_id'
		)
	);
	
	public function labels()
	{
		return array(
			'name'				=> __('Parn name'),
			'filter_id'			=> __('Filter'),
			'content'			=> __('Content'),
			'content_html'		=> __('HTML content'),
			'is_protected'		=> __('Is protected'),
			'is_expanded'		=> __('Is expanded'),
			'is_indexable'		=> __('Is indexable')
		);
	}
	
	public function rules() 
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 50))
			)
		);
	}

	public function before_save()
	{
		if (!empty($this->filter_id))
		{
			if (WYSIWYG::get($this->filter_id))
			{
				$filter_class = WYSIWYG::get($this->filter_id);

				if ($filter_class !== FALSE)
				{
					$this->content_html = $filter_class->apply($this->content);
					return TRUE;
				}
			}
		}
		
		if($this->filter_id === NULL)
		{
			$this->filter_id = Config::get('site', 'default_filter_id');
		}
		
		if($this->is_protected === NULL)
		{
			$this->is_protected = self::PART_NOT_PROTECTED;
		}
		
		if($this->name === NULL)
		{
			$this->name = 'part';
		}

		$this->content_html = $this->content;

		Observer::notify('part_before_save', $this);
		
		return TRUE;
	}
	
	public function after_save()
	{
		Observer::notify('part_after_save', $this);
		
		Cache::instance()->delete_tag('page_parts');
		return parent::after_save();
	}
	
	public function after_delete($id)
	{
		Cache::instance()->delete_tag('page_parts');
		return parent::after_save();
	}
}