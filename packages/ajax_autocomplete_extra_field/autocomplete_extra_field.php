<?php
/**
 * @version		
 * @package	k2 Auto Complete Extra Field	
 * @author Rodrigo Emygdio da Silva		
 * @copyright	
 * @license		
 */

// no direct access
defined('_JEXEC') or die ;


class plgAjaxautocomplete_extra_field extends JPlugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->pluginName = 'autocomplete_extra_field';
		$this->loadLanguage();
	}

	
        
         function onAjaxAutocomplete_extra_field(){
             
                $mainframe  = JFactory::getApplication();
                $db         = JFactory::getDBO();
                $serach_for = $db->Quote('%'.$_POST['q'].'%');
                $id         = $mainframe->input->get('id');
                $result     = array();
                
                
               
                $query = $db->getQuery(true);
                $query->select('extra_fields,extra_fields_search')
                        ->from('#__k2_items')
                        ->where("extra_fields_search like $serach_for");
                $db->setQuery($query);                
                
                foreach ($db->loadAssocList() as $value){
                    
                     $field_value = json_decode($value["extra_fields"]);                     
                    foreach ($field_value as $extra_field ) {//                       
                        if($extra_field->id == $id && strpos($extra_field->value,$_POST['q'] )!== false ){                            
                            if(preg_match('/\r\n/', $extra_field->value)){
                               $multiple_values = explode("\r\n", $extra_field->value);
                               foreach($multiple_values as $value) {
                                   if(strpos($value,$_POST['q'] )!== false){
                                       $result[]=$value;
                                   }
                               }
                            }else{
                                $result[]= $extra_field->value;
                            }
                            
                            break;
                        }

                    }
                    
                } 
               
               return array_unique($result);

                
        }

}
