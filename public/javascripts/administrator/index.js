//For IE6 - Background flicker fix
try {
	document.execCommand('BackgroundImageCache', false, true);
} catch (e) {}

document.menu = null;

submitbutton = function (pressbutton, options) {
	count = 0;
	if ($$('#ca_checkbox') !== null) {
		$$('#ca_checkbox').each(function (box) {
			if (box.checked === true) {
				++count;
			}
		});

		if (count > 0) {
			document.frm_ca_gridview.action = pressbutton;
			document.frm_ca_gridview.submit();
		}
	}
	return count;
};

submitedit = function (pressbutton, options) {
	count = 0;
	id = 0;

	if ($$('#ca_checkbox') !== null) {
		$$('#ca_checkbox').each(function (box) {
			if (box.checked === true) {
				++count;
				id = box.value;
			}
		});
	}

	if (count === 1) {
		document.frm_ca_gridview.action = pressbutton + "/" + id;
		document.frm_ca_gridview.submit();
	}

	return count;
};

// Submit the admin form
submitform = function (pressbutton, options) {
	document.adminForm.action = pressbutton;

	try {
		document.adminForm.onsubmit();
	} catch (e) {}

	if (options !== null) {
		if (options.validate === true) {
			if (Spry.Widget.Form.validate(document.adminForm) === true) {
				document.adminForm.submit();
			}
		} else {
			document.adminForm.submit();
		}
	} else {
		document.adminForm.submit();
	}
};

submitEdit = function (pressbutton, validate) {
	document.adminForm.act.value = pressbutton;
	document.adminForm.method = "get";
	document.adminForm.action = "index.php";

	n = 100;

	fldName = 'grid_checkbox';
	var f = document.adminForm;

	var n2 = 0;

	for (i = 0; i < n; i++) {
		cb = eval('f.' + fldName + i);
		if (cb) {
			if (cb.checked) {				
				document.adminForm.id.value = cb.value;
				n2++;
			}
		}

	}

	if (n2 > 1) {
		alert('Please select only one item to ' + pressbutton);
	} else if (n2 === 0) {
		alert('Please select an item to ' + pressbutton);
	} else {
		if (validate) {
			if (Spry.Widget.Form.validate(document.adminForm) === true) {
				document.adminForm.submit();
			}
		} else {
			document.adminForm.submit();
		}
	}
};

liveUrlTitle = function () {
	var defaultTitle = '';
	var NewText = document.adminForm.title.value;
	//var NewText = document.getElementById("title").value;
	if (defaultTitle !== '') {
		if (NewText.substr(0, defaultTitle.length) === defaultTitle) {
			NewText = NewText.substr(defaultTitle.length);
		}
	}

	NewText = NewText.toLowerCase();
	var separator = "_";

	if (separator !== "_") {
		NewText = NewText.replace(/\_/g, separator);
	} else {
		NewText = NewText.replace(/\-/g, separator);
	}

	// Foreign Character Attempt
	var NewTextTemp = '', pos = 0;
	for (pos = 0; pos < NewText.length; pos++) {
		var c = NewText.charCodeAt(pos);

		if (c >= 32 && c < 128) {
			NewTextTemp += NewText.charAt(pos);
		} else {
			if (c === '223') {
				NewTextTemp += 'ss';
				continue;
			}
			if (c === '224') {
				NewTextTemp += 'a';
				continue;
			}
			if (c === '225') {
				NewTextTemp += 'a';
				continue;
			}
			if (c === '226') {
				NewTextTemp += 'a';
				continue;
			}
			if (c === '229') {
				NewTextTemp += 'a';
				continue;
			}
			if (c === '227') {
				NewTextTemp += 'ae';
				continue;
			}
			if (c === '230') {
				NewTextTemp += 'ae';
				continue;
			}
			if (c === '228') {
				NewTextTemp += 'ae';
				continue;
			}
			if (c === '231') {
				NewTextTemp += 'c';
				continue;
			}
			if (c === '232') {
				NewTextTemp += 'e';
				continue;
			}
			if (c === '233') {
				NewTextTemp += 'e';
				continue;
			}
			if (c === '234') {
				NewTextTemp += 'e';
				continue;
			}
			if (c === '235') {
				NewTextTemp += 'e';
				continue;
			}
			if (c === '236') {
				NewTextTemp += 'i';
				continue;
			}
			if (c === '237') {
				NewTextTemp += 'i';
				continue;
			}
			if (c === '238') {
				NewTextTemp += 'i';
				continue;
			}
			if (c === '239') {
				NewTextTemp += 'i';
				continue;
			}
			if (c === '241') {
				NewTextTemp += 'n';
				continue;
			}
			if (c === '242') {
				NewTextTemp += 'o';
				continue;
			}
			if (c === '243') {
				NewTextTemp += 'o';
				continue;
			}
			if (c === '244') {
				NewTextTemp += 'o';
				continue;
			}
			if (c === '245') {
				NewTextTemp += 'o';
				continue;
			}
			if (c === '246') {
				NewTextTemp += 'oe';
				continue;
			}
			if (c === '249') {
				NewTextTemp += 'u';
				continue;
			}
			if (c === '250') {
				NewTextTemp += 'u';
				continue;
			}
			if (c === '251') {
				NewTextTemp += 'u';
				continue;
			}
			if (c === '252') {
				NewTextTemp += 'ue';
				continue;
			}
			if (c === '255') {
				NewTextTemp += 'y';
				continue;
			}
			if (c === '257') {
				NewTextTemp += 'aa';
				continue;
			}
			if (c === '269') {
				NewTextTemp += 'ch';
				continue;
			}
			if (c === '275') {
				NewTextTemp += 'ee';
				continue;
			}
			if (c === '291') {
				NewTextTemp += 'gj';
				continue;
			}
			if (c === '299') {
				NewTextTemp += 'ii';
				continue;
			}
			if (c === '311') {
				NewTextTemp += 'kj';
				continue;
			}
			if (c === '316') {
				NewTextTemp += 'lj';
				continue;
			}
			if (c === '326') {
				NewTextTemp += 'nj';
				continue;
			}
			if (c === '353') {
				NewTextTemp += 'sh';
				continue;
			}
			if (c === '363') {
				NewTextTemp += 'uu';
				continue;
			}
			if (c === '382') {
				NewTextTemp += 'zh';
				continue;
			}
			if (c === '256') {
				NewTextTemp += 'aa';
				continue;
			}
			if (c === '268') {
				NewTextTemp += 'ch';
				continue;
			}
			if (c === '274') {
				NewTextTemp += 'ee';
				continue;
			}
			if (c === '290') {
				NewTextTemp += 'gj';
				continue;
			}
			if (c === '298') {
				NewTextTemp += 'ii';
				continue;
			}
			if (c === '310') {
				NewTextTemp += 'kj';
				continue;
			}
			if (c === '315') {
				NewTextTemp += 'lj';
				continue;
			}
			if (c === '325') {
				NewTextTemp += 'nj';
				continue;
			}
			if (c === '352') {
				NewTextTemp += 'sh';
				continue;
			}
			if (c === '362') {
				NewTextTemp += 'uu';
				continue;
			}
			if (c === '381') {
				NewTextTemp += 'zh';
				continue;
			}
		}
	}

	NewText = NewTextTemp;

	NewText = NewText.replace('/<(.*?)>/g', '');
	NewText = NewText.replace('/\&#\d+\;/g', '');
	NewText = NewText.replace('/\&\#\d+?\;/g', '');
	NewText = NewText.replace('/\&\S+?\;/g', '');
	NewText = NewText.replace(/['\"\?\.\!*$\#@%;:,=\(\)\[\]]/g, '');
	NewText = NewText.replace(/\s+/g, separator);
	NewText = NewText.replace(/\//g, separator);
	NewText = NewText.replace(/[^a-z0-9-_]/g, '');
	NewText = NewText.replace(/\+/g, separator);
	NewText = NewText.replace(/[-_]+/g, separator);
	NewText = NewText.replace(/\&/g, '');
	NewText = NewText.replace(/-$/g, '');
	NewText = NewText.replace(/_$/g, '');
	NewText = NewText.replace(/^_/g, '');
	NewText = NewText.replace(/^-/g, '');

	if (document.adminForm.alias) {
		document.adminForm.alias.value = "" + NewText;
	} else {
		document.adminForm.alias.value = "" + NewText;
	}
};

// Pops up a new window in the middle of the screen
popupWindow = function (mypage, myname, w, h, scroll) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height=' + h + ',width=' + w + ',top=' + wint + ',left=' + winl + ',scrollbars=' + scroll + ',resizable'
	win = window.open(mypage, myname, winprops)
	if (parseInt(navigator.appVersion) >= 4) {
		win.window.focus();
	}
};

replaceImage = function (id) {
	if (document.getElementById(id).style.display === "none") {
		document.getElementById(id).style.display = "block";
		//document.getElementById('replace').value = "";
	} else {
		document.getElementById(id).style.display = "none";
		//document.getElementById('replace').value = document.adminForm.image.value;
	}
};

// LTrim(string) : Returns a copy of a string without leading spaces.
ltrim = function (str) {
	var whitespace = new String(" \t\n\r");
	var s = new String(str);
	if (whitespace.indexOf(s.charAt(0)) != -1) {
		var j = 0,
			i = s.length;
		while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
		j++;
		s = s.substring(j, i);
	}
	return s;
};

//RTrim(string) : Returns a copy of a string without trailing spaces.
rtrim = function (str) {
	var whitespace = new String(" \t\n\r");
	var s = new String(str);
	if (whitespace.indexOf(s.charAt(s.length - 1)) != -1) {
		var i = s.length - 1; // Get length of string
		while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
		i--;
		s = s.substring(0, i + 1);
	}
	return s;
};

// Trim(string) : Returns a copy of a string without leading or trailing spaces
trim = function (str) {
	return rtrim(ltrim(str));
};

AjaxRequest = function (URL, load_ajax) {
	new Ajax.Request(URL, {
		method: 'get',
		onLoading: function () {
			var notice = $(load_ajax);
			notice.show();
			//notice.update('Loading...').setStyle({ background: '#dfd' });
		},
		onSuccess: function (transport) {
			var notice = $(load_ajax);
			notice.update();
			notice.update(transport.responseText);
		},
		onFailure: function () {
			var notice = $(load_ajax);
			notice.update('Error Occured!');
		}
	});
};

// Set cookie
/* setCookie = function (c_name, value, expiredays) {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + expiredays);
	document.cookie = c_name + "=" + escape(value) + ((expiredays === null) ? "" : ";expires=" + exdate.toUTCString());
} */

// Return cookie value
/* getCookie = function (c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end === -1) c_end = document.cookie.length;
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return "";
}; */

// Check existance of cookie
/* checkCookie = function (c_name) {
	value = getCookie(c_name);
	if (value != null && value != "") {
		return true;
	} else {
		return false;
	}
}; */

translate = function (from, to, text, place) {
	// Send text for tranlation
	$.post(ca.BASE_URL + '/translate/index', {
		'from': from,
		'to': to,
		'text': text
	}, function (data) {
		var translate = '';
		$.each(data.data[0], function (key, value) {
			translate += value[0];
		});

		// Place translated text
		$(place).val(translate);
	}, 'json');
};