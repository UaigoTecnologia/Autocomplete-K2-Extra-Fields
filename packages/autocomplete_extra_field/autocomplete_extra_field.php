<?php
/**
 * @version		
 * @package K2 Autocomplete Extra Field		
 * @author Rodrigo Emygdio da Silva		
 * @copyright	
 * @license		
 */

// no direct access
defined('_JEXEC') or die ;

JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php');

class plgK2autocomplete_extra_field extends K2Plugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->pluginName = 'autocomplete_extra_field';
		$this->loadLanguage();
	}

	public function onRenderAdminForm(&$item, $type, $tab = '') {
            static $first_time = 1;
            
           
            if($first_time == 1){
            $doc = JFactory::getDocument();
            $auto_complete_field_id = $this->params->get('id_field_auto_fill');
            $id_extra_field         = explode('_', $auto_complete_field_id)[1];
            $k2_site_path = JUri::root();
            $extra_fields_value = strlen($item->extra_fields) > 0 ? htmlspecialchars_decode(addslashes($item->extra_fields)): '[]';
            
            $auto_complete_field = <<<"Eof"
              function removeValue(value){
                   var field = document.getElementsByName('$auto_complete_field_id')[0];
                   var value_to_replace = value + ''\r\n'';
                   //field.value = field.value.replace(value_to_replace,'');
                    field.value = field.value.replace(RegExp(value_to_replace, 'g'),'');
                    if(field.value == "\\\\r\\\\n"){
                            field.value = '';
                    }
               }
                    function createInitialUlLi(field){
                        
                        var ul_initial = document.createElement('ul');
                        var li_initial = document.createElement('li');
                        var td_parent  = field.parentNode;
                    
                        ul_initial.setAttribute('class','field_$id_extra_field');
                        li_initial.setAttribute('class','fieldAdd_$id_extra_field');
                        
                        li_initial.appendChild(field);
                        ul_initial.appendChild(li_initial);
                        td_parent.appendChild(ul_initial);
                    }
               function createLi(extra_field_value){
                   var li = document.createElement('li');                       
                       ul = document.getElementsByClassName('field_$id_extra_field')[0];    
                       
                       li.setAttribute('class',"fieldAdded_$id_extra_field");
                       li.innerHTML =  extra_field_value + '<span class="tagRemove" onclick="\$K2(this).parent().remove();removeValue(\'' + extra_field_value + '\')">x</span>';
                       ul.appendChild(li);
                                          
                        
                    
                }
              window.addEvent('domready', function() {
                var  K2SitePath = '$k2_site_path';
                                                  
                var extra_field_values = $extra_fields_value;
                
                var field = document.getElementById('$auto_complete_field_id');
                createInitialUlLi(field);
                if(field.hasChildNodes() != true){
                    var input = document.createElement('input');
                    input.setAttribute('type','hidden');
                    input.setAttribute('name','$auto_complete_field_id');
                    field.insertBefore(input,field.parentNode[0]);
                    field.name ='';
                    
                }
                var field_extra_field = document.getElementsByName('$auto_complete_field_id')[0];
                    //field_extra_field.value = field.getAttribute('value');
                //Constroi as opções selecionadas
                   
                    for(var i = 0; i < extra_field_values.length;i++){
                        if(extra_field_values[i].id == $id_extra_field){
                            if(extra_field_values[i].value.length > 0){
                                var values_extra_field_array = field.getAttribute('value').replace(/(\\n)/g,'/').split('/');
                                for(var index =0; index < values_extra_field_array.length;index++){
                                    createLi(values_extra_field_array[index]);
                                    field_extra_field.value += values_extra_field_array[index] + '\\r\\n';
                                }
                            }
                        }
                    }
                //Zera o input padrão do k2    
                field.value ='';
                 \$K2('#$auto_complete_field_id').keypress(function(event) {
                        if (event.which == '13') {
                            if (\$K2(this).val() != '') {                                
                                 createLi(\$K2(this).val());
                                \$K2(this).val().replace(RegExp('\\n', 'g'),'');
                                \$K2(this).val().replace(RegExp('\\r', 'g'),''); 
                                        
                                this.childNodes[0].value = this.childNodes[0].value +\$K2(this).val()+ '\\r\\n';
                                this.value='';
                            }
                        }
                  });
                  \$K2('#$auto_complete_field_id').autocomplete({
                        source : function(request, response) {
                            \$K2.ajax({
                            type : 'post',
                            url : K2SitePath + 'index.php?option=com_ajax&plugin=autocomplete_extra_field&format=json&id=$id_extra_field',
                            data : 'q=' + request.term,
                            dataType : 'json',
                            success : function(data) {
                                    \$K2('#$auto_complete_field_id').removeClass('tagsLoading');
                                    response(\$K2.map( data.data[0], function(item) {
                                    return item;
                                    }));
                                }
                            });
                        },
                        minLength : 3,
                        select : function(event, ui) {
                            createLi(ui.item.value);
                            this.value = '';
                            ui.item.value = ui.item.value.replace(RegExp('\\n', 'g'),'');
                            ui.item.value = ui.item.value.replace(RegExp('\\r', 'g'),'');
                            this.childNodes[0].value = this.childNodes[0].value + ui.item.value + '\\r\\n';
                            return false;
                        },
                        search : function(event, ui) {
                            \$K2('#$auto_complete_field_id').addClass('tagsLoading');
                        }
                 }); 
            });
Eof;
            $cs_auto_complte = <<<"Eof"
            ul.field_$id_extra_field li {
                background: none repeat scroll 0 0 #CFE4FA;
                border: 1px solid #B0D2F9;
                border-radius: 4px;
                clear: none;
                color: #222222;
                float: left;
                font-family: "Lucida Grande";
                font-size: 12px;
                font-weight: normal;
                line-height: 12px;
                list-style: none outside none;
                margin: 2px 1px 1px;
                padding: 2px 4px 4px;
            }
            ul.field_$id_extra_field {
                cursor: default;
            }
            ul.field_$id_extra_field li.fieldAdd_$id_extra_field input {
                border: medium none;
            }
            ul.field_$id_extra_field li input {
                background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
                border: medium none;
                clear: none;
                float: left;
                font-family: "Lucida Grande";
                font-size: 12px;
                font-weight: normal;
                padding-top: 2px;
            }
           ul.field_$id_extra_field li span.tagRemove {
                color: #5279A1;
                cursor: pointer;
                font-family: "Lucida Grande";
                font-size: 12px;
                padding-left: 4px;
            }               
Eof;
            $doc->addStyleDeclaration($cs_auto_complte);            
            $doc->addScriptDeclaration($auto_complete_field);
            }
            ++$first_time;
            parent::onRenderAdminForm($item, $type, $tab);
        }
}
