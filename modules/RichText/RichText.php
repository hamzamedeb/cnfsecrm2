<?php
class RichText	
{
	public function vtlib_handler($moduleName, $eventType)	{
		if ($eventType == 'module.postinstall')	{
			$this->_registerLinks($moduleName);
		} 
		else if ($eventType == 'module.enabled')	{
			$this->_registerLinks($moduleName);
		} 
		else if ($eventType == 'module.disabled')	{
			$this->_deregisterLinks($moduleName);
		}
	}

	protected function _registerLinks($moduleName)	{
		$thisModuleInstance = Vtiger_Module::getInstance($moduleName);
		if ($thisModuleInstance) {
			$thisModuleInstance->addLink("HEADERSCRIPT", "RichText1", "modules/RichText/js/RichText.js");
			$thisModuleInstance->addLink("HEADERSCRIPT", "RichText2", "modules/RichText/js/ckeditor/ckeditor.js");
		}
	}

	protected function _deregisterLinks($moduleName)	{
		$thisModuleInstance = Vtiger_Module::getInstance($moduleName);
		if($thisModuleInstance)	{
			$thisModuleInstance->deleteLink("HEADERSCRIPT", "RichText1", "modules/RichText/js/RichText.js");
			$thisModuleInstance->deleteLink("HEADERSCRIPT", "RichText2", "modules/RichText/js/ckeditor/ckeditor.js");
		}
	}
}
