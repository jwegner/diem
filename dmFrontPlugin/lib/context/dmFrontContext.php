<?php

require_once(sfConfig::get('dm_core_dir').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'context'.DIRECTORY_SEPARATOR.'dmContext.php');

class dmFrontContext extends dmContext
{
	protected
	  $page;
  
	protected function configureServiceContainer()
	{
	  parent::configureServiceContainer();
	  
    if ($this->getSfContext()->getUser()->can('front_edit'))
    {
      $this->serviceContainer->setParameter('page_helper.class', $this->serviceContainer->getParameter('page_helper.edit_class'));
      $this->getPageHelper()->setUser($this->sfContext->getUser());
    }
    else
    {
      $this->serviceContainer->setParameter('page_helper.class', $this->serviceContainer->getParameter('page_helper.view_class'));
    }
	}
  
  protected function configureUser()
  {
    parent::configureUser();
    
    /*
     * User require themeManager
     */
    $this->serviceContainer->addParameters(array(
      'theme_manager.options' => array(
        'list' => sfConfig::get('dm_theme_list'),
        'default' => sfConfig::get('dm_theme_default')
      )
    ));
    
    $this->sfContext->getUser()->setThemeManager($this->serviceContainer->getService('theme_manager'));
    
    /*
     * Set theme to user to ensure event firing
     */
    $this->sfContext->getUser()->getTheme();
  }
	  
  /*
   * @return DmPage the current page object
   */
  public function getPage()
  {
    return $this->page;
  }
  
  /*
   * @return dmFrontLayoutHelper
   */
  public function getLayoutHelper()
  {
    return $this->serviceContainer->getService('layout_helper');
  }

  /*
   * @return dmFrontPageHelper
   */
  public function getPageHelper()
  {
    return $this->serviceContainer->getService('page_helper');
  }
  
  /*
   * @return dmWidgetTypeManager
   */
  public function getWidgetTypeManager()
  {
    return $this->serviceContainer->getService('widget_type_manager');
  }
  
  /*
   * @return dmModule a project module
   */
  public function getModule()
  {
  	if (!$this->page)
  	{
  		return null;
  	}
  	
    return $this->page->getDmModule();
  }

  public function setPage(DmPage $page = null)
  {
    $this->page = $page;
    
    if (null !== $page)
    {
      $this->getPageHelper()->setPage($page);
      $this->getLayoutHelper()->setPage($page);
    }
  }

  public static function createInstance(sfContext $sfContext)
  {
    return self::$instance = new self($sfContext);
  }
}