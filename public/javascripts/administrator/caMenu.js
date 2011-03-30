/**
 * Last Modified: Februaary 02, 2007
 * @copyright CodeArts Nepal Pvt. Ltd.
 * @author Prajwal Tuladhar
 * @version 1.0
 * caMenu javascript behavior converted from cMenu based on MooTools
 * Prototype 1.6 Required. Tested at IE 7 and FF 1.5.x
 * 
 * @param {UL ID Name} el
 */
var caMenu = Class.create({
	initialize: function(el){
		try
		{			
			var menuElements = $(el).select('li');
			if (typeof menuElements != 'object' )	{
				throw el + " is not an object ID...";
			}
			else	{
				var nestedElement = null;		
				menuElements.each(function(menuElement){			
					menuElement.onmouseover = function()	{
						menuElement.addClassName('hover');
					}
					menuElement.onmouseout = function()	{
						menuElement.removeClassName('hover');
					}					
					//Find nest UL
					nestedElement = menuElement.select('ul');			
					//declare width
					var offsetWidth = 0;			
					//find longest child
					if (nestedElement.length == 1)
					{				
						nestedElement.each(function(item){					
							if (item.nodeName == "UL" || item.nodeName == "ul")	{
								(item.childElements().length).times(function(n)	{							
									if (item.childElements()[n].nodeName == "LI" || item.childElements()[n].nodeName == "li")
										offsetWidth = (offsetWidth >= item.childElements()[n].offsetWidth) ? offsetWidth :  item.childElements()[n].offsetWidth;								
								});						
								(item.childElements().length).times(function(n)	{
									if (item.childElements()[n].nodeName == "LI" || item.childElements()[n].nodeName == "li")										item.childElements()[n].setStyle('width', (offsetWidth) + 'px');
								});
								item.setStyle('width', (offsetWidth) +'px');	
							}	//End if		
						});	//End each	 
					}	//End if
				});	//End iteration for menu elements
			}	//End if
		}	//End try block
		catch (e)	{}
	}	//End of constructor
});
Event.observe(window, 'load', function() { var menu = new caMenu('menu'); });