/* LTrim(string) : Returns a copy of a string without leading spaces. */
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

/* RTrim(string) : Returns a copy of a string without trailing spaces. */
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

/* Trim(string) : Returns a copy of a string without leading or trailing spaces */
trim = function (str) {
	return rtrim(ltrim(str));
};