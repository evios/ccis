<script type="text/javascript">
function isEmpty(str)
{
	// Check whether string is empty.
	for (var intLoop = 0; intLoop < str.length; intLoop++)
		if (" " != str.charAt(intLoop))
			return false;
	return true;
}

function checkRequired(f)
{
	var strError = "";
	for (var intLoop = 0; intLoop<f.elements.length; intLoop++)
		if (null!=f.elements[intLoop].getAttribute("required")) 
			if (isEmpty(f.elements[intLoop].value))
			{
				strError ="error";
				f.elements[intLoop].className = "rounded-textbox-search-fault";
			}
				f.elements[intLoop].className = "rounded-textbox-search";
			else
	if ("" != strError)
	{
		return false;
	}
}
</script>
