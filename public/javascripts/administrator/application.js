/**
* Default function.  Usually would be overriden by the component
*/
function submitbutton(pressbutton) {
	submitform(pressbutton);
}

/**
* Submit the admin form
*/
function submitform(pressbutton){
	document.adminForm.task.value=pressbutton;
	try {
		document.adminForm.onsubmit();
		}
	catch(e){}
	document.adminForm.submit();
}

/**
* Submit the control panel admin form
*/
function submitcpform(sectionid, id){
	document.adminForm.sectionid.value=sectionid;
	document.adminForm.id.value=id;
	submitbutton("edit");
}


function submitForm(pressbutton,validate){
	document.adminForm.act.value=pressbutton;			
	document.adminForm.method="post";
	document.adminForm.action="index2.php";
	
	if(validate==1) {
		if (Spry.Widget.Form.validate(document.adminForm) == true){
			document.adminForm.submit();
		}
	} else if(validate==2) {		
		n=100;	
		fldName = 'grid_checkbox';	
		var f = document.adminForm;
		
		var n2 = 0;
							
		for (i=0; i < n; i++) {
			cb = eval( 'f.' + fldName + '' + i );		
			if (cb) {			
				if(cb.checked) {
					//alert(cb.value);
					document.adminForm.id.value=cb.value;
					n2++;
				}			
			}		
		}
		if(n2<1) {
				alert ('Please select atleast an item to '+pressbutton);				
		}
		else {	
			 if(confirm ('Are you sure you want to '+pressbutton)) {				
				document.adminForm.submit();				
			}		
		}	
	}
	else {
		document.adminForm.submit();
	}	
}

function submitEdit(pressbutton,validate){
	document.adminForm.act.value=pressbutton;
	document.adminForm.method="get";
	document.adminForm.action="index.php";
	
	n=100;
	
	fldName = 'grid_checkbox';	
	var f = document.adminForm;
	
	var n2 = 0;
						
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );		
		if (cb) {			
			if(cb.checked) {
				//alert(cb.value);
				document.adminForm.id.value=cb.value;
				n2++;
			}			
		}
		
	}
	//try {
		//document.adminForm.onsubmit();
	//}
	//catch(e){}
	if(n2>1) {
				alert ('Please select only one item to '+pressbutton);				
	}
	else if(n2==0) {
				alert ('Please select an item to '+pressbutton);				
	}
	else {			
		if(validate) {
			if (Spry.Widget.Form.validate(document.adminForm) == true){
				document.adminForm.submit();
			}
		} else {
			document.adminForm.submit();
		}
	}
}

function liveUrlTitle()
{
	var defaultTitle = '';
	var NewText = document.adminForm.title.value;
	//var NewText = document.getElementById("title").value;
	
	if (defaultTitle != '')
	{
		if (NewText.substr(0, defaultTitle.length) == defaultTitle)
		{
			NewText = NewText.substr(defaultTitle.length)
		}	
	}
	
	NewText = NewText.toLowerCase();
	var separator = "_";
	
	if (separator != "_")
	{
		NewText = NewText.replace(/\_/g, separator);
	}
	else
	{
		NewText = NewText.replace(/\-/g, separator);
	}

	// Foreign Character Attempt
	
	var NewTextTemp = '';
	for(var pos=0; pos<NewText.length; pos++)
	{
		var c = NewText.charCodeAt(pos);
		
		if (c >= 32 && c < 128)
		{
			NewTextTemp += NewText.charAt(pos);
		}
		else
		{
			if (c == '223') {NewTextTemp += 'ss'; continue;}
			if (c == '224') {NewTextTemp += 'a'; continue;}
			if (c == '225') {NewTextTemp += 'a'; continue;}
			if (c == '226') {NewTextTemp += 'a'; continue;}
			if (c == '229') {NewTextTemp += 'a'; continue;}
			if (c == '227') {NewTextTemp += 'ae'; continue;}
			if (c == '230') {NewTextTemp += 'ae'; continue;}
			if (c == '228') {NewTextTemp += 'ae'; continue;}
			if (c == '231') {NewTextTemp += 'c'; continue;}
			if (c == '232') {NewTextTemp += 'e'; continue;}
			if (c == '233') {NewTextTemp += 'e'; continue;}
			if (c == '234') {NewTextTemp += 'e'; continue;}
			if (c == '235') {NewTextTemp += 'e'; continue;}
			if (c == '236') {NewTextTemp += 'i'; continue;}
			if (c == '237') {NewTextTemp += 'i'; continue;}
			if (c == '238') {NewTextTemp += 'i'; continue;}
			if (c == '239') {NewTextTemp += 'i'; continue;}
			if (c == '241') {NewTextTemp += 'n'; continue;}
			if (c == '242') {NewTextTemp += 'o'; continue;}
			if (c == '243') {NewTextTemp += 'o'; continue;}
			if (c == '244') {NewTextTemp += 'o'; continue;}
			if (c == '245') {NewTextTemp += 'o'; continue;}
			if (c == '246') {NewTextTemp += 'oe'; continue;}
			if (c == '249') {NewTextTemp += 'u'; continue;}
			if (c == '250') {NewTextTemp += 'u'; continue;}
			if (c == '251') {NewTextTemp += 'u'; continue;}
			if (c == '252') {NewTextTemp += 'ue'; continue;}
			if (c == '255') {NewTextTemp += 'y'; continue;}
			if (c == '257') {NewTextTemp += 'aa'; continue;}
			if (c == '269') {NewTextTemp += 'ch'; continue;}
			if (c == '275') {NewTextTemp += 'ee'; continue;}
			if (c == '291') {NewTextTemp += 'gj'; continue;}
			if (c == '299') {NewTextTemp += 'ii'; continue;}
			if (c == '311') {NewTextTemp += 'kj'; continue;}
			if (c == '316') {NewTextTemp += 'lj'; continue;}
			if (c == '326') {NewTextTemp += 'nj'; continue;}
			if (c == '353') {NewTextTemp += 'sh'; continue;}
			if (c == '363') {NewTextTemp += 'uu'; continue;}
			if (c == '382') {NewTextTemp += 'zh'; continue;}
			if (c == '256') {NewTextTemp += 'aa'; continue;}
			if (c == '268') {NewTextTemp += 'ch'; continue;}
			if (c == '274') {NewTextTemp += 'ee'; continue;}
			if (c == '290') {NewTextTemp += 'gj'; continue;}
			if (c == '298') {NewTextTemp += 'ii'; continue;}
			if (c == '310') {NewTextTemp += 'kj'; continue;}
			if (c == '315') {NewTextTemp += 'lj'; continue;}
			if (c == '325') {NewTextTemp += 'nj'; continue;}
			if (c == '352') {NewTextTemp += 'sh'; continue;}
			if (c == '362') {NewTextTemp += 'uu'; continue;}
			if (c == '381') {NewTextTemp += 'zh'; continue;}		
		}
	}

	NewText = NewTextTemp;
	
	NewText = NewText.replace('/<(.*?)>/g', '');
	NewText = NewText.replace('/\&#\d+\;/g', '');
	NewText = NewText.replace('/\&\#\d+?\;/g', '');
	NewText = NewText.replace('/\&\S+?\;/g','');
	NewText = NewText.replace(/['\"\?\.\!*$\#@%;:,=\(\)\[\]]/g,'');
	NewText = NewText.replace(/\s+/g, separator);
	NewText = NewText.replace(/\//g, separator);
	NewText = NewText.replace(/[^a-z0-9-_]/g,'');
	NewText = NewText.replace(/\+/g, separator);
	NewText = NewText.replace(/[-_]+/g, separator);
	NewText = NewText.replace(/\&/g,'');
	NewText = NewText.replace(/-$/g,'');
	NewText = NewText.replace(/_$/g,'');
	NewText = NewText.replace(/^_/g,'');
	NewText = NewText.replace(/^-/g,'');

	if (document.adminForm.alias)
	{
		document.adminForm.alias.value = "" + NewText;			
	}
	else
	{
		document.adminForm.alias.value = "" + NewText; 
	}
}

	
/**
* Pops up a new window in the middle of the screen
*/
function popupWindow(mypage, myname, w, h, scroll) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
	win = window.open(mypage, myname, winprops)
	if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}

// LTrim(string) : Returns a copy of a string without leading spaces.
function ltrim(str)
{
   var whitespace = new String(" \t\n\r");
   var s = new String(str);
   if (whitespace.indexOf(s.charAt(0)) != -1) {
      var j=0, i = s.length;
      while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
         j++;
      s = s.substring(j, i);
   }
   return s;
}

//RTrim(string) : Returns a copy of a string without trailing spaces.
function rtrim(str)
{
   var whitespace = new String(" \t\n\r");
   var s = new String(str);
   if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
      var i = s.length - 1;       // Get length of string
      while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
         i--;
      s = s.substring(0, i+1);
   }
   return s;
}

// Trim(string) : Returns a copy of a string without leading or trailing spaces
function trim(str) {
   return rtrim(ltrim(str));
}

// image block display or hide	
function replaceImage(id){
	if(document.getElementById(id).style.display == "none"){
		document.getElementById(id).style.display = "block";
		document.getElementById('replace').value = "";
	} else {
		document.getElementById(id).style.display = "none";
		document.getElementById('replace').value = document.adminForm.image.value;
	}
}

// JS Calendar
var calendar = null; // remember the calendar object so that we reuse
// it and avoid creating another

// This function gets called when an end-user clicks on some date
function selected(cal, date) {
	cal.sel.value = date; // just update the value of the input field
}

// And this gets called when the end-user clicks on the _selected_ date,
// or clicks the "Close" (X) button.  It just hides the calendar without
// destroying it.
function closeHandler(cal) {
	cal.hide();			// hide the calendar

	// don't check mousedown on document anymore (used to be able to hide the
	// calendar when someone clicks outside it, see the showCalendar function).
	Calendar.removeEvent(document, "mousedown", checkCalendar);
}

// This gets called when the user presses a mouse button anywhere in the
// document, if the calendar is shown.  If the click was outside the open
// calendar this function closes it.
function checkCalendar(ev) {
	var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
	for (; el != null; el = el.parentNode)
	// FIXME: allow end-user to click some link without closing the
	// calendar.  Good to see real-time stylesheet change :)
	if (el == calendar.element || el.tagName == "A") break;
	if (el == null) {
		// calls closeHandler which should hide the calendar.
		calendar.callCloseHandler(); Calendar.stopEvent(ev);
	}
}

// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.
function showCalendar(id) {
	var el = document.getElementById(id);
	if (calendar != null) {
		// we already have one created, so just update it.
		calendar.hide();		// hide the existing calendar
		calendar.parseDate(el.value); // set it to a new date
	} else {
		// first-time call, create the calendar
		var cal = new Calendar(true, null, selected, closeHandler);
		calendar = cal;		// remember the calendar in the global
		cal.setRange(1900, 2070);	// min/max year allowed
		calendar.create();		// create a popup calendar
		calendar.parseDate(el.value); // set it to a new date
	}
	calendar.sel = el;		// inform it about the input field in use
	calendar.showAtElement(el);	// show the calendar next to the input field

	// catch mousedown on the document
	Calendar.addEvent(document, "mousedown", checkCalendar);
	return false;
}


function AjaxRequest(URL, load_ajax){
	new Ajax.Request(URL, {
	  method: 'get',
	  onLoading: function(){
		var notice = $(load_ajax);
		notice.show();
		//notice.update('Loading...').setStyle({ background: '#dfd' });
	  },
	  onSuccess: function(transport) {
		var notice = $(load_ajax);
		notice.update();
		notice.update(transport.responseText);
	  },
	  onFailure: function(){
		var notice = $(load_ajax);
		notice.update('Error Occured!');
	  }
	});
}