

	var jwType = typeof this.subPaths;
	if (jwType == "string" || (jwType == "object" && this.subPaths.constructor != Array))
		this.subPaths = [ this.subPaths ];
}; // End of Spry.Data.XMLDataSet() constructor.

Spry.Data.XMLDataSet.prototype = new Spry.Data.HTTPSourceDataSet();
Spry.Data.XMLDataSet.prototype.constructor = Spry.Data.XMLDataSet;


Spry.Data.XMLDataSet.prototype.getDataRefStrings = function()
{
	var strArr = [];
	if (this.url) strArr.push(this.url);
	if (this.xpath) strArr.push(this.xpath);
	if (this.requestInfo && this.requestInfo.postData) strArr.push(this.requestInfo.postData);
	return strArr;
};

Spry.Data.XMLDataSet.prototype.getDocument = function() { return this.doc; };
Spry.Data.XMLDataSet.prototype.getXPath = function() { return this.xpath; };
Spry.Data.XMLDataSet.prototype.setXPath = function(path)
{
	if (this.xpath != path)
	{
		this.xpath = path;
		if (this.dataWasLoaded && this.doc)
		{
			this.notifyObservers("onPreLoad");
			this.setDataFromDoc(this.doc);
		}
	}
};

Spry.Data.XMLDataSet.nodeContainsElementNode = function(node)
{
	if (node)
	{
		node = node.firstChild;

		while (node)
		{
			if (node.nodeType == 1 /* Node.ELEMENT_NODE */)
				return true;

			node = node.nextSibling;
		}
	}
	return false;
};

Spry.Data.XMLDataSet.getNodeText = function(node, encodeText, encodeCData)
{
	var txt = "";

	if (!node)
		return;

	try
	{
		var child = node.firstChild;

		while (child)
		{
			try
			{
				if (child.nodeType == 3 /* TEXT_NODE */)
					txt += encodeText ? Spry.Utils.encodeEntities(child.data) : child.data;
				else if (child.nodeType == 4 /* CDATA_SECTION_NODE */)
					txt += encodeCData ? Spry.Utils.encodeEntities(child.data) : child.data;
			} catch (e) { Spry.Debug.reportError("Spry.Data.XMLDataSet.getNodeText() exception caught: " + e + "\n"); }

			child = child.nextSibling;
		}
	}
	catch (e) { Spry.Debug.reportError("Spry.Data.XMLDataSet.getNodeText() exception caught: " + e + "\n"); }

	return txt;
};

Spry.Data.XMLDataSet.createObjectForNode = function(node, encodeText, encodeCData)
{
	if (!node)
		return null;

	var obj = new Object();
	var i = 0;
	var attr = null;

	try
	{
		for (i = 0; i < node.attributes.length; i++)
		{
			attr = node.attributes[i];
			if (attr && attr.nodeType == 2 /* Node.ATTRIBUTE_NODE */)
				obj["@" + attr.name] = attr.value;
		}
	}
	catch (e)
	{
		Spry.Debug.reportError("Spry.Data.XMLDataSet.createObjectForNode() caught exception while accessing attributes: " + e + "\n");
	}

	var child = node.firstChild;

	if (child && !child.nextSibling && child.nodeType != 1 /* Node.ELEMENT_NODE */)
	{
		// We have a single child and it's not an element. It must
		// be the text value for this node. Add it to the record set and
		// give it the column the same name as the node.

		obj[node.nodeName] = Spry.Data.XMLDataSet.getNodeText(node, encodeText, encodeCData);
	}

	while (child)
	{
		// Add the text value for each child element. Note that
		// We skip elements that have element children (sub-elements)
		// because we don't handle multi-level data sets right now.

		if (child.nodeType == 1 /* Node.ELEMENT_NODE */)
		{
			if (!Spry.Data.XMLDataSet.nodeContainsElementNode(child))
			{
				obj[child.nodeName] = Spry.Data.XMLDataSet.getNodeText(child, encodeText, encodeCData);

				// Now add properties for any attributes on the child. The property
				// name will be of the form "<child.nodeName>/@<attr.name>".
				try
				{
					var namePrefix = child.nodeName + "/@";

					for (i = 0; i < child.attributes.length; i++)
					{
						attr = child.attributes[i];
						if (attr && attr.nodeType == 2 /* Node.ATTRIBUTE_NODE */)
							obj[namePrefix + attr.name] = attr.value;
					}
				}
				catch (e)
				{
					Spry.Debug.reportError("Spry.Data.XMLDataSet.createObjectForNode() caught exception while accessing attributes: " + e + "\n");
				}
			}
		}

		child = child.nextSibling;
	}

	return obj;
};

Spry.Data.XMLDataSet.getRecordSetFromXMLDoc = function(xmlDoc, path, suppressColumns, entityEncodeStrings)
{
	if (!xmlDoc || !path)
		return null;

	var recordSet = new Object();
	recordSet.xmlDoc = xmlDoc;
	recordSet.xmlPath = path;
	recordSet.dataHash = new Object;
	recordSet.data = new Array;
	recordSet.getData = function() { return this.data; };

	// Use the XPath library to find the nodes that will
	// make up our data set. The result should be an array
	// of subtrees that we need to flatten.

	var ctx = new ExprContext(xmlDoc);
	var pathExpr = xpathParse(path);
	var e = pathExpr.evaluate(ctx);

	// XXX: Note that we should check the result type of the evaluation
	// just in case it's a boolean, string, or number value instead of
	// a node set.

	var nodeArray = e.nodeSetValue();

	var isDOMNodeArray = true;

	if (nodeArray && nodeArray.length > 0)
		isDOMNodeArray = nodeArray[0].nodeType != 2 /* Node.ATTRIBUTE_NODE */;

	var nextID = 0;

	var encodeText = true;
	var encodeCData = false;
	if (typeof entityEncodeStrings == "boolean")
		encodeText = encodeCData = entityEncodeStrings;

	// We now have the set of nodes that make up our data set
	// so process each one.

	for (var i = 0; i < nodeArray.length; i++)
	{
		var rowObj = null;

		if (suppressColumns)
			rowObj = new Object;
		else
		{
			if (isDOMNodeArray)
				rowObj = Spry.Data.XMLDataSet.createObjectForNode(nodeArray[i], encodeText, encodeCData);
			else // Must be a Node.ATTRIBUTE_NODE array.
			{
				rowObj = new Object;
				rowObj["@" + nodeArray[i].name] = nodeArray[i].value;
			}
		}

		if (rowObj)
		{
			// We want to make sure that every row has a unique ID and since we
			// we don't know which column, if any, in this recordSet is a unique
			// identifier, we generate a unique ID ourselves and store it under
			// the ds_RowID column in the row object.

			rowObj['ds_RowID'] = nextID++;
			rowObj['ds_XMLNode'] = nodeArray[i];
			recordSet.dataHash[rowObj['ds_RowID']] = rowObj;
			recordSet.data.push(rowObj);
		}
	}

	return recordSet;
};

Spry.Data.XMLDataSet.PathNode = function(path)
{
	this.path = path;
	this.subPaths = [];
	this.xpath = "";
};

Spry.Data.XMLDataSet.PathNode.prototype.addSubPath = function(path)
{
	var node = this.findSubPath(path);
	if (!node)
	{
		node = new Spry.Data.XMLDataSet.PathNode(path);
		this.subPaths.push(node);
	}
	return node;
};

Spry.Data.XMLDataSet.PathNode.prototype.findSubPath = function(path)
{
	var numSubPaths = this.subPaths.length;
	for (var i = 0; i < numSubPaths; i++)
	{
		var subPath = this.subPaths[i];
		if (path == subPath.path)
			return subPath;
	}
	return null;
};

Spry.Data.XMLDataSet.PathNode.prototype.consolidate = function()
{
	// This method recursively runs through the path tree and
	// tries to flatten any nodes that have no XPath and one child.
	// The flattening involves merging the parent's path component
	// with its child path component.

	var numSubPaths = this.subPaths.length;
	if (!this.xpath && numSubPaths == 1)
	{
		// Consolidate!
		var subPath = this.subPaths[0];
		this.path += ((subPath[0] != "/") ? "/" : "") + subPath.path;
		this.xpath = subPath.xpath;
		this.subPaths = subPath.subPaths;
		this.consolidate();
		return;
	}

	for (var i = 0; i < numSubPaths; i++)
		this.subPaths[i].consolidate();
};

/* This method is commented out so that it gets stripped when the file
   is minimized. Please do not remove this from the full version of the
   file! It is needed for debugging.

Spry.Data.XMLDataSet.PathNode.prototype.dump = function(indentStr)
{
	var didPre = false;
	var result = "";
	if (!indentStr)
	{
		indentStr = "";
		didPre = true;
		result = "<pre>";
	}
	result += indentStr + "<strong>" + this.path + "</strong>" + (this.xpath ? " <em>-- xpath(" + Spry.Utils.encodeEntities(this.xpath) + ")</em>" : "") + "\n";
	var numSubPaths = this.subPaths.length;
	indentStr += "    ";
	for (var i = 0; i < numSubPaths; i++)
		result += this.subPaths[i].dump(indentStr);
	if (didPre)
		result += "</pre>";
	return result;
};
*/

Spry.Data.XMLDataSet.prototype.convertXPathsToPathTree = function(xpathArray)
{
	var xpaLen = xpathArray.length;
	var root = new Spry.Data.XMLDataSet.PathNode("");

	for (var i = 0; i < xpaLen; i++)
	{
		// Convert any "//" in the XPath to our placeholder value.
		// We need to do that so they don't get removed when we split the
		// path into components.

		var xpath = xpathArray[i];
		var cleanXPath = xpath.replace(/\/\//g, "/__SPRYDS__");
		cleanXPath = cleanXPath.replace(/^\//, ""); // Strip any leading slash.
		var pathItems = cleanXPath.split(/\//);
		var pathItemsLen = pathItems.length;

		// Now add each path component to our tree.

		var node = root;
		for (var j = 0; j < pathItemsLen; j++)
		{
			// If this path component has a placeholder in it, convert it
			// back to a double slash.

			var path = pathItems[j].replace(/__SPRYDS__/, "//");
			node = node.addSubPath(path);
		}

		// Now add the full xpath to the node that represents the
		// last path component in our path.

		node.xpath = xpath;
	}

	// Now that we have a tree of nodes. Tell the root to consolidate
	// itself so we get a tree that is as flat as possible. This reduces
	// the number of XPaths we will have to flatten.

	root.consolidate();
	return root;
};

Spry.Data.XMLDataSet.prototype.flattenSubPaths = function(rs, subPaths)
{
	if (!rs || !subPaths)
		return;

	var numSubPaths = subPaths.length;
	if (numSubPaths < 1)
		return;

	var data = rs.data;
	var dataHash = {};

	// Convert all of the templated subPaths to XPaths with real values.
	// We also need a "cleaned" version of the XPath which contains no
	// expressions in it, so that we can pre-pend it to the column names
	// of any nested data we find.

	var xpathArray = [];
	var cleanedXPathArray = [];

	for (var i = 0; i < numSubPaths; i++)
	{
		// The elements of the subPaths array can be XPath strings,
		// or objects that describe a path with nested sub-paths below
		// it, so make sure we properly extract out the XPath to use.

		var subPath = subPaths[i];
		if (typeof subPath == "object")
			subPath = subPath.path;
		if (!subPath)
			subPath = "";

		// Convert any data references in the XPath to real values!

		xpathArray[i] = Spry.Data.Region.processDataRefString(null, subPath, this.dataSetsForDataRefStrings);

		// Create a clean version of the XPath by stripping out any
		// expressions it may contain.

		cleanedXPathArray[i] = xpathArray[i].replace(/\[.*\]/g, "");
	}

	// For each row of the base record set passed in, generate a flattened
	// recordset from each subPath, and then join the results with the base
	// row. The row from the base data set will be duplicated to match the
	// number of rows matched by the subPath. The results are then merged.

	var row;
	var numRows = data.length;
	var newData = [];

	// Iterate over each row of the base record set.

	for (var i = 0; i < numRows; i++)
	{
		row = data[i];
		var newRows = [ row ];

		// Iterate over every subPath passed into this function.

		for (var j = 0; j < numSubPaths; j++)
		{
			// Search for all nodes that match the given XPath underneath
			// the XML node for the base row and flatten the data into
			// a tabular recordset structure.

			var newRS = Spry.Data.XMLDataSet.getRecordSetFromXMLDoc(row.ds_XMLNode, xpathArray[j], (subPaths[j].xpath ? false : true), this.entityEncodeStrings);

			// If this subPath has additional subPaths beneath it,
			// flatten and join them with the recordset we just created.

			if (newRS && newRS.data && newRS.data.length)
			{
				if (typeof subPaths[j] == "object" && subPaths[j].subPaths)
				{
					// The subPaths property can be either an XPath string,
					// an Object describing a subPath and paths beneath it,
					// or an Array of XPath strings or objects. We need to
					// normalize these variations into an array to simplify
					// our processing.

					var sp = subPaths[j].subPaths;
					spType = typeof sp;
					if (spType == "string")
						sp = [ sp ];
					else if (spType == "object" && spType.constructor == Object)
						sp = [ sp ];

					// Now that we have a normalized array of sub paths, flatten
					// them and join them to the recordSet we just calculated.

					this.flattenSubPaths(newRS, sp);
				}

				var newRSData = newRS.data;
				var numRSRows = newRSData.length;

				var cleanedXPath = cleanedXPathArray[j] + "/";

				var numNewRows = newRows.length;
				var joinedRows = [];

				// Iterate over all rows in our newRows array. Note that the
				// contents of newRows changes after the execution of this
				// loop, allowing us to perform more joins when more than
				// one subPath is specified.

				for (var k = 0; k < numNewRows; k++)
				{
					var newRow = newRows[k];

					// Iterate over all rows in the record set generated
					// from the current subPath. We are going to create
					// m*n rows for the joined table, where m is the number
					// of rows in the newRows array, and n is the number of
					// rows in the current subPath recordset.

					for (var l = 0; l < numRSRows; l++)
					{
						// Create a new row that will house the join result.

						var newRowObj = new Object;
						var newRSRow = newRSData[l];

						// Copy the columns from the newRow into our row
						// object.

						for (prop in newRow)
							newRowObj[prop] = newRow[prop];

						// Copy the data from the current row of the record set
						// into our new row object, but make sure to store the
						// data in columns that have the subPath prepended to
						// it so that it doesn't collide with any columns from
						// the newRows row data.

						for (var prop in newRSRow)
						{
							// The new propery name will have the subPath used prepended to it.
							var newPropName = cleanedXPath + prop;

							// We need to handle the case where the tag name of the node matched
							// by the XPath has a value. In that specific case, the name of the
							// property should be the cleanedXPath itself. For example:
							//
							//	<employees>
							//		<employee>Bob</employee>
							//		<employee>Joe</employee>
							//	</employees>
							//
							// XPath: /employees/employee
							//
							// The property name that contains "Bob" and "Joe" will be "employee".
							// So in our new row, we need to call this column "/employees/employee"
							// instead of "/employees/employee/employee" which would be incorrect.

							if (cleanedXPath == (prop + "/") || cleanedXPath.search(new RegExp("\\/" + prop + "\\/$")) != -1)
								newPropName = cleanedXPathArray[j];

							// Copy the props to the new object using the new property name.

							newRowObj[newPropName] = newRSRow[prop];
						}

						// Now add this row to the array that tracks all of the new
						// rows we've just created.

						joinedRows.push(newRowObj);
					}
				}

				// Set the newRows array equal to our joinedRows we just created,
				// so that when we flatten the data for the next subPath, it gets
				// joined with our new set of rows.

				newRows = joinedRows;
			}
		}

		newData = newData.concat(newRows);
	}

	// Now that we have a new set of joined rows, we need to run through
	// all of the rows and make sure they all have a unique row ID and
	// rebuild our dataHash.

	data = newData;
	numRows = data.length;

	for (i = 0; i < numRows; i++)
	{
		row = data[i];
		row.ds_RowID = i;
		dataHash[row.ds_RowID] = row;
	}

	// We're all done, so stuff the new data and dataHash
	// back into the base recordSet.

	rs.data = data;
	rs.dataHash = dataHash;
};

Spry.Data.XMLDataSet.prototype.loadDataIntoDataSet = function(rawDataDoc)
{
	var rs = null;
	var mainXPath = Spry.Data.Region.processDataRefString(null, this.xpath, this.dataSetsForDataRefStrings);
	var subPaths = this.subPaths;
	var suppressColumns = false;

	if (this.subPaths && this.subPaths.length > 0)
	{
		// Some subPaths were specified. Convert any data references in each subPath
		// to real data. While we're at it, convert any subPaths that are relative
		// to our main XPath to absolute paths.

		var processedSubPaths = [];
		var numSubPaths = subPaths.length;
		for (var i = 0; i < numSubPaths; i++)
		{
			var subPathStr = Spry.Data.Region.processDataRefString(null, subPaths[i], this.dataSetsForDataRefStrings);
			if (subPathStr.charAt(0) != '/')
				subPathStr = mainXPath + "/" + subPathStr;
			processedSubPaths.push(subPathStr);
		}

		// We need to add our main XPath to the set of subPaths and generate a path
		// tree so we can find the XPath to the common parent of all the paths, just
		// in case the user specified a path that was outside of our main XPath.

		processedSubPaths.unshift(mainXPath);
		var commonParent = this.convertXPathsToPathTree(processedSubPaths);

		// The root node of the resulting path tree should contain the XPath
		// to the common parent. Make this the XPath we generate our initial
		// set of rows from so we can group the results of flattening the other
		// subPaths in predictable/expected manner.

		mainXPath = commonParent.path;
		subPaths = commonParent.subPaths;

		// If the XPath to the common parent we calculated isn't our main XPath
		// or any of the subPaths specified by the user, it is used purely for
		// grouping and joining the data we will flatten. We don't want to include
		// any of the columns for the rows created for the common parent XPath since
		// the user did not ask for it.

		suppressColumns = commonParent.xpath ? false : true;
	}

	rs = Spry.Data.XMLDataSet.getRecordSetFromXMLDoc(rawDataDoc, mainXPath, suppressColumns, this.entityEncodeStrings);

	if (!rs)
	{
		Spry.Debug.reportError("Spry.Data.XMLDataSet.loadDataIntoDataSet() failed to create dataSet '" + this.name + "'for '" + this.xpath + "' - " + this.url + "\n");
		return;
	}

	// Now that we have our base set of rows, flatten any additional subPaths
	// specified by the user.

	this.flattenSubPaths(rs, subPaths);

	this.doc = rs.xmlDoc;
	this.data = rs.data;
	this.dataHash = rs.dataHash;
	this.dataWasLoaded = (this.doc != null);
};

Spry.Data.XMLDataSet.prototype.xhRequestProcessor = function(xhRequest)
{
	// XMLDataSet uses the responseXML from the xhRequest

	var resp = xhRequest.responseXML;
	var manualParseRequired = false;

	if (xhRequest.status != 200)
	{
		if (xhRequest.status == 0)
		{
			// The page that is attempting to load data was probably loaded with
			// a file:// url. Mozilla based browsers will actually provide the complete DOM
			// tree for the data, but IE provides an empty document node so try to parse
			// the xml text manually to create a dom tree we can use.

			if (xhRequest.responseText && (!resp || !resp.firstChild))
				manualParseRequired = true;
		}
	}
	else if (!resp)
	{
		// The server said it sent us data, but for some reason we don't have
		// an XML DOM document. Some browsers won't auto-create an XML DOM
		// unless the server used a content-type of "text/xml" or "application/xml".
		// Try to manually parse the XML string, just in case the server
		// gave us an unexpected Content-Type.

		manualParseRequired = true;
	}

	if (manualParseRequired)
		resp = Spry.Utils.stringToXMLDoc(xhRequest.responseText);

	if (!resp  || !resp.firstChild || resp.firstChild.nodeName == "parsererror")
		return null;

	return resp;
};

Spry.Data.XMLDataSet.prototype.sessionExpiredChecker = function(req)
{
	if (req.xhRequest.responseText == 'session expired')
		return true;
	else
	{
		if (req.rawData)
		{
			var firstChild = req.rawData.documentElement.firstChild;
			if (firstChild && firstChild.nodeValue == "session expired")
				return true;
		}
	}
	return false;
};

//////////////////////////////////////////////////////////////////////
//
// Spry.Data.Region
//
//////////////////////////////////////////////////////////////////////

Spry.Data.Region = function(regionNode, name, isDetailRegion, data, dataSets, regionStates, regionStateMap, hasBehaviorAttributes)
{
	this.regionNode = regionNode;
	this.name = name;
	this.isDetailRegion = isDetailRegion;
	this.data = data;
	this.dataSets = dataSets;
	this.hasBehaviorAttributes = hasBehaviorAttributes;
	this.tokens = null;
	this.currentState = null;
	this.states = { ready: true };
	this.stateMap = {};

	Spry.Utils.setOptions(this.states, regionStates);
	Spry.Utils.setOptions(this.stateMap, regionStateMap);

	// Add the region as an observer to the dataSet!
	for (var i = 0; i < this.dataSets.length; i++)
	{
		var ds = this.dataSets[i];

		try
		{
			if (ds)
				ds.addObserver(this);
		}
		catch(e) { Spry.Debug.reportError("Failed to add '" + this.name + "' as a dataSet observer!\n"); }
	}
}; // End of Spry.Data.Region() constructor.

Spry.Data.Region.hiddenRegionClassName = "SpryHiddenRegion";
Spry.Data.Region.evenRowClassName = "even";
Spry.Data.Region.oddRowClassName = "odd";
Spry.Data.Region.notifiers = {};
Spry.Data.Region.evalScripts = true;

Spry.Data.Region.addObserver = function(regionID, observer)
{
	var n = Spry.Data.Region.notifiers[regionID];
	if (!n)
	{
		n = new Spry.Utils.Notifier();
		Spry.Data.Region.notifiers[regionID] = n;
	}
	n.addObserver(observer);
};

Spry.Data.Region.removeObserver = function(regionID, observer)
{
	var n = Spry.Data.Region.notifiers[regionID];
	if (n)
		n.removeObserver(observer);
};

Spry.Data.Region.notifyObservers = function(methodName, region, data)
{
	var n = Spry.Data.Region.notifiers[region.name];
	if (n)
	{
		var dataObj = {};
		if (data && typeof data == "object")
			dataObj = data;
		else
			dataObj.data = data;

		dataObj.region = region;
		dataObj.regionID = region.name;
		dataObj.regionNode = region.regionNode;

		n.notifyObservers(methodName, dataObj);
	}
};

Spry.Data.Region.RS_Error = 0x01;
Spry.Data.Region.RS_LoadingData = 0x02;
Spry.Data.Region.RS_PreUpdate = 0x04;
Spry.Data.Region.RS_PostUpdate = 0x08;

Spry.Data.Region.prototype.getState = function()
{
	return this.currentState;
};

Spry.Data.Region.prototype.mapState = function(stateName, newStateName)
{
	this.stateMap[stateName] = newStateName;
};

Spry.Data.Region.prototype.getMappedState = function(stateName)
{
	var mappedState = this.stateMap[stateName];
	return mappedState ? mappedState : stateName;
};

Spry.Data.Region.prototype.setState = function(stateName, suppressNotfications)
{
	var stateObj = { state: stateName, mappedState: this.getMappedState(stateName) };
	if (!suppressNotfications)
		Spry.Data.Region.notifyObservers("onPreStateChange", this, stateObj);

	this.currentState = stateObj.mappedState ? stateObj.mappedState : stateName;

	// If the region has content that is specific to this
	// state, regenerate the region so that its markup is updated.

	if (this.states[stateName])
	{
		var notificationData = { state: this.currentState };
		if (!suppressNotfications)
			Spry.Data.Region.notifyObservers("onPreUpdate", this, notificationData);

		// Make the region transform the xml data. The result is
		// a string that we need to parse and insert into the document.

		var str = this.transform();

		// Clear out any previous transformed content.
		// this.clearContent();

		if (Spry.Data.Region.debug)
			Spry.Debug.trace("<hr />Generated region markup for '" + this.name + "':<br /><br />" + Spry.Utils.encodeEntities(str));

		// Now insert the new transformed content into the document.
		Spry.Utils.setInnerHTML(this.regionNode, str, !Spry.Data.Region.evalScripts);

		// Now run through the content looking for attributes
		// that tell us what behaviors to attach to each element.
		if (this.hasBehaviorAttributes)
			this.attachBehaviors();

		if (!suppressNotfications)
			Spry.Data.Region.notifyObservers("onPostUpdate", this, notificationData);
	}

	if (!suppressNotfications)
		Spry.Data.Region.notifyObservers("onPostStateChange", this, stateObj);
};

Spry.Data.Region.prototype.getDataSets = function()
{
	return this.dataSets;
};

Spry.Data.Region.prototype.addDataSet = function(aDataSet)
{
	if (!aDataSet)
		return;

	if (!this.dataSets)
		this.dataSets = new Array;

	// Check to see if the data set is already in our list.

	for (var i = 0; i < this.dataSets.length; i++)
	{
		if (this.dataSets[i] == aDataSet)
			return; // It's already in our list!
	}

	this.dataSets.push(aDataSet);
	aDataSet.addObserver(this);
};

Spry.Data.Region.prototype.removeDataSet = function(aDataSet)
{
	if (!aDataSet || this.dataSets)
		return;

	for (var i = 0; i < this.dataSets.length; i++)
	{
		if (this.dataSets[i] == aDataSet)
		{
			this.dataSets.splice(i, 1);
			aDataSet.removeObserver(this);
			return;
		}
	}
};

Spry.Data.Region.prototype.onPreLoad = function(dataSet)
{
	if (this.currentState != "loading")
		this.setState("loading");
};

Spry.Data.Region.prototype.onLoadError = function(dataSet)
{
	if (this.currentState != "error")
		this.setState("error");
	Spry.Data.Region.notifyObservers("onError", this);
};

Spry.Data.Region.prototype.onSessionExpired = function(dataSet)
{
	if (this.currentState != "expired")
		this.setState("expired");
	Spry.Data.Region.notifyObservers("onExpired", this);
};

Spry.Data.Region.prototype.onCurrentRowChanged = function(dataSet, data)
{
	if (this.isDetailRegion)
		this.updateContent();
};

Spry.Data.Region.prototype.onPostSort = function(dataSet, data)
{
	this.updateContent();
};

Spry.Data.Region.prototype.onDataChanged = function(dataSet, data)
{
	this.updateContent();
};

Spry.Data.Region.enableBehaviorAttributes = true;
Spry.Data.Region.behaviorAttrs = {};

Spry.Data.Region.behaviorAttrs["spry:select"] =
{
	attach: function(rgn, node, value)
	{
		var selectGroupName = null;
		try { selectGroupName = node.attributes.getNamedItem("spry:selectgroup").value; } catch (e) {}
		if (!selectGroupName)
			selectGroupName = "default";

		Spry.Utils.addEventListener(node, "click", function(event) { Spry.Utils.SelectionManager.select(selectGroupName, node, value); }, false);

		if (node.attributes.getNamedItem("spry:selected"))
			Spry.Utils.SelectionManager.select(selectGroupName, node, value);
	}
};

Spry.Data.Region.behaviorAttrs["spry:hover"] =
{
	attach: function(rgn, node, value)
	{
		Spry.Utils.addEventListener(node, "mouseover", function(event){ Spry.Utils.addClassName(node, value); }, false);
		Spry.Utils.addEventListener(node, "mouseout", function(event){ Spry.Utils.removeClassName(node, value); }, false);
	}
};

Spry.Data.Region.setUpRowNumberForEvenOddAttr = function(node, attr, value, rowNumAttrName)
{
	// The format for the spry:even and spry:odd attributes are as follows:
	//
	// <div spry:even="dataSetName cssEvenClassName" spry:odd="dataSetName cssOddClassName">
	//
	// The dataSetName is optional, and if not specified, the first data set
	// listed for the region is used.
	//
	// cssEvenClassName and cssOddClassName are required and *must* be specified. They can be
	// any user defined CSS class name.

	if (!value)
	{
		Spry.Debug.showError("The " + attr + " attribute requires a CSS class name as its value!");
		node.attributes.removeNamedItem(attr);
		return;
	}

	var dsName = "";
	var valArr = value.split(/\s/);
	if (valArr.length > 1)
	{
		// Extract out the data set name and reset the attribute so
		// that it only contains the CSS class name to use.

		dsName = valArr[0];
		node.setAttribute(attr, valArr[1]);
	}

	// Tag the node with an attribute that will allow us to fetch the row
	// number used when it is written out during the re-generation process.

	node.setAttribute(rowNumAttrName, "{" + (dsName ? (dsName + "::") : "") + "ds_RowNumber}");
};

Spry.Data.Region.behaviorAttrs["spry:even"] =
{
	setup: function(node, value)
	{
		Spry.Data.Region.setUpRowNumberForEvenOddAttr(node, "spry:even", value, "spryevenrownumber");
	},

	attach: function(rgn, node, value)
	{
		if (value)
		{
			rowNumAttr = node.attributes.getNamedItem("spryevenrownumber");
			if (rowNumAttr && rowNumAttr.value)
			{
				var rowNum = parseInt(rowNumAttr.value);
				if (rowNum % 2)
					Spry.Utils.addClassName(node, value);
			}
		}
		node.removeAttribute("spry:even");
		node.removeAttribute("spryevenrownumber");
	}
};

Spry.Data.Region.behaviorAttrs["spry:odd"] =
{
	setup: function(node, value)
	{
		Spry.Data.Region.setUpRowNumberForEvenOddAttr(node, "spry:odd", value, "spryoddrownumber");
	},

	attach: function(rgn, node, value)
	{
		if (value)
		{
			rowNumAttr = node.attributes.getNamedItem("spryoddrownumber");
			if (rowNumAttr && rowNumAttr.value)
			{
				var rowNum = parseInt(rowNumAttr.value);
				if (rowNum % 2 == 0)
					Spry.Utils.addClassName(node, value);
			}
		}
		node.removeAttribute("spry:odd");
		node.removeAttribute("spryoddrownumber");
	}
};

Spry.Data.Region.setRowAttrClickHandler = function(node, dsName, rowAttr, funcName)
{
		if (dsName)
		{
			var ds = Spry.Data.getDataSetByName(dsName);
			if (ds)
			{
				rowIDAttr = node.attributes.getNamedItem(rowAttr);
				if (rowIDAttr)
				{
					var rowAttrVal = rowIDAttr.value;
					if (rowAttrVal)
						Spry.Utils.addEventListener(node, "click", function(event){ ds[funcName](rowAttrVal); }, false);
				}
			}
		}
};

Spry.Data.Region.behaviorAttrs["spry:setrow"] =
{
	setup: function(node, value)
	{
		if (!value)
		{
			Spry.Debug.reportError("The spry:setrow attribute requires a data set name as its value!");
			node.removeAttribute("spry:setrow");
			return;
		}

		// Tag the node with an attribute that will allow us to fetch the id of the
		// row used when it is written out during the re-generation process.

		node.setAttribute("spryrowid", "{" + value + "::ds_RowID}");
	},

	attach: function(rgn, node, value)
	{
		Spry.Data.Region.setRowAttrClickHandler(node, value, "spryrowid", "setCurrentRow");
		node.removeAttribute("spry:setrow");
		node.removeAttribute("spryrowid");
	}
};

Spry.Data.Region.behaviorAttrs["spry:setrownumber"] =
{
	setup: function(node, value)
	{
		if (!value)
		{
			Spry.Debug.reportError("The spry:setrownumber attribute requires a data set name as its value!");
			node.removeAttribute("spry:setrownumber");
			return;
		}

		// Tag the node with an attribute that will allow us to fetch the row number
		// of the row used when it is written out during the re-generation process.

		node.setAttribute("spryrownumber", "{" + value + "::ds_RowID}");
	},

	attach: function(rgn, node, value)
	{
		Spry.Data.Region.setRowAttrClickHandler(node, value, "spryrownumber", "setCurrentRowNumber");
		node.removeAttribute("spry:setrownumber");
		node.removeAttribute("spryrownumber");
	}
};

Spry.Data.Region.behaviorAttrs["spry:sort"] =
{
	attach: function(rgn, node, value)
	{
		if (!value)
			return;

		// The format of a spry:sort attribute is as follows:
		//
		// <div spry:sort="dataSetName column1Name column2Name ... sortOrderName">
		//
		// The dataSetName and sortOrderName are optional, but when specified, they
		// must appear in the order mentioned above. If the dataSetName is not specified,
		// the first data set listed for the region is used. If the sortOrderName is not
		// specified, the sort defaults to "toggle".
		//
		// The user *must* specify at least one column name.

		var ds = rgn.getDataSets()[0];
		var sortOrder = "toggle";

		var colArray = value.split(/\s/);
		if (colArray.length > 1)
		{
			// Check the first string in the attribute to see if a data set was
			// specified. If so, make sure we use it for the sort.

			var specifiedDS = Spry.Data.getDataSetByName(colArray[0]);
			if (specifiedDS)
			{
				ds = specifiedDS;
				colArray.shift();
			}

			// Check to see if the last string in the attribute is the name of
			// a sort order. If so, use that sort order during the sort.

			if (colArray.length > 1)
			{
				var str = colArray[colArray.length - 1];
				if (str == "ascending" || str == "descending" || str == "toggle")
				{
					sortOrder = str;
					colArray.pop();
				}
			}
		}

		// If we have a data set and some column names, add a non-destructive
		// onclick handler that will perform a toggle sort on the data set.

		if (ds && colArray.length > 0)
			Spry.Utils.addEventListener(node, "click", function(event){ ds.sort(colArray, sortOrder); }, false);

		node.removeAttribute("spry:sort");
	}
};

Spry.Data.Region.prototype.attachBehaviors = function()
{
	var rgn = this;
	Spry.Utils.getNodesByFunc(this.regionNode, function(node)
	{
		if (!node || node.nodeType != 1 /* Node.ELEMENT_NODE */)
			return false;
		try
		{
			var bAttrs = Spry.Data.Region.behaviorAttrs;
			for (var bAttrName in bAttrs)
			{
				var attr = node.attributes.getNamedItem(bAttrName);
				if (attr)
				{
					var behavior = bAttrs[bAttrName];
					if (behavior && behavior.attach)
						behavior.attach(rgn, node, attr.value);
				}
			}
		} catch(e) {}

		return false;
	});
};

Spry.Data.Region.prototype.updateContent = function()
{
	var allDataSetsReady = true;

	var dsArray = this.getDataSets();

	if (!dsArray || dsArray.length < 1)
	{
		Spry.Debug.reportError("updateContent(): Region '" + this.name + "' has no data set!\n");
		return;
	}

	for (var i = 0; i < dsArray.length; i++)
	{
		var ds = dsArray[i];

		if (ds)
		{
			if (ds.getLoadDataRequestIsPending())
				allDataSetsReady = false;
			else if (!ds.getDataWasLoaded())
			{
				// Kick off the loading of the data if it hasn't happened yet.
				ds.loadData();
				allDataSetsReady = false;
			}
		}
	}

	if (!allDataSetsReady)
	{
		Spry.Data.Region.notifyObservers("onLoadingData", this);

		// Just return, this method will get called again automatically
		// as each data set load completes!
		return;
	}

	this.setState("ready");
};

Spry.Data.Region.prototype.clearContent = function()
{
	this.regionNode.innerHTML = "";
};

Spry.Data.Region.processContentPI = function(inStr)
{
	var outStr = "";
	var regexp = /<!--\s*<\/?spry:content\s*[^>]*>\s*-->/mg;
	var searchStartIndex = 0;
	var processingContentTag = 0;

	while (inStr.length)
	{
		var results = regexp.exec(inStr);
		if (!results || !results[0])
		{
			outStr += inStr.substr(searchStartIndex, inStr.length - searchStartIndex);
			break;
		}

		if (!processingContentTag && results.index != searchStartIndex)
		{
			// We found a match but it's not at the start of the inStr.
			// Create a string token for everything that precedes the match.
			outStr += inStr.substr(searchStartIndex, results.index - searchStartIndex);
		}

		if (results[0].search(/<\//) != -1)
		{
			--processingContentTag;
			if (processingContentTag)
				Spry.Debug.reportError("Nested spry:content regions are not allowed!\n");
		}
		else
		{
			++processingContentTag;
			var dataRefStr = results[0].replace(/.*\bdataref="/, "");
			outStr += dataRefStr.replace(/".*$/, "");
		}

		searchStartIndex = regexp.lastIndex;
	}

	return outStr;
};

Spry.Data.Region.prototype.tokenizeData = function(dataStr)
{
	// If there is no data, there's nothing to do.
	if (!dataStr)
		return null;

	var rootToken = new Spry.Data.Region.Token(Spry.Data.Region.Token.LIST_TOKEN, null, null, null);
	var tokenStack = new Array;
	var parseStr = Spry.Data.Region.processContentPI(dataStr);

	tokenStack.push(rootToken);

	// Create a regular expression that will match one of the following:
	//
	//   <spry:repeat select="regionName" test="true">
	//   </spry:repeat>
	//   {valueReference}
	var regexp = /((<!--\s*){0,1}<\/{0,1}spry:[^>]+>(\s*-->){0,1})|((\{|%7[bB])[^\}\s%]+(\}|%7[dD]))/mg;
	var searchStartIndex = 0;

	while(parseStr.length)
	{
		var results = regexp.exec(parseStr);
		var token = null;

		if (!results || !results[0])
		{
			// If we get here, the rest of the parseStr should be
			// just a plain string. Create a token for it and then
			// break out of the list.
			var str = parseStr.substr(searchStartIndex, parseStr.length - searchStartIndex);
			token = new Spry.Data.Region.Token(Spry.Data.Region.Token.STRING_TOKEN, null, str, str);
			tokenStack[tokenStack.length - 1].addChild(token);
			break;
		}

		if (results.index != searchStartIndex)
		{
			// We found a match but it's not at the start of the parseStr.
			// Create a string token for everything that precedes the match.
			var str = parseStr.substr(searchStartIndex, results.index - searchStartIndex);
			token = new Spry.Data.Region.Token(Spry.Data.Region.Token.STRING_TOKEN, null, str, str);
			tokenStack[tokenStack.length - 1].addChild(token);
		}

		// We found a string that needs to be turned into a token. Create a token
		// for it and then update parseStr for the next iteration.
		if (results[0].search(/^({|%7[bB])/) != -1 /* results[0].charAt(0) == '{' */)
		{
			var valueName = results[0];
			var regionStr = results[0];

			// Strip off brace and url encode brace chars inside the valueName.

			valueName = valueName.replace(/^({|%7[bB])/, "");
			valueName = valueName.replace(/(}|%7[dD])$/, "");

			// Check to see if our value begins with the name of a data set.
			// For example: {dataSet:tokenValue}. If it is, we need to save
			// the data set name so we know which data set to use to get the
			// value for the token during the region transform.

			var dataSetName = null;
			var splitArray = valueName.split(/::/);

			if (splitArray.length > 1)
			{
				dataSetName = splitArray[0];
				valueName = splitArray[1];
			}

			// Convert any url encoded braces to regular brace chars.

			regionStr = regionStr.replace(/^%7[bB]/, "{");
			regionStr = regionStr.replace(/%7[dD]$/, "}");

			// Now create a token for the placeholder.

			token = new Spry.Data.Region.Token(Spry.Data.Region.Token.VALUE_TOKEN, dataSetName, valueName, new String(regionStr));
			tokenStack[tokenStack.length - 1].addChild(token);
		}
		else if (results[0].charAt(0) == '<')
		{
			// Extract out the name of the processing instruction.
			var piName = results[0].replace(/^(<!--\s*){0,1}<\/?/, "");
			piName = piName.replace(/>(\s*-->){0,1}|\s.*$/, "");

			if (results[0].search(/<\//) != -1 /* results[0].charAt(1) == '/' */)
			{
				// We found a processing instruction close tag. Pop the top of the
				// token stack!
				//
				// XXX: We need to make sure that the close tag name matches the one
				//      on the top of the token stack!
				if (tokenStack[tokenStack.length - 1].tokenType != Spry.Data.Region.Token.PROCESSING_INSTRUCTION_TOKEN)
				{
					Spry.Debug.reportError("Invalid processing instruction close tag: " + piName + " -- " + results[0] + "\n");
					return null;
				}

				tokenStack.pop();
			}
			else
			{
				// Create the processing instruction token, add it as a child of the token
				// at the top of the token stack, and then push it on the stack so that it
				// becomes the parent of any tokens between it and its close tag.

				var piDesc = Spry.Data.Region.PI.instructions[piName];

				if (piDesc)
				{
					var dataSet = null;

					var selectedDataSetName = "";
					if (results[0].search(/^.*\bselect=\"/) != -1)
					{
						selectedDataSetName = results[0].replace(/^.*\bselect=\"/, "");
						selectedDataSetName = selectedDataSetName.replace(/".*$/, "");

						if (selectedDataSetName)
						{
							dataSet = Spry.Data.getDataSetByName(selectedDataSetName);
							if (!dataSet)
							{
								Spry.Debug.reportError("Failed to retrieve data set (" + selectedDataSetName + ") for " + piName + "\n");
								selectedDataSetName = "";
							}
						}
					}

					// Check if the repeat has a test attribute.
					var jsExpr = null;
					if (results[0].search(/^.*\btest=\"/) != -1)
					{
						jsExpr = results[0].replace(/^.*\btest=\"/, "");
						jsExpr = jsExpr.replace(/".*$/, "");
						jsExpr = Spry.Utils.decodeEntities(jsExpr);
					}

					// Check if the instruction has a state name specified.
					var regionState = null;
					if (results[0].search(/^.*\bname=\"/) != -1)
					{
						regionState = results[0].replace(/^.*\bname=\"/, "");
						regionState = regionState.replace(/".*$/, "");
						regionState = Spry.Utils.decodeEntities(regionState);
					}

					var piData = new Spry.Data.Region.Token.PIData(piName, selectedDataSetName, jsExpr, regionState);

					token = new Spry.Data.Region.Token(Spry.Data.Region.Token.PROCESSING_INSTRUCTION_TOKEN, dataSet, piData, new String(results[0]));

					tokenStack[tokenStack.length - 1].addChild(token);
					tokenStack.push(token);
				}
				else
				{
					Spry.Debug.reportError("Unsupported region processing instruction: " + results[0] + "\n");
					return null;
				}
			}
		}
		else
		{
			Spry.Debug.reportError("Invalid region token: " + results[0] + "\n");
			return null;
		}

		searchStartIndex = regexp.lastIndex;
	}

	return rootToken;
};

Spry.Data.Region.prototype.processTokenChildren = function(outputArr, token, processContext)
{
	var children = token.children;
	var len = children.length;

	for (var i = 0; i < len; i++)
		this.processTokens(outputArr, children[i], processContext);
};

Spry.Data.Region.prototype.processTokens = function(outputArr, token, processContext)
{
	var i = 0;

	switch(token.tokenType)
	{
		case Spry.Data.Region.Token.LIST_TOKEN:
			this.processTokenChildren(outputArr, token, processContext);
			break;
		case Spry.Data.Region.Token.STRING_TOKEN:
			outputArr.push(token.data);
			break;
		case Spry.Data.Region.Token.PROCESSING_INSTRUCTION_TOKEN:
			if (token.data.name == "spry:repeat")
			{
				var dataSet = null;

				if (token.dataSet)
					dataSet = token.dataSet;
				else
					dataSet = this.dataSets[0];

				if (dataSet)
				{
					var dsContext = processContext.getDataSetContext(dataSet);
					if (!dsContext)
					{
						Spry.Debug.reportError("processTokens() failed to get a data set context!\n");
						break;
					}

					dsContext.pushState();

					var dataSetRows = dsContext.getData();
					var numRows = dataSetRows.length;
					for (i = 0; i < numRows; i++)
					{
						dsContext.setRowIndex(i);
						var testVal = true;
						if (token.data.jsExpr)
						{
							var jsExpr = Spry.Data.Region.processDataRefString(processContext, token.data.jsExpr, null, true);
							try { testVal = Spry.Utils.eval(jsExpr); }
							catch(e)
							{
								Spry.Debug.trace("Caught exception in Spry.Data.Region.prototype.processTokens while evaluating: " + jsExpr + "\n    Exception:" + e + "\n");
								testVal = true;
							}
						}

						if (testVal)
							this.processTokenChildren(outputArr, token, processContext);
					}
					dsContext.popState();
				}
			}
			else if (token.data.name == "spry:if")
			{
				var testVal = true;

				if (token.data.jsExpr)
				{
					var jsExpr = Spry.Data.Region.processDataRefString(processContext, token.data.jsExpr, null, true);

					try { testVal = Spry.Utils.eval(jsExpr); }
					catch(e)
					{
						Spry.Debug.trace("Caught exception in Spry.Data.Region.prototype.processTokens while evaluating: " + jsExpr + "\n    Exception:" + e + "\n");
						testVal = true;
					}
				}

				if (testVal)
					this.processTokenChildren(outputArr, token, processContext);
			}
			else if (token.data.name == "spry:choose")
			{
				var defaultChild = null;
				var childToProcess = null;
				var testVal = false;
				var j = 0;

				// All of the children of the spry:choose token should be of the type spry:when or spry:default.
				// Run through all of the spry:when children and see if any of their test expressions return true.
				// If one does, then process its children tokens. If none of the test expressions return true,
				// process the spry:default token's children, if it exists.

				for (j = 0; j < token.children.length; j++)
				{
					var child = token.children[j];
					if (child.tokenType == Spry.Data.Region.Token.PROCESSING_INSTRUCTION_TOKEN)
					{
						if (child.data.name == "spry:when")
						{
							if (child.data.jsExpr)
							{
								var jsExpr = Spry.Data.Region.processDataRefString(processContext, child.data.jsExpr, null, true);
								try { testVal = Spry.Utils.eval(jsExpr); }
								catch(e)
								{
									Spry.Debug.trace("Caught exception in Spry.Data.Region.prototype.processTokens while evaluating: " + jsExpr + "\n    Exception:" + e + "\n");
									testVal = false;
								}

								if (testVal)
								{
									childToProcess = child;
									break;
								}
							}
						}
						else if (child.data.name == "spry:default")
							defaultChild = child;
					}
				}

				// If we didn't find a match, use the token for the default case.

				if (!childToProcess && defaultChild)
					childToProcess = defaultChild;

				if (childToProcess)
					this.processTokenChildren(outputArr, childToProcess, processContext);
			}
			else if (token.data.name == "spry:state")
			{
				var testVal = true;

				if (!token.data.regionState || token.data.regionState == this.currentState)
					this.processTokenChildren(outputArr, token, processContext);
			}
			else
			{
				Spry.Debug.reportError("processTokens(): Unknown processing instruction: " + token.data.name + "\n");
				return "";
			}
			break;
		case Spry.Data.Region.Token.VALUE_TOKEN:

			var dataSet = token.dataSet;
			if (!dataSet && this.dataSets && this.dataSets.length > 0 && this.dataSets[0])
			{
				// No dataSet was specified by the token, so use whatever the first
				// data set specified in the region.

				dataSet = this.dataSets[0];
			}
			if (!dataSet)
			{
				Spry.Debug.reportError("processTokens(): Value reference has no data set specified: " + token.regionStr + "\n");
				return "";
			}

			var dsContext = processContext.getDataSetContext(dataSet);
			if (!dsContext)
			{
				Spry.Debug.reportError("processTokens: Failed to get a data set context!\n");
				return "";
			}

			var ds = dsContext.getDataSet();

			if (token.data == "ds_RowNumber")
				outputArr.push(dsContext.getRowIndex());
			else if (token.data == "ds_RowNumberPlus1")
				outputArr.push(dsContext.getRowIndex() + 1);
			else if (token.data == "ds_RowCount")
				outputArr.push(dsContext.getNumRows());
			else if (token.data == "ds_UnfilteredRowCount")
				outputArr.push(dsContext.getNumRows(true));
			else if (token.data == "ds_CurrentRowNumber")
				outputArr.push(ds.getRowNumber(ds.getCurrentRow()));
			else if (token.data == "ds_CurrentRowID")
				outputArr.push(ds.getCurrentRowID());
			else if (token.data == "ds_EvenOddRow")
				outputArr.push((dsContext.getRowIndex() % 2) ? Spry.Data.Region.evenRowClassName : Spry.Data.Region.oddRowClassName);
			else if (token.data == "ds_SortOrder")
				outputArr.push(ds.getSortOrder());
			else if (token.data == "ds_SortColumn")
				outputArr.push(ds.getSortColumn());
			else
			{
				var curDataSetRow = dsContext.getCurrentRow();
				if (curDataSetRow)
					outputArr.push(curDataSetRow[token.data]);
			}
			break;
		default:
			Spry.Debug.reportError("processTokens(): Invalid token type: " + token.regionStr + "\n");
			break;
	}
};

Spry.Data.Region.prototype.transform = function()
{
	if (this.data && !this.tokens)
		this.tokens = this.tokenizeData(this.data);

	if (!this.tokens)
		return "";

	processContext = new Spry.Data.Region.ProcessingContext(this);
	if (!processContext)
		return "";

	// Now call processTokens to transform our tokens into real data strings.
	// We use an array to gather the strings during processing as a performance
	// enhancement for IE to avoid n-square problems of adding to an existing
	// string. For example:
	//
	//     for (var i = 0; i < token.children.length; i++)
	//       outputStr += this.processTokens(token.children[i], processContext);
	//
	// Using an array with a final join reduced one of our test cases  from over
	// a minute to about 15 seconds.

	var outputArr = [ "" ];
	this.processTokens(outputArr, this.tokens, processContext);
	return outputArr.join("");
};

Spry.Data.Region.PI = {};
Spry.Data.Region.PI.instructions = {};

Spry.Data.Region.PI.buildOpenTagForValueAttr = function(ele, piName, attrName)
{
	if (!ele || !piName)
		return "";

	var jsExpr = "";

	try
	{
		var testAttr = ele.attributes.getNamedItem(piName);
		if (testAttr && testAttr.value)
			jsExpr = Spry.Utils.encodeEntities(testAttr.value);
	}
	catch (e) { jsExpr = ""; }

	if (!jsExpr)
	{
		Spry.Debug.reportError(piName + " attribute requires a JavaScript expression that returns true or false!\n");
		return "";
	}

	return "<" + Spry.Data.Region.PI.instructions[piName].tagName + " " + attrName +"=\"" + jsExpr + "\">";
};

Spry.Data.Region.PI.buildOpenTagForTest = function(ele, piName)
{
	return Spry.Data.Region.PI.buildOpenTagForValueAttr(ele, piName, "test");
};

Spry.Data.Region.PI.buildOpenTagForState = function(ele, piName)
{
	return Spry.Data.Region.PI.buildOpenTagForValueAttr(ele, piName, "name");
};

Spry.Data.Region.PI.buildOpenTagForRepeat = function(ele, piName)
{
	if (!ele || !piName)
		return "";

	var selectAttrStr = "";

	try
	{
		var selectAttr = ele.attributes.getNamedItem(piName);
		if (selectAttr && selectAttr.value)
		{
			selectAttrStr = selectAttr.value;
			selectAttrStr = selectAttrStr.replace(/\s/g, "");
		}
	}
	catch (e) { selectAttrStr = ""; }

	if (!selectAttrStr)
	{
		Spry.Debug.reportError(piName + " attribute requires a data set name!\n");
		return "";
	}

	var testAttrStr = "";

	try
	{
		var testAttr = ele.attributes.getNamedItem("spry:test");
		if (testAttr)
		{
			if (testAttr.value)
				testAttrStr = " test=\"" + Spry.Utils.encodeEntities(testAttr.value) + "\"";
			ele.attributes.removeNamedItem(testAttr.nodeName);
		}
	}
	catch (e) { testAttrStr = ""; }

	return "<" + Spry.Data.Region.PI.instructions[piName].tagName + " select=\"" + selectAttrStr + "\"" + testAttrStr + ">";
};

Spry.Data.Region.PI.buildOpenTagForContent = function(ele, piName)
{
	if (!ele || !piName)
		return "";

	var dataRefStr = "";

	try
	{
		var contentAttr = ele.attributes.getNamedItem(piName);
		if (contentAttr && contentAttr.value)
			dataRefStr = Spry.Utils.encodeEntities(contentAttr.value);
	}
	catch (e) { dataRefStr = ""; }

	if (!dataRefStr)
	{
		Spry.Debug.reportError(piName + " attribute requires a data reference!\n");
		return "";
	}

	return "<" + Spry.Data.Region.PI.instructions[piName].tagName + " dataref=\"" + dataRefStr + "\">";
};

Spry.Data.Region.PI.buildOpenTag = function(ele, piName)
{
	return "<" + Spry.Data.Region.PI.instructions[piName].tagName + ">";
};

Spry.Data.Region.PI.buildCloseTag = function(ele, piName)
{
	return "</" + Spry.Data.Region.PI.instructions[piName].tagName + ">";
};

Spry.Data.Region.PI.instructions["spry:state"] = { tagName: "spry:state", childrenOnly: false, getOpenTag: Spry.Data.Region.PI.buildOpenTagForState, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:if"] = { tagName: "spry:if", childrenOnly: false, getOpenTag: Spry.Data.Region.PI.buildOpenTagForTest, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:repeat"] = { tagName: "spry:repeat", childrenOnly: false, getOpenTag: Spry.Data.Region.PI.buildOpenTagForRepeat, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:repeatchildren"] = { tagName: "spry:repeat", childrenOnly: true, getOpenTag: Spry.Data.Region.PI.buildOpenTagForRepeat, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:choose"] = { tagName: "spry:choose", childrenOnly: true, getOpenTag: Spry.Data.Region.PI.buildOpenTag, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:when"] = { tagName: "spry:when", childrenOnly: false, getOpenTag: Spry.Data.Region.PI.buildOpenTagForTest, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:default"] = { tagName: "spry:default", childrenOnly: false, getOpenTag: Spry.Data.Region.PI.buildOpenTag, getCloseTag: Spry.Data.Region.PI.buildCloseTag };
Spry.Data.Region.PI.instructions["spry:content"] = { tagName: "spry:content", childrenOnly: true, getOpenTag: Spry.Data.Region.PI.buildOpenTagForContent, getCloseTag: Spry.Data.Region.PI.buildCloseTag };

Spry.Data.Region.PI.orderedInstructions = [ "spry:state", "spry:if", "spry:repeat", "spry:repeatchildren", "spry:choose", "spry:when", "spry:default", "spry:content" ];

Spry.Data.Region.getTokensFromStr = function(str)
{
	// XXX: This will need to be modified if we support
	// tokens that use javascript between the braces!
	if (!str)
		return null;
	return str.match(/{[^}]+}/g);
};

Spry.Data.Region.processDataRefString = function(processingContext, regionStr, dataSetsToUse, isJSExpr)
{
	if (!regionStr)
		return "";

	if (!processingContext && !dataSetsToUse)
		return regionStr;

	var resultStr = "";
	var re = new RegExp("\\{([^\\}:]+::)?[^\\}]+\\}", "g");
	var startSearchIndex = 0;

	while (startSearchIndex < regionStr.length)
	{
		var reArray = re.exec(regionStr);
		if (!reArray || !reArray[0])
		{
			resultStr += regionStr.substr(startSearchIndex, regionStr.length - startSearchIndex);
			return resultStr;
		}

		if (reArray.index != startSearchIndex)
			resultStr += regionStr.substr(startSearchIndex, reArray.index - startSearchIndex);

		var dsName = "";
		if (reArray[0].search(/^\{[^}:]+::/) != -1)
			dsName = reArray[0].replace(/^\{|::.*/g, "");

		var fieldName = reArray[0].replace(/^\{|.*::|\}/g, "");
		var row = null;

		if (processingContext)
		{
			var dsContext = processingContext.getDataSetContext(dsName);

			if (fieldName == "ds_RowNumber")
			{
				resultStr += dsContext.getRowIndex();
				row = null;
			}
			else if (fieldName == "ds_RowNumberPlus1")
			{
				resultStr += (dsContext.getRowIndex() + 1);
				row = null;
			}
			else if (fieldName == "ds_RowCount")
			{
				resultStr += dsContext.getNumRows();
				row = null;
			}
			else if (fieldName == "ds_UnfilteredRowCount")
			{
				resultStr += dsContext.getNumRows(true);
				row = null;
			}
			else if (fieldName == "ds_CurrentRowNumber")
			{
				var ds = dsContext.getDataSet();
				resultStr += ds.getRowNumber(ds.getCurrentRow());
				row = null;
			}
			else if (fieldName == "ds_CurrentRowID")
			{
				var ds = dsContext.getDataSet();
				resultStr += "" + ds.getCurrentRowID();
				row = null;
			}
			else if (fieldName == "ds_EvenOddRow")
			{
				resultStr += (dsContext.getRowIndex() % 2) ? Spry.Data.Region.evenRowClassName : Spry.Data.Region.oddRowClassName;
				row = null;
			}
			else if (fieldName == "ds_SortOrder")
			{
				resultStr += dsContext.getDataSet().getSortOrder();
				row = null;
			}
			else if (fieldName == "ds_SortColumn")
			{
				resultStr += dsContext.getDataSet().getSortColumn();
				row = null;
			}
			else
				row = processingContext.getCurrentRowForDataSet(dsName);
		}
		else
		{
			var ds = dsName ? dataSetsToUse[dsName] : dataSetsToUse[0];
			if (ds)
				row = ds.getCurrentRow();
		}

		if (row)
			resultStr += isJSExpr ? Spry.Utils.escapeQuotesAndLineBreaks("" + row[fieldName]) : row[fieldName];

		if (startSearchIndex == re.lastIndex)
		{
			// On IE if there was a match near the end of the string, it sometimes
			// leaves re.lastIndex pointing to the value it had before the last time
			// we called re.exec. We check for this case to prevent an infinite loop!
			// We need to write out any text in regionStr that comes after the last
			// match.

			var leftOverIndex = reArray.index + reArray[0].length;
			if (leftOverIndex < regionStr.length)
				resultStr += regionStr.substr(leftOverIndex);

			break;
		}

		startSearchIndex = re.lastIndex;
	}

	return resultStr;
};

Spry.Data.Region.strToDataSetsArray = function(str, returnRegionNames)
{
	var dataSetsArr = new Array;
	var foundHash = {};

	if (!str)
		return dataSetsArr;

	str = str.replace(/\s+/g, " ");
	str = str.replace(/^\s|\s$/g, "");
	var arr = str.split(/ /);


	for (var i = 0; i < arr.length; i++)
	{
		if (arr[i] && !Spry.Data.Region.PI.instructions[arr[i]])
		{
			try {
				var dataSet = Spry.Data.getDataSetByName(arr[i]);

				if (!foundHash[arr[i]])
				{
					if (returnRegionNames)
						dataSetsArr.push(arr[i]);
					else
						dataSetsArr.push(dataSet);
					foundHash[arr[i]] = true;
				}
			}
			catch (e) { /* Spry.Debug.trace("Caught exception: " + e + "\n"); */ }
		}
	}

	return dataSetsArr;
};

Spry.Data.Region.DSContext = function(dataSet, processingContext)
{
	var m_dataSet = dataSet;
	var m_processingContext = processingContext;
	var m_curRowIndexArray = [ { rowIndex: -1 } ]; // -1 means return whatever the current row is inside the data set.
	var m_parent = null;
	var m_children = [];

	// Private Methods:

	var getInternalRowIndex = function() { return m_curRowIndexArray[m_curRowIndexArray.length - 1].rowIndex; };

	// Public Methods:
	this.resetAll = function() { m_curRowIndexArray = [ { rowIndex: m_dataSet.getCurrentRow() } ] };
	this.getDataSet = function() { return m_dataSet; };
	this.getNumRows = function(unfiltered)
	{
		var data = this.getCurrentState().data;
		return data ? data.length : m_dataSet.getRowCount(unfiltered);
	};
	this.getData = function()
	{
		var data = this.getCurrentState().data;
		return data ? data : m_dataSet.getData();
	};
	this.setData = function(data)
	{
		this.getCurrentState().data = data;
	};
	this.getCurrentRow = function()
	{
		if (m_curRowIndexArray.length < 2 || getInternalRowIndex() < 0)
			return m_dataSet.getCurrentRow();

		var data = this.getData();
		var curRowIndex = getInternalRowIndex();

		if (curRowIndex < 0 || curRowIndex > data.length)
		{
			Spry.Debug.reportError("Invalid index used in Spry.Data.Region.DSContext.getCurrentRow()!\n");
			return null;
		}

		return data[curRowIndex];
	};
	this.getRowIndex = function()
	{
		var curRowIndex = getInternalRowIndex();
		if (curRowIndex >= 0)
			return curRowIndex;

		return m_dataSet.getRowNumber(m_dataSet.getCurrentRow());
	};
	this.setRowIndex = function(rowIndex)
	{
		this.getCurrentState().rowIndex = rowIndex;

		var data = this.getData();
		var numChildren = m_children.length;
		for (var i = 0; i < numChildren; i++)
			m_children[i].syncDataWithParentRow(this, rowIndex, data);
	};
	this.syncDataWithParentRow = function(parentDSContext, rowIndex, parentData)
	{
		var row = parentData[rowIndex];
		if (row)
		{
			nestedDS = m_dataSet.getNestedDataSetForParentRow(row);
			if (nestedDS)
			{
				var currentState = this.getCurrentState();
				currentState.data = nestedDS.getData();
				currentState.rowIndex = nestedDS.getCurrentRowNumber();

				var numChildren = m_children.length;
				for (var i = 0; i < numChildren; i++)
					m_children[i].syncDataWithParentRow(this, currentState.rowIndex, currentState.data);
			}
		}
	};
	this.pushState = function()
	{
		var curState = this.getCurrentState();
		var newState = new Object;
		newState.rowIndex = curState.rowIndex;
		newState.data = curState.data;

		m_curRowIndexArray.push(newState);

		var numChildren = m_children.length;
		for (var i = 0; i < numChildren; i++)
			m_children[i].pushState();
	};
	this.popState = function()
	{
		if (m_curRowIndexArray.length < 2)
		{
			// Our array should always have at least one element in it!
			Spry.Debug.reportError("Stack underflow in Spry.Data.Region.DSContext.popState()!\n");
			return;
		}

		var numChildren = m_children.length;
		for (var i = 0; i < numChildren; i++)
			m_children[i].popState();

		m_curRowIndexArray.pop();
	};
	this.getCurrentState = function()
	{
		return m_curRowIndexArray[m_curRowIndexArray.length - 1];
	};
	this.addChild = function(childDSContext)
	{
		var numChildren = m_children.length;
		for (var i = 0; i < numChildren; i++)
		{
			if (m_children[i] == childDSContext)
				return;
		}
		m_children.push(childDSContext);
	};
};

Spry.Data.Region.ProcessingContext = function(region)
{
	this.region = region;
	this.dataSetContexts = [];

	if (region && region.dataSets)
	{
		// Run through each data set in the list and check to see if we need
		// to add its parent to the list of data sets we track.
		var dsArray = region.dataSets.slice(0);
		var dsArrayLen = dsArray.length;
		for (var i = 0; i < dsArrayLen; i++)
		{
			var ds = region.dataSets[i];
			while (ds && ds.getParentDataSet)
			{
				var doesExist = false;
				ds = ds.getParentDataSet();
				if (ds && this.indexOf(dsArray, ds) == -1)
					dsArray.push(ds);
			}
		}

		// Create a data set context for every data set in our list.

		for (i = 0; i < dsArray.length; i++)
			this.dataSetContexts.push(new Spry.Data.Region.DSContext(dsArray[i], this));

		// Now run through the list of data set contexts and wire up the parent/child
		// relationships so that notifications get dispatched as expected.

		var dsContexts = this.dataSetContexts;
		var numDSContexts = dsContexts.length;

		for (i = 0; i < numDSContexts; i++)
		{
			var dsc = dsContexts[i];
			var ds = dsc.getDataSet();
			if (ds.getParentDataSet)
			{
				var parentDS = ds.getParentDataSet();
				if (parentDS)
				{
					var pdsc = this.getDataSetContext(parentDS);
					if (pdsc) pdsc.addChild(dsc);
				}
			}
		}
	}
};

Spry.Data.Region.ProcessingContext.prototype.indexOf = function(arr, item)
{
	// Given an array, return the index of item in that array
	// or -1 if it doesn't exist.

	if (arr)
	{
		var arrLen = arr.length;
		for (var i = 0; i < arrLen; i++)
			if (arr[i] == item)
				return i;
	}
	return -1;
};

Spry.Data.Region.ProcessingContext.prototype.getDataSetContext = function(dataSet)
{
	if (!dataSet)
	{
		// We were called without a specified data set or
		// data set name. Assume the caller wants the first
		// data set in the processing context.

		if (this.dataSetContexts.length > 0)
			return this.dataSetContexts[0];
		return null;
	}

	if (typeof dataSet == 'string')
	{
		dataSet = Spry.Data.getDataSetByName(dataSet);
		if (!dataSet)
			return null;
	}

	for (var i = 0; i < this.dataSetContexts.length; i++)
	{
		var dsc = this.dataSetContexts[i];
		if (dsc.getDataSet() == dataSet)
			return dsc;
	}

	return null;
};

Spry.Data.Region.ProcessingContext.prototype.getCurrentRowForDataSet = function(dataSet)
{
	var dsc = this.getDataSetContext(dataSet);
	if (dsc)
		return dsc.getCurrentRow();
	return null;
};

Spry.Data.Region.Token = function(tokenType, dataSet, data, regionStr)
{
	var self = this;
	this.tokenType = tokenType;
	this.dataSet = dataSet;
	this.data = data;
	this.regionStr = regionStr;
	this.parent = null;
	this.children = null;
};

Spry.Data.Region.Token.prototype.addChild = function(child)
{
	if (!child)
		return;

	if (!this.children)
		this.children = new Array;

	this.children.push(child);
	child.parent = this;
};

Spry.Data.Region.Token.LIST_TOKEN                   = 0;
Spry.Data.Region.Token.STRING_TOKEN                 = 1;
Spry.Data.Region.Token.PROCESSING_INSTRUCTION_TOKEN = 2;
Spry.Data.Region.Token.VALUE_TOKEN                  = 3;

Spry.Data.Region.Token.PIData = function(piName, data, jsExpr, regionState)
{
	var self = this;
	this.name = piName;
	this.data = data;
	this.jsExpr = jsExpr;
	this.regionState = regionState;
};

Spry.Utils.addLoadListener(function() { setTimeout(function() { if (Spry.Data.initRegionsOnLoad) Spry.Data.initRegions(); }, 0); });
